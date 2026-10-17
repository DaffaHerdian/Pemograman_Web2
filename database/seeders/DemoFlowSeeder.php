<?php

namespace Database\Seeders;

use App\Models\Anggota;
use App\Models\User;
use App\Services\Finance;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use RuntimeException;

/** Fictional training data, posted through exactly the same service as admin forms. */
class DemoFlowSeeder extends Seeder
{
    private function key(string $name): string
    {
        return Uuid::uuid5(Uuid::NAMESPACE_URL, 'koperasiku-demo-flow-v1/'.$name)->toString();
    }

    public function run(): void
    {
        $connection = DB::connection();
        $file = str_replace('\\', '/', (string) $connection->getConfig('database'));
        $allowedMemory = app()->environment('testing') && $file === ':memory:';
        if (! app()->environment(['local', 'testing']) || $connection->getDriverName() !== 'sqlite' || (! $allowedMemory && $file !== str_replace('\\', '/', database_path('demo-flow.sqlite')))) {
            throw new RuntimeException('DemoFlowSeeder hanya untuk database latihan. Jalankan php artisan koperasi:demo dari restore-local. Database koperasiku tidak boleh diisi demo ini.');
        }
        if (DB::table('financial_transactions')->where('idempotency_key', $this->key('opening'))->exists()) {
            $this->command?->info('Data latihan sudah ada; tidak ditambahkan ulang atau direset.');

            return;
        }
        foreach (['users', 'anggota', 'simpanan', 'pinjaman', 'angsuran', 'financial_transactions', 'ledger_entries'] as $table) {
            if (DB::table($table)->exists()) {
                throw new RuntimeException('Database latihan harus kosong; tidak akan menimpa data yang sudah ada.');
            }
        }
        DB::transaction(function () {
            $finance = app(Finance::class);
            $today = CarbonImmutable::today('Asia/Jakarta');
            $start = $today->startOfMonth()->subMonthsNoOverflow(3);
            $day = $start->toDateString();
            $admin = User::create(['name' => 'DEMO Admin KoperasiKu', 'email' => 'admin@koperasiku.id', 'password' => 'demo-training-only', 'email_verified_at' => now()]);
            DB::table('cooperative_identity')->where('id', 1)->update(['name' => 'KoperasiKu — LATIHAN DATA FIKTIF', 'address' => 'Alamat fiktif untuk belajar alur aplikasi', 'telephone' => '']);
            $members = [];
            foreach (['Budi', 'Siti', 'Rina', 'Dewi', 'Adi', 'Mira', 'Eko', 'Agus'] as $n => $name) {
                $members[$name] = Anggota::create(['nama' => 'DEMO '.$name, 'nik' => str_pad((string) ($n + 1), 16, '0', STR_PAD_LEFT), 'telepon' => '000000000000', 'jenis_kelamin' => in_array($name, ['Siti', 'Rina', 'Dewi', 'Mira'], true) ? 'Perempuan' : 'Laki-laki', 'alamat' => 'Alamat fiktif — data latihan', 'tanggal_bergabung' => $day, 'status' => $name === 'Agus' ? 'Tidak Aktif' : 'Aktif'])->getKey();
            }
            $finance->opening('20000000', $day, 'Dana pembukaan fiktif Rp20 juta untuk latihan, bukan kas sebenarnya', $this->key('opening'), $admin->id);
            foreach ($members as $name => $id) {
                if ($name === 'Agus') {
                    continue;
                }
                foreach (['Pokok' => '100000', 'Wajib' => '50000', 'Sukarela' => $name === 'Budi' ? '1000000' : '500000'] as $type => $amount) {
                    $finance->savings(['id_anggota' => $id, 'tanggal' => $day, 'jenis_simpanan' => $type, 'jumlah' => $amount, 'idempotency_key' => $this->key('savings-'.$name.'-'.$type)], $admin->id);
                }
            }
            $loans = [];
            foreach (['Budi' => ['6000000', 12], 'Siti' => ['3000000', 6], 'Rina' => ['2000000', 6], 'Dewi' => ['1000000', 4]] as $name => [$principal,$months]) {
                $tx = $finance->apply(['id_anggota' => $members[$name], 'tanggal' => $day, 'jumlah_pinjaman' => $principal, 'tenor' => $months, 'idempotency_key' => $this->key('apply-'.$name)], $admin->id);
                $loans[$name] = $tx->loan_id;
                $finance->decision($tx->loan_id, 'approve', 'Persetujuan fiktif untuk latihan', $this->key('approve-'.$name), $admin->id);
            }
            foreach (['Budi', 'Siti'] as $name) {
                $finance->disburse($loans[$name], $day, $this->key('disburse-'.$name), $admin->id);
            }
            $firstDue = $start->addMonthNoOverflow();
            $finance->pay(['id_pinjaman' => $loans['Budi'], 'tanggal' => $firstDue->toDateString(), 'jumlah_bayar' => '560000', 'idempotency_key' => $this->key('budi-payment-1')], $admin->id);
            $finance->savings(['id_anggota' => $members['Siti'], 'tanggal' => $firstDue->addDay()->toDateString(), 'jumlah' => '100000', 'idempotency_key' => $this->key('siti-withdrawal')], $admin->id, true);
            $finance->pay(['id_pinjaman' => $loans['Budi'], 'tanggal' => $firstDue->addDays(14)->toDateString(), 'jumlah_bayar' => '100000', 'idempotency_key' => $this->key('budi-payment-partial')], $admin->id);
            $quote = $finance->settlement($loans['Siti']);
            $finance->pay(['id_pinjaman' => $loans['Siti'], 'tanggal' => $firstDue->addDays(19)->toDateString(), 'jumlah_bayar' => $quote['total'], 'idempotency_key' => $this->key('siti-settlement')], $admin->id, true);
            // Find a real calendar date whose first installment falls in the next seven days.
            $soonDate = null;
            for ($offset = 40; $offset >= 1; $offset--) {
                $candidate = $today->subDays($offset);
                $due = $candidate->addMonthNoOverflow();
                if ($due->gt($today) && $due->lte($today->addDays(7))) {
                    $soonDate = $candidate;
                    break;
                }
            }
            if (! $soonDate) {
                throw new RuntimeException('Tanggal latihan jatuh tempo tidak ditemukan.');
            }
            $finance->disburse($loans['Rina'], $soonDate->toDateString(), $this->key('disburse-Rina'), $admin->id);
            $finance->disburse($loans['Dewi'], $today->toDateString(), $this->key('disburse-Dewi'), $admin->id);
            foreach (['Adi' => ['1000000', 6, null], 'Mira' => ['1500000', 6, 'approve'], 'Eko' => ['750000', 3, 'reject']] as $name => [$principal,$months,$decision]) {
                $tx = $finance->apply(['id_anggota' => $members[$name], 'tanggal' => $today->toDateString(), 'jumlah_pinjaman' => $principal, 'tenor' => $months, 'idempotency_key' => $this->key('apply-'.$name)], $admin->id);
                if ($decision) {
                    $finance->decision($tx->loan_id, $decision, 'Contoh keputusan pengajuan untuk latihan', $this->key('decision-'.$name), $admin->id);
                }
            }
            $mistake = $finance->savings(['id_anggota' => $members['Eko'], 'tanggal' => $today->toDateString(), 'jenis_simpanan' => 'Sukarela', 'jumlah' => '50000', 'idempotency_key' => $this->key('eko-mistake')], $admin->id);
            $finance->reverse($mistake->id, 'Contoh salah input: dibalik tanpa menghapus histori', $today->toDateString(), $this->key('eko-reversal'), $admin->id);
            $this->command?->info('8 anggota, 7 skenario pinjaman, simpanan, penarikan, pembayaran parsial, pelunasan dan reversal siap. Saldo kas latihan: Rp16.790.000.');
        });
    }
}
