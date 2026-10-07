<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\Angsuran;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'anggota');
        $data = collect();
        
        switch ($jenis) {
            case 'anggota':
                $query = Anggota::query();
                if ($request->has('status') && $request->status != '') {
                    $query->where('status', $request->status);
                }
                $data = $query->get();
                break;
                
            case 'simpanan':
                $query = Simpanan::with('anggota');
                if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
                    $query->whereDate('tanggal', '>=', $request->tanggal_dari);
                }
                if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
                    $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
                }
                if ($request->has('jenis_simpanan') && $request->jenis_simpanan != '') {
                    $query->where('jenis_simpanan', $request->jenis_simpanan);
                }
                $data = $query->get();
                break;
                
            case 'pinjaman':
                $query = Pinjaman::with('anggota');
                if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
                    $query->whereDate('tanggal', '>=', $request->tanggal_dari);
                }
                if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
                    $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
                }
                if ($request->has('status') && $request->status != '') {
                    $query->where('status', $request->status);
                }
                $data = $query->get();
                break;
                
            case 'angsuran':
                $query = Angsuran::with(['pinjaman.anggota']);
                if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
                    $query->whereDate('tanggal', '>=', $request->tanggal_dari);
                }
                if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
                    $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
                }
                $data = $query->get();
                break;
        }
        
        return view('laporan.index', compact('jenis', 'data'));
    }
    
    public function print(Request $request)
    {
        $jenis = $request->get('jenis', 'anggota');
        $data = collect();
        
        switch ($jenis) {
            case 'anggota':
                $data = Anggota::all();
                break;
            case 'simpanan':
                $data = Simpanan::with('anggota')->get();
                break;
            case 'pinjaman':
                $data = Pinjaman::with('anggota')->get();
                break;
            case 'angsuran':
                $data = Angsuran::with(['pinjaman.anggota'])->get();
                break;
        }
        
        return view('laporan.print', compact('jenis', 'data'));
    }
}
