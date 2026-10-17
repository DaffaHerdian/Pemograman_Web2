<?php

namespace App\Services;

use Brick\Math\BigDecimal as D;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class Finance
{
    public function fail(string $message): never
    {
        throw ValidationException::withMessages(['finance' => $message]);
    }

    public function balance(): string
    {
        return (string) D::of((string) DB::table('ledger_entries')->sum('amount'))->toScale(2);
    }

    public function rules(): array
    {
        $p = DB::table('financial_policies')->where('active', true)->orderByDesc('id')->first();

        return json_decode($p->rules, true, 512, JSON_THROW_ON_ERROR);
    }

    public function audit(int $actor, string $action, string $reference, array $summary = []): void
    {
        DB::table('activity_logs')->insert(['actor_id' => $actor, 'action' => $action, 'reference' => $reference, 'summary' => json_encode($summary), 'created_at' => now()]);
    }

    public function money(string $value, bool $whole = false): D
    {
        if (! preg_match($whole ? '/^\d{1,13}(\.00)?$/D' : '/^\d{1,13}(\.\d{1,2})?$/D', $value) || ! D::of((string) $value)->isPositive()) {
            $this->fail('Nominal tidak valid. Gunakan angka positif dengan presisi yang diperbolehkan.');
        }

        return D::of((string) $value)->toScale(2);
    }

    public function date(string $value): void
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) {
            $this->fail('Tanggal tidak valid.');
        }
        try {
            $d = CarbonImmutable::createFromFormat('!Y-m-d', $value, 'Asia/Jakarta');
        } catch (\Throwable) {
            $this->fail('Tanggal tidak valid.');
        }
        if (! $d || $d->format('Y-m-d') !== $value || $d->isAfter(CarbonImmutable::today('Asia/Jakarta'))) {
            $this->fail('Tanggal transaksi harus valid dan tidak di masa depan.');
        }
        $opening = DB::table('cash_book')->where('id', 1)->value('opening_date');
        if ($opening && $value < $opening) {
            $this->fail('Tanggal sebelum pembukaan buku kas tidak dapat diposting.');
        }
        $latest = DB::table('financial_transactions as t')->join('ledger_entries as e', 'e.transaction_id', '=', 't.id')->max('t.date');
        if ($latest && $value < $latest) {
            $this->fail('Tanggal sebelum mutasi kas terakhir tidak diperbolehkan agar saldo kronologis tetap konsisten.');
        }
    }

    private function ready(): void
    {
        if (! DB::table('cash_book')->where('id', 1)->value('reconciled')) {
            $this->fail('Verifikasi saldo kas pembukaan terlebih dahulu di Buku Kas. Saldo historis tidak ditebak.');
        }
    }

    private function member(int $id): object
    {
        $m = DB::table('anggota')->where('id_anggota', $id)->lockForUpdate()->first();
        if (! $m || $m->status !== 'Aktif') {
            $this->fail('Anggota harus berstatus Aktif.');
        }

        return $m;
    }

    private function enough(D $out): void
    {
        if (D::of((string) $this->balance())->isLessThan($out)) {
            $this->fail('Saldo kas tidak mencukupi.');
        }
    }

    private function run(string $kind, string $key, array $data, int $actor, callable $operation): object
    {
        if (! Str::isUuid($key)) {
            $this->fail('Kunci transaksi tidak valid. Muat ulang formulir.');
        }

        return DB::transaction(function () use ($kind, $key, $data, $actor, $operation) {
            DB::table('cash_book')->where('id', 1)->lockForUpdate()->firstOrFail();
            $hash = hash('sha256', json_encode([$kind, $actor, $data], JSON_UNESCAPED_SLASHES));
            if ($old = DB::table('financial_transactions')->where('idempotency_key', $key)->first()) {
                if (! hash_equals($old->request_hash, $hash)) {
                    $this->fail('Kunci yang sama telah digunakan untuk data transaksi berbeda.');
                }

                return $old;
            }
            if (! DB::table('users')->where('id', $actor)->exists()) {
                $this->fail('Admin tidak valid.');
            }
            $id = (string) Str::ulid();
            DB::table('financial_transactions')->insert(['id' => $id, 'number' => 'TRX-'.now('Asia/Jakarta')->format('Ymd').'-'.$id, 'idempotency_key' => $key, 'request_hash' => $hash, 'kind' => $kind, 'actor_id' => $actor, 'date' => $data['tanggal'] ?? now('Asia/Jakarta')->toDateString(), 'amount' => '0.00', 'created_at' => now(), 'updated_at' => now()]);
            [$amount,$cash,$member,$loan,$meta] = $operation($id);
            DB::table('financial_transactions')->where('id', $id)->update(['amount' => (string) $amount, 'member_id' => $member, 'loan_id' => $loan, 'meta' => json_encode($meta), 'reversal_of' => $meta['reversal_of'] ?? null, 'reason' => $data['reason'] ?? null]);
            if ($cash !== null) {
                DB::table('ledger_entries')->insert(['transaction_id' => $id, 'amount' => (string) $cash, 'created_at' => now(), 'updated_at' => now()]);
            }
            if ($cash !== null) {
                (new ReceiptService)->ensure($id);
            }
            $this->audit($actor, $kind, $id, ['amount' => (string) $amount]);

            return DB::table('financial_transactions')->where('id', $id)->first();
        }, 3);
    }

    public function opening(string $amount, string $date, string $reason, string $key, int $actor): object
    {
        return $this->run('opening', $key, ['amount' => $amount, 'tanggal' => $date, 'reason' => $reason], $actor, function () use ($amount, $date, $reason) {
            if (DB::table('cash_book')->where('id', 1)->value('reconciled')) {
                $this->fail('Saldo pembukaan sudah ditetapkan; gunakan koreksi beralasan.');
            }
            $this->date($date);
            if (mb_strlen(trim($reason)) < 10) {
                $this->fail('Alasan/sumber verifikasi kas minimal 10 karakter.');
            }
            if (! preg_match('/^\d{1,13}(\.\d{1,2})?$/D', $amount)) {
                $this->fail('Saldo pembukaan tidak valid.');
            }
            $v = D::of((string) $amount)->toScale(2);
            DB::table('cash_book')->where('id', 1)->update(['reconciled' => true, 'opening_date' => $date]);

            return [$v, $v, null, null, ['source' => 'Saldo kas terverifikasi, bukan hasil tebakan histori']];
        });
    }

    public function savings(array $d, int $actor, bool $withdraw = false): object
    {
        return $this->run($withdraw ? 'withdrawal' : 'deposit', $d['idempotency_key'], $d, $actor, function ($id) use ($d, $withdraw) {
            $this->ready();
            $this->date($d['tanggal']);
            $this->member((int) $d['id_anggota']);
            $v = $this->money((string) $d['jumlah']);
            $type = $withdraw ? 'Sukarela' : $d['jenis_simpanan'];
            if (! in_array($type, ['Pokok', 'Wajib', 'Sukarela'], true)) {
                $this->fail('Jenis simpanan tidak valid.');
            }
            if ($withdraw) {
                $saldo = DB::table('simpanan')->where('id_anggota', $d['id_anggota'])->where('jenis_simpanan', 'Sukarela')->sum('jumlah');
                if (D::of((string) $saldo)->isLessThan($v)) {
                    $this->fail('Saldo sukarela tidak mencukupi.');
                }$this->enough($v);
            }
            $signed = $withdraw ? $v->negated() : $v;
            $sid = DB::table('simpanan')->insertGetId(['id_anggota' => $d['id_anggota'], 'tanggal' => $d['tanggal'], 'jenis_simpanan' => $type, 'jumlah' => (string) $signed, 'transaction_id' => $id, 'created_at' => now(), 'updated_at' => now()]);

            return [$v, $signed, (int) $d['id_anggota'], null, ['savings_id' => $sid, 'type' => $type]];
        });
    }

    public function apply(array $d, int $actor): object
    {
        return $this->run('loan_application', $d['idempotency_key'], $d, $actor, function () use ($d) {
            $this->date($d['tanggal']);
            $this->member((int) $d['id_anggota']);
            $this->noActive((int) $d['id_anggota']);
            $policy = DB::table('financial_policies')->where('active', true)->orderByDesc('id')->first();
            $rules = json_decode($policy->rules, true, 512, JSON_THROW_ON_ERROR);
            $q = (new LoanCalculator)->quote((string) $d['jumlah_pinjaman'], (int) $d['tenor'], $d['tanggal'], $rules);
            $loan = DB::table('pinjaman')->insertGetId(['id_anggota' => $d['id_anggota'], 'tanggal' => $d['tanggal'], 'jumlah_pinjaman' => $q['principal'], 'tenor' => $d['tenor'], 'angsuran_per_bulan' => $q['monthly'], 'sisa_pinjaman' => '0', 'status' => 'Berjalan', 'workflow_status' => 'submitted', 'policy_id' => $policy->id, 'policy_snapshot' => json_encode($rules), 'tarif_jasa' => $rules['rate'], 'total_jasa' => $q['interest'], 'sisa_jasa' => '0', 'created_at' => now(), 'updated_at' => now()]);

            return [$q['principal'], null, (int) $d['id_anggota'], $loan, []];
        });
    }

    private function noActive(int $member, ?int $except = null): void
    {
        $q = DB::table('pinjaman')->where('id_anggota', $member)->where(function ($q) {
            $q->whereIn('workflow_status', ['submitted', 'approved', 'disbursed'])->orWhere(fn ($q) => $q->where('workflow_status', 'legacy')->where('status', 'Berjalan'));
        });
        if ($except) {
            $q->where('id_pinjaman', '!=', $except);
        }
        if ($q->exists()) {
            $this->fail('Anggota masih memiliki pinjaman/pengajuan aktif.');
        }
    }

    public function decision(int $loan, string $decision, string $reason, string $key, int $actor): object
    {
        return $this->run('loan_'.$decision, $key, ['loan' => $loan, 'reason' => $reason], $actor, function () use ($loan, $decision, $reason) {
            $l = DB::table('pinjaman')->where('id_pinjaman', $loan)->lockForUpdate()->firstOrFail();
            if ($l->workflow_status !== 'submitted' || ! in_array($decision, ['approve', 'reject'], true)) {
                $this->fail('Pengajuan tidak dapat diproses.');
            }
            if (mb_strlen(trim($reason)) < 5) {
                $this->fail('Alasan keputusan wajib diisi (minimal 5 karakter).');
            }
            $this->member($l->id_anggota);
            DB::table('pinjaman')->where('id_pinjaman', $loan)->update(['workflow_status' => $decision === 'approve' ? 'approved' : 'rejected', 'decision_reason' => $reason, 'updated_at' => now()]);

            return [$l->jumlah_pinjaman, null, $l->id_anggota, $loan, []];
        });
    }

    public function disburse(int $loan, string $date, string $key, int $actor): object
    {
        return $this->run('disbursement', $key, ['loan' => $loan, 'tanggal' => $date], $actor, function () use ($loan, $date) {
            $this->ready();
            $this->date($date);
            $l = DB::table('pinjaman')->where('id_pinjaman', $loan)->lockForUpdate()->firstOrFail();
            if ($l->workflow_status !== 'approved') {
                $this->fail('Hanya pengajuan disetujui yang dapat dicairkan.');
            }
            if ($date < $l->tanggal) {
                $this->fail('Tanggal pencairan sebelum pengajuan.');
            }
            $this->member($l->id_anggota);
            $this->noActive($l->id_anggota, $loan);
            $this->enough(D::of((string) $l->jumlah_pinjaman));
            $rules = json_decode($l->policy_snapshot, true, 512, JSON_THROW_ON_ERROR);
            $q = (new LoanCalculator)->quote((string) D::of((string) $l->jumlah_pinjaman)->toScale(0), $l->tenor, $date, $rules);
            foreach ($q['schedule'] as $s) {
                DB::table('loan_schedules')->insert(['loan_id' => $loan, 'number' => $s['number'], 'due_date' => $s['due_date'], 'principal' => $s['principal'], 'interest' => $s['interest']]);
            }
            DB::table('pinjaman')->where('id_pinjaman', $loan)->update(['tanggal' => $date, 'workflow_status' => 'disbursed', 'sisa_pinjaman' => $q['principal'], 'sisa_jasa' => $q['interest'], 'updated_at' => now()]);

            return [$q['principal'], D::of((string) $q['principal'])->negated(), $l->id_anggota, $loan, []];
        });
    }

    public function settlement(int $loan): array
    {
        $l = DB::table('pinjaman')->where('id_pinjaman', $loan)->firstOrFail();
        if (! in_array($l->workflow_status, ['legacy', 'disbursed'], true) || $l->status !== 'Berjalan') {
            $this->fail('Pinjaman tidak memiliki kewajiban aktif.');
        }

        return ['principal' => $l->sisa_pinjaman, 'interest' => $l->sisa_jasa, 'total' => (string) D::of((string) $l->sisa_pinjaman)->plus($l->sisa_jasa), 'policy' => $l->policy_snapshot ? json_decode($l->policy_snapshot, true) : ['label' => 'Legacy: saldo pokok historis, tidak ditambah bunga baru']];
    }

    public function pay(array $d, int $actor, bool $settlement = false): object
    {
        return $this->run($settlement ? 'settlement' : 'repayment', $d['idempotency_key'], $d, $actor, function ($id) use ($d, $settlement) {
            $this->ready();
            $this->date($d['tanggal']);
            $loan = (int) $d['id_pinjaman'];
            $l = DB::table('pinjaman')->where('id_pinjaman', $loan)->lockForUpdate()->firstOrFail();
            if (! in_array($l->workflow_status, ['legacy', 'disbursed'], true) || $l->status !== 'Berjalan') {
                $this->fail('Pinjaman belum dicairkan atau sudah lunas.');
            }
            if ($d['tanggal'] < $l->tanggal) {
                $this->fail('Pembayaran sebelum pencairan.');
            }
            $v = $this->money((string) $d['jumlah_bayar'], $l->workflow_status !== 'legacy');
            $remaining = D::of((string) $l->sisa_pinjaman)->plus($l->sisa_jasa);
            if ($v->isGreaterThan($remaining)) {
                $this->fail('Pembayaran melebihi sisa kewajiban.');
            }
            if ($settlement && ! $v->isEqualTo($remaining)) {
                $this->fail('Quotation pelunasan berubah. Tinjau ulang sebelum konfirmasi.');
            }
            $rest = $v;
            $paidP = D::of((string) 0);
            $paidI = D::of((string) 0);
            if ($l->workflow_status === 'legacy') {
                $paidP = $v;
                $rest = D::of((string) 0);
            } else {
                $rows = DB::table('loan_schedules')->where('loan_id', $loan)->orderBy('number')->lockForUpdate()->get();
                // Interest then principal within each oldest outstanding installment.
                foreach ($rows as $s) {
                    $i = D::min($rest, D::of((string) $s->interest)->minus($s->paid_interest));
                    $rest = $rest->minus($i);
                    $p = D::min($rest, D::of((string) $s->principal)->minus($s->paid_principal));
                    $rest = $rest->minus($p);
                    if ($i->isPositive() || $p->isPositive()) {
                        DB::table('payment_allocations')->insert(['transaction_id' => $id, 'schedule_id' => $s->id, 'principal' => (string) $p, 'interest' => (string) $i]);
                        DB::table('loan_schedules')->where('id', $s->id)->update(['paid_principal' => (string) D::of((string) $s->paid_principal)->plus($p), 'paid_interest' => (string) D::of((string) $s->paid_interest)->plus($i)]);
                        $paidP = $paidP->plus($p);
                        $paidI = $paidI->plus($i);
                    }
                    if ($rest->isZero()) {
                        break;
                    }
                }
            }
            if (! $rest->isZero()) {
                $this->fail('Jadwal tidak rekonsiliasi. Pembayaran dibatalkan.');
            }
            $newP = D::of((string) $l->sisa_pinjaman)->minus($paidP);
            $newI = D::of((string) $l->sisa_jasa)->minus($paidI);
            $closed = $newP->plus($newI)->isZero();
            // cash_book serializes all writers; ordinal is history sequence, never a schedule number.
            $ordinal = (int) DB::table('angsuran')->where('id_pinjaman', $loan)->max('angsuran_ke') + 1;
            $aid = DB::table('angsuran')->insertGetId(['id_pinjaman' => $loan, 'tanggal' => $d['tanggal'], 'angsuran_ke' => $ordinal, 'jumlah_bayar' => (string) $v, 'sisa_pinjaman' => (string) $newP, 'jasa_bayar' => (string) $paidI, 'sisa_jasa' => (string) $newI, 'transaction_id' => $id, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('pinjaman')->where('id_pinjaman', $loan)->update(['sisa_pinjaman' => (string) $newP, 'sisa_jasa' => (string) $newI, 'status' => $closed ? 'Lunas' : 'Berjalan', 'workflow_status' => $closed && $l->workflow_status !== 'legacy' ? 'closed' : $l->workflow_status, 'updated_at' => now()]);

            return [$v, $v, $l->id_anggota, $loan, ['payment_id' => $aid, 'principal' => (string) $paidP, 'interest' => (string) $paidI, 'legacy' => $l->workflow_status === 'legacy']];
        });
    }

    public function reverse(string $original, string $reason, string $date, string $key, int $actor): object
    {
        return $this->run('reversal', $key, ['original' => $original, 'reason' => $reason, 'tanggal' => $date], $actor, function ($id) use ($original, $reason, $date) {
            $this->ready();
            $this->date($date);
            if (mb_strlen(trim($reason)) < 10) {
                $this->fail('Alasan pembatalan minimal 10 karakter.');
            }
            $tx = DB::table('financial_transactions')->where('id', $original)->lockForUpdate()->firstOrFail();
            if ($date < $tx->date) {
                $this->fail('Pembatalan tidak boleh sebelum transaksi asal.');
            }
            if (DB::table('financial_transactions')->where('reversal_of', $original)->exists()) {
                $this->fail('Transaksi sudah dibatalkan.');
            }
            if (! in_array($tx->kind, ['opening', 'deposit', 'withdrawal', 'repayment', 'settlement', 'disbursement'], true)) {
                $this->fail('Jenis transaksi ini tidak dapat direversal.');
            }
            $cash = DB::table('ledger_entries')->where('transaction_id', $original)->value('amount');
            $opposite = D::of((string) $cash)->negated();
            if ($opposite->isNegative()) {
                $this->enough($opposite->negated());
            }
            $meta = json_decode($tx->meta, true);
            if ($tx->kind === 'opening') {
                $used = DB::table('ledger_entries as e')->join('financial_transactions as t', 't.id', '=', 'e.transaction_id')->whereNotIn('t.kind', ['opening', 'reversal'])->exists();
                if ($used) {
                    $this->fail('Pembukaan telah dipakai transaksi lain. Koreksi harus direkonsiliasi sebelum mengubah pembukaan.');
                }
                DB::table('cash_book')->where('id', 1)->update(['reconciled' => false, 'opening_date' => null]);
            } elseif (in_array($tx->kind, ['deposit', 'withdrawal'], true)) {
                DB::table('anggota')->where('id_anggota', $tx->member_id)->lockForUpdate()->firstOrFail();
                $row = DB::table('simpanan')->where('transaction_id', $original)->firstOrFail();
                $saldo = DB::table('simpanan')->where('id_anggota', $tx->member_id)->where('jenis_simpanan', $row->jenis_simpanan)->sum('jumlah');
                if (D::of((string) $saldo)->plus($opposite)->isNegative()) {
                    $this->fail('Pembatalan membuat saldo simpanan negatif.');
                }
                DB::table('simpanan')->insert(['id_anggota' => $tx->member_id, 'tanggal' => $date, 'jenis_simpanan' => $row->jenis_simpanan, 'jumlah' => (string) $opposite, 'transaction_id' => $id, 'created_at' => now(), 'updated_at' => now()]);
            } elseif (in_array($tx->kind, ['repayment', 'settlement'], true)) {
                $l = DB::table('pinjaman')->where('id_pinjaman', $tx->loan_id)->lockForUpdate()->firstOrFail();
                if ($l->workflow_status === 'reversed') {
                    $this->fail('Pencairan telah dibatalkan.');
                }
                if ($l->status === 'Lunas') {
                    $this->noActive($l->id_anggota, $l->id_pinjaman);
                }
                foreach (DB::table('payment_allocations')->where('transaction_id', $original)->get() as $a) {
                    $s = DB::table('loan_schedules')->where('id', $a->schedule_id)->lockForUpdate()->firstOrFail();
                    DB::table('loan_schedules')->where('id', $s->id)->update(['paid_principal' => (string) D::of((string) $s->paid_principal)->minus($a->principal), 'paid_interest' => (string) D::of((string) $s->paid_interest)->minus($a->interest)]);
                }
                DB::table('pinjaman')->where('id_pinjaman', $l->id_pinjaman)->update(['sisa_pinjaman' => (string) D::of((string) $l->sisa_pinjaman)->plus($meta['principal']), 'sisa_jasa' => (string) D::of((string) $l->sisa_jasa)->plus($meta['interest']), 'status' => 'Berjalan', 'workflow_status' => $meta['legacy'] ? 'legacy' : 'disbursed', 'updated_at' => now()]);
            } else {
                $l = DB::table('pinjaman')->where('id_pinjaman', $tx->loan_id)->lockForUpdate()->firstOrFail();
                if (DB::table('angsuran')->where('id_pinjaman', $tx->loan_id)->exists()) {
                    $this->fail('Pencairan dengan histori pembayaran tidak dapat dibatalkan langsung. Gunakan proses koreksi yang direkonsiliasi.');
                }
                DB::table('pinjaman')->where('id_pinjaman', $tx->loan_id)->update(['workflow_status' => 'reversed', 'status' => 'Lunas', 'sisa_pinjaman' => '0', 'sisa_jasa' => '0', 'updated_at' => now()]);
            }

            return [$tx->amount, $opposite, $tx->member_id, $tx->loan_id, ['reversal_of' => $original]];
        });
    }
}
