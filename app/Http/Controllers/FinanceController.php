<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialRequest;
use App\Services\Finance;
use App\Services\LoanCalculator;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $r, Finance $finance)
    {
        $q = DB::table('financial_transactions as t')->leftJoin('users as u', 'u.id', '=', 't.actor_id')->select('t.*', 'u.name as actor')->selectSub(DB::table('financial_transactions as rev')->select('rev.id')->whereColumn('rev.reversal_of', 't.id')->limit(1), 'reversed_by')->orderByDesc('t.created_at')->orderByDesc('t.id');
        if ($r->filled('from')) {
            $q->whereDate('t.date', '>=', $r->validate(['from' => 'date_format:Y-m-d'])['from']);
        }
        if ($r->filled('to')) {
            $q->whereDate('t.date', '<=', $r->validate(['to' => 'date_format:Y-m-d'])['to']);
        }

        return view('finance.index', ['transactions' => $q->paginate(20)->withQueryString(), 'balance' => $finance->balance(), 'book' => DB::table('cash_book')->first()]);
    }

    public function simulator(Request $r, Finance $finance)
    {
        return view('finance.simulator', ['rules' => $finance->rules()]);
    }

    public function calculate(Request $r, Finance $finance, LoanCalculator $calculator)
    {
        $d = $r->validate(['jumlah_pinjaman' => 'required|regex:/^\d{1,8}$/', 'tenor' => 'required|integer|min:1|max:36', 'tanggal' => 'required|date_format:Y-m-d']);

        return response()->json($calculator->quote($d['jumlah_pinjaman'], (int) $d['tenor'], $d['tanggal'], $finance->rules()));
    }

    public function opening(FinancialRequest $r, Finance $finance)
    {
        $d = $r->validated();
        $finance->opening($d['amount'], $d['tanggal'], $d['reason'], $d['idempotency_key'], $r->user()->id);

        return back()->with('success', 'Kas pembukaan terverifikasi dan tercatat sebagai transaksi.');
    }

    public function loans()
    {
        return view('finance.loans', ['loans' => DB::table('pinjaman as p')->join('anggota as a', 'a.id_anggota', '=', 'p.id_anggota')->select('p.*', 'a.nama')->orderByDesc('p.id_pinjaman')->paginate(20)]);
    }

    public function decision(Request $r, int $loan, Finance $finance)
    {
        $d = $r->validate(['decision' => 'required|in:approve,reject', 'reason' => 'required|string|min:5|max:2000', 'idempotency_key' => 'required|uuid', 'confirm' => 'accepted']);
        $finance->decision($loan, $d['decision'], $d['reason'], $d['idempotency_key'], $r->user()->id);

        return back()->with('success', 'Keputusan pengajuan tercatat.');
    }

    public function disburse(FinancialRequest $r, int $loan, Finance $finance)
    {
        $d = $r->validated();
        $r->validate(['confirm' => 'accepted']);
        $finance->disburse($loan, $d['tanggal'], $d['idempotency_key'], $r->user()->id);

        return back()->with('success', 'Pinjaman dicairkan. Jadwal dan kas keluar tercatat.');
    }

    public function monitor(Request $r)
    {
        $r->validate(['status' => 'nullable|in:upcoming,soon,overdue,paid', 'member' => 'nullable|integer', 'as_of' => 'nullable|date_format:Y-m-d']);
        $today = $r->input('as_of', now('Asia/Jakarta')->toDateString());
        $soon = CarbonImmutable::parse($today)->addDays(7)->toDateString();
        $q = DB::table('loan_schedules as s')->join('pinjaman as p', 'p.id_pinjaman', '=', 's.loan_id')->join('anggota as a', 'a.id_anggota', '=', 'p.id_anggota')->whereIn('p.workflow_status', ['disbursed', 'closed'])->select('s.*', 'a.nama', 'a.id_anggota');
        if ($r->filled('member')) {
            $q->where('a.id_anggota', $r->member);
        }
        $q->selectRaw("CASE WHEN s.principal + s.interest - s.paid_principal - s.paid_interest = 0 THEN 'paid' WHEN s.due_date < ? THEN 'overdue' WHEN s.due_date <= ? THEN 'soon' ELSE 'upcoming' END as due_status", [$today, $soon]);
        if ($r->filled('status')) {
            if ($r->status === 'paid') {
                $q->whereRaw('s.principal + s.interest = s.paid_principal + s.paid_interest');
            } else {
                $q->whereRaw('s.principal + s.interest > s.paid_principal + s.paid_interest');
                match ($r->status) {
                    'overdue' => $q->where('s.due_date', '<', $today),'soon' => $q->whereBetween('s.due_date', [$today, $soon]),default => $q->where('s.due_date', '>', $soon)
                };
            }
        }

        return view('finance.monitor', ['schedules' => $q->orderBy('s.due_date')->orderBy('s.id')->paginate(25)->withQueryString(), 'asOf' => $today]);
    }

    public function quote(int $loan, Finance $finance)
    {
        return view('finance.settlement', ['loan' => $loan, 'quote' => $finance->settlement($loan)]);
    }

    public function settle(FinancialRequest $r, Finance $finance)
    {
        $r->validate(['confirm' => 'accepted']);
        $finance->pay($r->validated(), $r->user()->id, true);

        return redirect()->route('finance.loans')->with('success', 'Pelunasan tercatat sesuai quotation.');
    }

    public function reverse(FinancialRequest $r, string $transaction, Finance $finance)
    {
        $d = $r->validated();
        $finance->reverse($transaction, $d['reason'], $d['tanggal'], $d['idempotency_key'], $r->user()->id);

        return back()->with('success', 'Pembatalan tercatat. Histori transaksi asal tetap tersimpan.');
    }
}
