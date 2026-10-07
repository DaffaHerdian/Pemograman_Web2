<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = Anggota::query();
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('id_anggota', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        $anggota = $query->latest()->paginate(10);
        
        return view('anggota.index', compact('anggota'));
    }

    public function create()
    {
        return view('anggota.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|unique:anggota,nik|max:16',
            'telepon' => 'required|string|max:15',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'tanggal_bergabung' => 'required|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        Anggota::create($validated);

        return redirect()->route('anggota.index')
            ->with('success', 'Data anggota berhasil ditambahkan.');
    }

    public function show($id)
    {
        $anggota = Anggota::with(['simpanan', 'pinjaman.angsuran'])->findOrFail($id);
        
        // Calculate total simpanan
        $totalSimpanan = $anggota->simpanan->sum('jumlah');
        
        // Calculate total pinjaman
        $totalPinjaman = $anggota->pinjaman->sum('jumlah_pinjaman');
        $sisaPinjaman = $anggota->pinjaman->where('status', 'Berjalan')->sum('sisa_pinjaman');
        
        return view('anggota.show', compact('anggota', 'totalSimpanan', 'totalPinjaman', 'sisaPinjaman'));
    }

    public function edit($id)
    {
        $anggota = Anggota::findOrFail($id);
        return view('anggota.edit', compact('anggota'));
    }

    public function update(Request $request, $id)
    {
        $anggota = Anggota::findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nik' => 'required|string|max:16|unique:anggota,nik,' . $id . ',id_anggota',
            'telepon' => 'required|string|max:15',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'tanggal_bergabung' => 'required|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $anggota->update($validated);

        return redirect()->route('anggota.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $anggota = Anggota::findOrFail($id);
        
        // Check if anggota has related transactions
        if ($anggota->simpanan()->count() > 0 || $anggota->pinjaman()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus anggota yang memiliki riwayat transaksi. Ubah status menjadi "Tidak Aktif" sebagai gantinya.');
        }
        
        $anggota->delete();

        return redirect()->route('anggota.index')
            ->with('success', 'Data anggota berhasil dihapus.');
    }
}
