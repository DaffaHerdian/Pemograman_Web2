<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pinjaman;
use App\Models\Anggota;

class PinjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pinjaman::with('anggota');
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        
        if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        
        $pinjaman = $query->latest('tanggal')->paginate(15);
        
        return view('transaksi.pinjaman', compact('pinjaman'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_anggota' => 'required|exists:anggota,id_anggota',
            'tanggal' => 'required|date',
            'jumlah_pinjaman' => 'required|numeric|min:0.01|max:10000000',
            'tenor' => 'required|integer|min:1',
            'angsuran_per_bulan' => 'required|numeric|min:0.01',
        ]);

        // Set initial values
        $validated['sisa_pinjaman'] = $validated['jumlah_pinjaman'];
        $validated['status'] = 'Berjalan';

        Pinjaman::create($validated);

        return redirect()->back()
            ->with('success', 'Pinjaman berhasil ditambahkan.');
    }

    public function show($id)
    {
        $pinjaman = Pinjaman::with(['anggota', 'angsuran'])->findOrFail($id);
        
        $totalDibayar = $pinjaman->angsuran->sum('jumlah_bayar');
        
        return view('transaksi.detail-pinjaman', compact('pinjaman', 'totalDibayar'));
    }
}
