<?php

namespace Tests\Feature;

use App\Services\Finance;
use App\Services\LoanCalculator;
use Brick\Math\BigDecimal as D;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class FinanceTest extends TestCase
{
    use RefreshDatabase;

    private Finance $finance;

    private int $actor;

    private int $member;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-10-16 10:00:00');
        CarbonImmutable::setTestNow('2026-10-16 10:00:00');
        $this->finance = new Finance;
        $this->actor = DB::table('users')->insertGetId(['name' => 'QA', 'email' => 'qa@example.test', 'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT, ['cost' => 4])]);
        $this->member = DB::table('anggota')->insertGetId(['nama' => 'Fiktif', 'nik' => '0000000000000001', 'telepon' => '000', 'jenis_kelamin' => 'Perempuan', 'alamat' => 'Data uji', 'tanggal_bergabung' => '2026-01-01', 'status' => 'Aktif']);
    }

    private function key(): string
    {
        return (string) Str::uuid();
    }

    private function opening(string $amount = '10000000'): void
    {
        $this->finance->opening($amount, '2026-10-01', 'Hasil hitung kas uji', $this->key(), $this->actor);
    }

    private function apply(): object
    {
        return $this->finance->apply(['id_anggota' => $this->member, 'tanggal' => '2026-10-01', 'jumlah_pinjaman' => '6000000', 'tenor' => 12, 'idempotency_key' => $this->key()], $this->actor);
    }

    private function loan(): int
    {
        $this->opening();
        $tx = $this->apply();
        $this->finance->decision($tx->loan_id, 'approve', 'Disetujui QA', $this->key(), $this->actor);
        $this->finance->disburse($tx->loan_id, '2026-10-01', $this->key(), $this->actor);

        return $tx->loan_id;
    }

    private function payment(int $loan, string $amount, string $key = ''): array
    {
        return ['id_pinjaman' => $loan, 'tanggal' => '2026-10-16', 'jumlah_bayar' => $amount, 'idempotency_key' => $key ?: $this->key()];
    }

    public function test_application_has_no_cash_or_schedule_until_disbursed(): void
    {
        $this->opening();
        $tx = $this->apply();
        $this->assertSame('10000000.00', $this->finance->balance());
        $this->assertSame(0, DB::table('loan_schedules')->count());
        $this->assertSame('submitted', DB::table('pinjaman')->value('workflow_status'));
    }

    public function test_full_lifecycle_schedule_partial_and_early_settlement(): void
    {
        $id = $this->loan();
        $this->assertSame('4000000.00', $this->finance->balance());
        $this->assertSame(12, DB::table('loan_schedules')->count());
        $this->finance->pay($this->payment($id, '100000'), $this->actor);
        $a = DB::table('payment_allocations')->first();
        $this->assertTrue(D::of((string) $a->interest)->isEqualTo('60000'));
        $this->assertTrue(D::of((string) $a->principal)->isEqualTo('40000'));
        $q = $this->finance->settlement($id);
        $this->assertTrue(D::of((string) $q['total'])->isEqualTo('6620000'));
        $this->finance->pay($this->payment($id, '6620000'), $this->actor, true);
        $l = DB::table('pinjaman')->first();
        $this->assertSame('Lunas', $l->status);
        $this->assertTrue(D::of((string) $l->sisa_pinjaman)->plus($l->sisa_jasa)->isZero());
        $this->assertSame('10720000.00', $this->finance->balance());
        $sum = D::of((string) (string) DB::table('angsuran')->sum('jumlah_bayar'));
        $this->assertTrue($sum->isEqualTo('6720000'));
    }

    public function test_retry_posts_only_once_and_conflicting_retry_rejected(): void
    {
        $id = $this->loan();
        $d = $this->payment($id, '560000');
        $a = $this->finance->pay($d, $this->actor);
        $b = $this->finance->pay($d, $this->actor);
        $this->assertSame($a->id, $b->id);
        $this->assertSame(1, DB::table('angsuran')->count());
        $d['jumlah_bayar'] = '560001';
        $this->expectException(ValidationException::class);
        $this->finance->pay($d, $this->actor);
    }

    public function test_overpayment_rolls_back_every_effect(): void
    {
        $id = $this->loan();
        $before = DB::table('financial_transactions')->count();
        try {
            $this->finance->pay($this->payment($id, '6720001'), $this->actor);
            $this->fail('Overpayment accepted');
        } catch (ValidationException) {
        }
        $this->assertSame($before, DB::table('financial_transactions')->count());
        $this->assertSame(0, DB::table('angsuran')->count());
        $this->assertSame('4000000.00', $this->finance->balance());
    }

    public function test_reversal_preserves_history_restores_schedules_and_cash(): void
    {
        $id = $this->loan();
        $tx = $this->finance->pay($this->payment($id, '560000'), $this->actor);
        $r = $this->finance->reverse($tx->id, 'Koreksi input pembayaran', '2026-10-16', $this->key(), $this->actor);
        $this->assertSame($tx->id, $r->reversal_of);
        $this->assertSame(1, DB::table('angsuran')->count());
        $this->assertSame(1, DB::table('payment_allocations')->count());
        $this->assertTrue(D::of((string) DB::table('loan_schedules')->sum('paid_principal'))->isZero());
        $this->assertSame('4000000.00', $this->finance->balance());
        $this->assertTrue(D::of((string) $this->finance->settlement($id)['total'])->isEqualTo('6720000'));
        $this->expectException(ValidationException::class);
        $this->finance->reverse($tx->id, 'Pembatalan berulang uji', '2026-10-16', $this->key(), $this->actor);
    }

    public function test_inactive_member_rejected_by_server(): void
    {
        DB::table('anggota')->where('id_anggota', $this->member)->update(['status' => 'Tidak Aktif']);
        $this->expectException(ValidationException::class);
        $this->apply();
    }

    public function test_duplicate_active_loan_rejected(): void
    {
        $this->apply();
        $this->expectException(ValidationException::class);
        $this->apply();
    }

    public function test_disbursement_requires_approval_and_available_cash(): void
    {
        $this->opening('0');
        $tx = $this->apply();
        $this->finance->decision($tx->loan_id, 'approve', 'Disetujui QA', $this->key(), $this->actor);
        try {
            $this->finance->disburse($tx->loan_id, '2026-10-01', $this->key(), $this->actor);
            $this->fail('Cash overdraft');
        } catch (ValidationException) {
        }
        $this->assertSame('approved', DB::table('pinjaman')->value('workflow_status'));
        $this->assertSame(0, DB::table('loan_schedules')->count());
        $this->assertSame('0.00', $this->finance->balance());
    }

    public function test_savings_withdrawal_and_reversal_keep_both_ledgers_consistent(): void
    {
        $this->opening('0');
        $d = ['id_anggota' => $this->member, 'tanggal' => '2026-10-16', 'jenis_simpanan' => 'Sukarela', 'jumlah' => '1000.25', 'idempotency_key' => $this->key()];
        $deposit = $this->finance->savings($d, $this->actor);
        $this->finance->savings($d, $this->actor);
        $this->assertSame(1, DB::table('simpanan')->count());
        $d['jumlah'] = '100';
        $d['idempotency_key'] = $this->key();
        $withdraw = $this->finance->savings($d, $this->actor, true);
        $this->assertSame('900.25', $this->finance->balance());
        $this->finance->reverse($withdraw->id, 'Penarikan salah input', '2026-10-16', $this->key(), $this->actor);
        $this->assertSame('1000.25', $this->finance->balance());
        $this->assertTrue(D::of((string) DB::table('simpanan')->sum('jumlah'))->isEqualTo('1000.25'), (string) DB::table('simpanan')->sum('jumlah'));
    }

    public function test_policy_changes_do_not_change_contract_snapshot(): void
    {
        $this->opening();
        $tx = $this->apply();
        $rules = LoanCalculator::DEMO;
        $rules['rate'] = '2.00';
        DB::table('financial_policies')->insert(['label' => 'Policy next', 'rules' => json_encode($rules), 'active' => true]);
        $this->finance->decision($tx->loan_id, 'approve', 'Disetujui QA', $this->key(), $this->actor);
        $this->finance->disburse($tx->loan_id, '2026-10-01', $this->key(), $this->actor);
        $this->assertTrue(D::of((string) DB::table('loan_schedules')->sum('interest'))->isEqualTo('720000'));
    }

    public function test_legacy_does_not_acquire_new_interest_or_schedule(): void
    {
        $this->opening();
        $id = DB::table('pinjaman')->insertGetId(['id_anggota' => $this->member, 'tanggal' => '2026-01-01', 'jumlah_pinjaman' => '1000.50', 'tenor' => 2, 'angsuran_per_bulan' => '500.25', 'sisa_pinjaman' => '1000.50', 'status' => 'Berjalan']);
        $this->finance->pay($this->payment($id, '100.25'), $this->actor);
        $q = $this->finance->settlement($id);
        $this->assertTrue(D::of((string) $q['total'])->isEqualTo('900.25'), json_encode($q));
        $this->assertTrue(D::of((string) $q['interest'])->isZero());
        $this->assertSame(0, DB::table('loan_schedules')->count());
    }

    public function test_cash_cannot_be_used_before_explicit_opening(): void
    {
        $this->expectException(ValidationException::class);
        $this->finance->savings(['id_anggota' => $this->member, 'tanggal' => '2026-10-16', 'jenis_simpanan' => 'Pokok', 'jumlah' => '1000', 'idempotency_key' => $this->key()], $this->actor);
    }

    public function test_opening_can_be_reversed_before_operations_then_reconciled_again(): void
    {
        $this->opening('100');
        $tx = DB::table('financial_transactions')->where('kind', 'opening')->first();
        $this->finance->reverse($tx->id, 'Nominal pembukaan salah input', '2026-10-01', $this->key(), $this->actor);
        $this->assertFalse((bool) DB::table('cash_book')->value('reconciled'));
        $this->assertSame('0.00', $this->finance->balance());
        $this->opening('200');
        $this->assertSame('200.00', $this->finance->balance());
        $this->assertSame(3, DB::table('ledger_entries')->count());
    }

    public function test_settlement_with_decimal_zero_representation_is_valid(): void
    {
        $loan = $this->loan();
        $tx = $this->finance->pay($this->payment($loan, '6720000.00'), $this->actor, true);
        $this->assertSame('settlement', $tx->kind);
        $this->assertSame('Lunas', DB::table('pinjaman')->where('id_pinjaman', $loan)->value('status'));
    }

    public function test_reverse_disbursement_restores_cash_and_retains_schedule_history(): void
    {
        $loan = $this->loan();
        $tx = DB::table('financial_transactions')->where('kind', 'disbursement')->first();
        $this->finance->reverse($tx->id, 'Pencairan dibatalkan sebelum bayar', '2026-10-16', $this->key(), $this->actor);
        $this->assertSame('10000000.00', $this->finance->balance());
        $this->assertSame(12, DB::table('loan_schedules')->count());
        $this->assertSame('reversed', DB::table('pinjaman')->where('id_pinjaman', $loan)->value('workflow_status'));
    }

    public function test_failure_after_ledger_insert_rolls_back_all_posting_effects(): void
    {
        Schema::rename('receipts', 'receipts_unavailable');
        try {
            try {
                $this->opening('100');
                $this->fail('Posting should fail when receipt storage unavailable');
            } catch (QueryException) {
            }
            foreach (['ledger_entries', 'financial_transactions', 'activity_logs'] as $table) {
                $this->assertSame(0, DB::table($table)->count());
            }
            $this->assertFalse((bool) DB::table('cash_book')->value('reconciled'));
        } finally {
            Schema::rename('receipts_unavailable', 'receipts');
        }
    }
}
