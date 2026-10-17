<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Services\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AnggotaController extends Controller
{
    public function index(Request $request)
    {
        $query = Anggota::query();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('id_anggota', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $anggota = $query->orderBy('id_anggota', 'asc')->paginate(10);

        return view('anggota.index', compact('anggota'));
    }

    public function create()
    {
        return view('anggota.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateMember($request);

        DB::transaction(function () use ($validated, $request) {
            $anggota = Anggota::create($validated);
            app(Finance::class)->audit($request->user()->id, 'member_create', (string) $anggota->getKey());
        });

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

        $validated = $this->validateMember($request, $anggota);

        DB::transaction(function () use ($validated, $request, $id) {
            DB::table('cash_book')->where('id', 1)->lockForUpdate()->firstOrFail();
            $anggota = Anggota::whereKey($id)->lockForUpdate()->firstOrFail();
            $anggota->update($validated);
            app(Finance::class)->audit($request->user()->id, 'member_update', (string) $id, ['fields' => array_keys($validated)]);
        });

        return redirect()->route('anggota.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    private function validateMember(Request $request, ?Anggota $anggota = null): array
    {
        $uniqueNik = Rule::unique('anggota', 'nik');
        if ($anggota !== null) {
            $uniqueNik->ignore($anggota->getKey(), 'id_anggota');
        }

        return $request->validate([
            'nama' => ['bail', 'required', 'string', 'max:255', 'regex:~\A\p{L}[\p{L}\p{M} .\x{27}\x{2019}\-]*\z~u'],
            'nik' => ['bail', 'required', 'string', 'regex:/\A[0-9]{16}\z/', $uniqueNik],
            'telepon' => ['bail', 'required', 'string', 'max:15', 'regex:/\A[0-9]+\z/'],
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'tanggal_bergabung' => 'required|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ], [
            'nama.regex' => 'Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung; tidak boleh angka.',
            'nik.regex' => 'NIK harus terdiri dari tepat 16 digit angka.',
            'nik.unique' => 'NIK sudah digunakan oleh anggota lain.',
            'telepon.regex' => 'Nomor telepon hanya boleh berisi angka, tanpa spasi atau tanda tambah.',
            'telepon.max' => 'Nomor telepon maksimal 15 digit angka.',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            DB::table('cash_book')->where('id', 1)->lockForUpdate()->firstOrFail();
            $anggota = Anggota::whereKey($id)->lockForUpdate()->firstOrFail();
            if ($anggota->simpanan()->exists() || $anggota->pinjaman()->exists()) {
                return back()->with('error', 'Anggota memiliki histori. Gunakan status Tidak Aktif.');
            }
            $anggota->delete();
            app(Finance::class)->audit($request->user()->id, 'member_delete', (string) $id);

            return redirect()->route('anggota.index')->with('success', 'Anggota tanpa histori berhasil dihapus.');
        });
    }
}
