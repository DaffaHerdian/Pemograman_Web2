<?php

namespace App;

use App\Models\Simpanan;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use stdClass;

class JasaSimpanan
{
    public function pengaturan(bool $lock = false): stdClass
    {
        $query = DB::table('pengaturan_koperasi')->where('id', 1);

        return ($lock ? $query->lockForUpdate() : $query)->firstOrFail();
    }

    public function periodeBerikutnya(stdClass $pengaturan): string
    {
        return $pengaturan->periode_terakhir
            ? CarbonImmutable::parse($pengaturan->periode_terakhir.'-01')->addMonth()->format('Y-m')
            : $pengaturan->periode_mulai;
    }

    public function validasiTanggal(string $tanggal, stdClass $pengaturan, string $bag = 'default'): void
    {
        if ($pengaturan->periode_terakhir && substr($tanggal, 0, 7) <= $pengaturan->periode_terakhir) {
            throw ValidationException::withMessages([
                'tanggal' => 'Periode ini sudah ditutup. Gunakan tanggal setelah periode pembukuan terakhir.',
            ])->errorBag($bag);
        }
    }

    /** @return array{periode: string, tarif: string, bisa_dibukukan: bool, total: string, anggota: array<int, string>} */
    public function ringkasan(stdClass $pengaturan): array
    {
        $periode = $this->periodeBerikutnya($pengaturan);
        $awal = CarbonImmutable::parse($periode.'-01', 'Asia/Jakarta');
        $akhir = $awal->endOfMonth();
        $hariIni = CarbonImmutable::now('Asia/Jakarta');
        $batas = $akhir->min($hariIni);
        $transaksi = Simpanan::where('jenis_simpanan', 'Sukarela')
            ->whereDate('tanggal', '<=', $batas->toDateString())
            ->whereNull('periode_jasa')->get();
        $jasaLalu = Simpanan::where('jenis_simpanan', 'Sukarela')->where('periode_jasa', '<', $periode)->get();
        $hasil = [];
        $total = BigDecimal::of('0.00');

        foreach ($transaksi->concat($jasaLalu)->groupBy('id_anggota') as $idAnggota => $riwayat) {
            $saldo = BigDecimal::of('0');
            $saldoHarian = BigDecimal::of('0');
            $mutasi = [];
            foreach ($riwayat as $item) {
                $tanggal = $item->tanggal->format('Y-m-d');
                if ($tanggal < $awal->toDateString()) {
                    $saldo = $saldo->plus($item->jumlah);
                } else {
                    $mutasi[$tanggal] = ($mutasi[$tanggal] ?? BigDecimal::of('0'))->plus($item->jumlah);
                }
            }

            for ($hari = $awal; $hari->toDateString() <= $batas->toDateString(); $hari = $hari->addDay()) {
                $saldo = $saldo->plus($mutasi[$hari->toDateString()] ?? '0');
                $saldoHarian = $saldoHarian->plus($saldo->isPositive() ? $saldo : '0');
            }

            $jasa = $saldoHarian->multipliedBy((string) $pengaturan->jasa_simpanan_persen)
                ->dividedBy($awal->daysInMonth * 100, 2, RoundingMode::HalfUp);
            $hasil[$idAnggota] = (string) $jasa;
            $total = $total->plus($jasa);
        }

        return [
            'periode' => $periode, 'tarif' => (string) $pengaturan->jasa_simpanan_persen,
            'bisa_dibukukan' => $akhir->lt($hariIni), 'total' => (string) $total, 'anggota' => $hasil,
        ];
    }

    public function bukukan(string $periode): void
    {
        DB::transaction(function () use ($periode): void {
            $pengaturan = $this->pengaturan(true);
            if (! ($pengaturan->jasa_simpanan_enabled ?? false)) {
                throw ValidationException::withMessages(['periode' => 'Aktivasi dan tarif jasa simpanan belum disetujui.']);
            }
            if ($periode !== $this->periodeBerikutnya($pengaturan)) {
                throw ValidationException::withMessages(['periode' => 'Periode sudah dibukukan atau belum sesuai urutan.']);
            }
            $ringkasan = $this->ringkasan($pengaturan);
            if (! $ringkasan['bisa_dibukukan']) {
                throw ValidationException::withMessages(['periode' => 'Jasa baru dapat dibukukan setelah bulan berakhir.']);
            }

            foreach ($ringkasan['anggota'] as $idAnggota => $jumlah) {
                if (BigDecimal::of($jumlah)->isPositive()) {
                    Simpanan::create([
                        'id_anggota' => $idAnggota,
                        'tanggal' => CarbonImmutable::parse($periode.'-01')->endOfMonth()->toDateString(),
                        'jenis_simpanan' => 'Sukarela', 'jumlah' => $jumlah,
                        'periode_jasa' => $periode, 'tarif_jasa' => $ringkasan['tarif'],
                    ]);
                }
            }
            DB::table('pengaturan_koperasi')->where('id', 1)->update(['periode_terakhir' => $periode]);
        });
    }
}
