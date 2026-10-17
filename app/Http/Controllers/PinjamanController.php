<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialRequest;
use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Services\Finance;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
    public function index()
    {
        return redirect()->route('finance.loans');
    }

    public function create(Finance $finance)
    {
        return view('finance.loan-create', ['members' => Anggota::where('status', 'Aktif')->orderBy('nama')->get(), 'rules' => $finance->rules()]);
    }

    public function store(FinancialRequest $request, Finance $finance)
    {
        $finance->apply($request->validated(), $request->user()->id);

        return redirect()->route('finance.loans')->with('success', 'Pengajuan tersimpan. Tinjau dan setujui sebelum pencairan.');
    }

    public function show($id)
    {
        $pinjaman = Pinjaman::with(['anggota', 'angsuran'])->findOrFail($id);
        $reversed = DB::table('financial_transactions')->whereNotNull('reversal_of')->pluck('reversal_of');
        $total = BigDecimal::of(0);
        foreach ($pinjaman->angsuran as $payment) {
            $payment->setAttribute('dibatalkan', $payment->transaction_id && $reversed->contains($payment->transaction_id));
            if (! $payment->dibatalkan) {
                $total = $total->plus($payment->jumlah_bayar);
            }
        }
        $totalDibayar = (string) $total;

        return view('transaksi.detail-pinjaman', compact('pinjaman', 'totalDibayar'));
    }
}
