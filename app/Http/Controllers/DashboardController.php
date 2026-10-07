<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Angsuran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAnggota = Anggota::count();
        $totalSimpanan = Simpanan::sum('jumlah');
        $totalPinjaman = Pinjaman::sum('jumlah_pinjaman');
        $totalPinjamanBerjalan = Pinjaman::where('status', 'Berjalan')->sum('sisa_pinjaman');
        $totalPinjamanLunas = Pinjaman::where('status', 'Lunas')->count();
        $pinjamanBerjalan = Pinjaman::where('status', 'Berjalan')->count();
        
        // Angsuran bulan ini
        $angsuranBulanIni = Angsuran::whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->sum('jumlah_bayar');
        
        // Transaksi terbaru (gabungan simpanan, pinjaman, angsuran)
        $transaksiTerbaru = collect();
        
        $simpananTerbaru = Simpanan::with('anggota')
            ->latest('tanggal')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'jenis' => 'Simpanan',
                    'anggota' => $item->anggota->nama,
                    'jumlah' => $item->jumlah,
                    'tanggal' => $item->tanggal,
                    'keterangan' => 'Simpanan ' . $item->jenis_simpanan,
                ];
            });
        
        $pinjamanTerbaru = Pinjaman::with('anggota')
            ->latest('tanggal')
            ->take(5)
            ->get()
            ->map(function($item) {
                return [
                    'jenis' => 'Pinjaman',
                    'anggota' => $item->anggota->nama,
                    'jumlah' => $item->jumlah_pinjaman,
                    'tanggal' => $item->tanggal,
                    'keterangan' => 'Pinjaman ' . $item->status,
                ];
            });
        
        $transaksiTerbaru = $transaksiTerbaru->concat($simpananTerbaru)
            ->concat($pinjamanTerbaru)
            ->sortByDesc('tanggal')
            ->take(10);
        
        return view('dashboard.index', compact(
            'totalAnggota',
            'totalSimpanan',
            'totalPinjaman',
            'totalPinjamanBerjalan',
            'totalPinjamanLunas',
            'pinjamanBerjalan',
            'angsuranBulanIni',
            'transaksiTerbaru'
        ));
    }
}
