<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use Illuminate\Support\Facades\DB;

class AngsuranController extends Controller
{
    public function index(Request $request)
    {
        $query = Angsuran::with(['pinjaman.anggota']);
        
        // Filter by date range
        if ($request->has('tanggal_dari') && $request->tanggal_dari != '') {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        
        if ($request->has('tanggal_sampai') && $request->tanggal_sampai != '') {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        
        $angsuran = $query->latest('tanggal')->paginate(15);
        
        return view('transaksi.angsuran', compact('angsuran'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pinjaman' => 'required|exists:pinjaman,id_pinjaman',
            'tanggal' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0.01',
        ]);

        // Get pinjaman
        $pinjaman = Pinjaman::findOrFail($validated['id_pinjaman']);
        
        // Validate pinjaman status
        if ($pinjaman->status == 'Lunas') {
            return back()->withErrors(['id_pinjaman' => 'Pinjaman sudah lunas.']);
        }
        
        // Validate jumlah bayar tidak melebihi sisa
        if ($validated['jumlah_bayar'] > $pinjaman->sisa_pinjaman) {
            return back()->withErrors(['jumlah_bayar' => 'Jumlah pembayaran melebihi sisa pinjaman.']);
        }

        DB::transaction(function() use ($validated, $pinjaman) {
            // Get next angsuran number
            $angsuranKe = $pinjaman->angsuran()->count() + 1;
            
            // Calculate new sisa
            $sisaBaru = $pinjaman->sisa_pinjaman - $validated['jumlah_bayar'];
            
            // Create angsuran
            Angsuran::create([
                'id_pinjaman' => $validated['id_pinjaman'],
                'tanggal' => $validated['tanggal'],
                'angsuran_ke' => $angsuranKe,
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'sisa_pinjaman' => $sisaBaru,
            ]);
            
            // Update pinjaman
            $pinjaman->sisa_pinjaman = $sisaBaru;
            
            // Check if lunas
            if ($sisaBaru <= 0) {
                $pinjaman->sisa_pinjaman = 0;
                $pinjaman->status = 'Lunas';
            }
            
            $pinjaman->save();
        });

        return redirect()->back()
            ->with('success', 'Angsuran berhasil ditambahkan.');
    }
}
