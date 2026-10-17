<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialRequest;
use App\Models\Anggota;
use App\Models\Angsuran;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Services\Finance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SimpananController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->query('tab') === 'pinjaman') {
            return redirect()->route('finance.loans');
        }
        if ($request->routeIs('simpanan.index') && $request->query('tab') === 'angsuran') {
            return redirect()->route('angsuran.index');
        }
        $page = $request->routeIs('angsuran.index') ? 'angsuran' : 'simpanan';
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

        $simpanan = $query->orderByDesc('tanggal')->orderByDesc('id_simpanan')
            ->paginate(10, ['*'], 'simpanan_page');
        $saldoAnggota = Anggota::withSum('simpanan as saldo_total', 'jumlah')
            ->withSum(['simpanan as saldo_sukarela' => fn ($query) => $query->where('jenis_simpanan', 'Sukarela')], 'jumlah')
            ->orderBy('id_anggota')->paginate(10, ['*'], 'saldo_page');
        $anggotaPenarikan = Anggota::withSum([
            'simpanan as saldo_sukarela' => fn ($query) => $query->where('jenis_simpanan', 'Sukarela'),
        ], 'jumlah')->orderBy('id_anggota')->get()->filter(fn (Anggota $anggota): bool => $anggota->saldo_sukarela > 0);

        $angsuran = Angsuran::with('pinjaman.anggota')->latest()->paginate(10, ['*'], 'angsuran_page');
        $reversed = DB::table('financial_transactions')->whereNotNull('reversal_of')->pluck('reversal_of');
        foreach ($angsuran as $payment) {
            $payment->setAttribute('dibatalkan', $payment->transaction_id && $reversed->contains($payment->transaction_id));
        }
        $anggotaAktif = Anggota::where('status', 'Aktif')->orderBy('id_anggota')->get();
        $pinjamanBayar = Pinjaman::with('anggota')->where('status', 'Berjalan')->whereIn('workflow_status', ['legacy', 'disbursed'])->get();

        return view('transaksi.simpanan', compact('page', 'simpanan', 'saldoAnggota', 'anggotaPenarikan', 'angsuran', 'anggotaAktif', 'pinjamanBayar'));
    }

    public function store(FinancialRequest $request, Finance $finance): RedirectResponse
    {
        $finance->savings($request->validated(), $request->user()->id);

        return back()->with('success', 'Setoran tersimpan dengan nomor transaksi dan buku kas.');
    }

    public function withdraw(FinancialRequest $request, Finance $finance): RedirectResponse
    {
        $finance->savings($request->validated(), $request->user()->id, true);

        return redirect()->route('simpanan.index', ['tab' => 'simpanan'])->with('success', 'Penarikan tercatat pada simpanan dan kas.');
    }
}
