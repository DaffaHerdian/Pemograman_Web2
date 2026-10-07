<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simpanan;
use App\Models\Anggota;

class SimpananController extends Controller
{
    public function index(Request $request)
    {
        $query = Simpanan::with('anggota');
        
        // Filter by jenis simpanan
        if ($request->has('jenis') && $request->jenis != '') {
            $query->where('jenis_simpanan', $request->jenis);
        }
        
        // Filter by date range
        if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        
        if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        
        $simpanan = $query->latest('tanggal')->paginate(15);
        
        return view('transaksi.simpanan', compact('simpanan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_anggota' => 'required|exists:anggota,id_anggota',
            'tanggal' => 'required|date',
            'jenis_simpanan' => 'required|in:Pokok,Wajib,Sukarela',
            'jumlah' => 'required|numeric|min:0.01',
        ]);

        Simpanan::create($validated);

        return redirect()->back()
            ->with('success', 'Simpanan berhasil ditambahkan.');
    }
}
