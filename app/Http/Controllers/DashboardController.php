<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Pinjaman;
use App\Models\Simpanan;
use App\Services\Finance;
use Brick\Math\BigDecimal as D;
use Brick\Math\RoundingMode;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Finance $finance)
    {
        $today = now('Asia/Jakarta');
        $totalAnggota = Anggota::count();
        $totalSimpanan = Simpanan::sum('jumlah');
        $eligible = fn () => Pinjaman::whereIn('workflow_status', ['legacy', 'disbursed', 'closed']);
        $totalPinjaman = $eligible()->sum('jumlah_pinjaman');
        $totalPinjamanBerjalan = $eligible()->where('status', 'Berjalan')->sum('sisa_pinjaman');
        $piutang = (string) D::of((string) $totalPinjamanBerjalan)->plus((string) $eligible()->where('status', 'Berjalan')->sum('sisa_jasa'));
        $totalPinjamanLunas = $eligible()->where('status', 'Lunas')->count();
        $pinjamanBerjalan = $eligible()->where('status', 'Berjalan')->count();
        $denominator = $pinjamanBerjalan + $totalPinjamanLunas;
        $runningPercent = $denominator ? round($pinjamanBerjalan / $denominator * 100) : 0;
        $paidPercent = $denominator ? round($totalPinjamanLunas / $denominator * 100) : 0;
        $angsuranBulanIni = DB::table('angsuran as a')->whereBetween('a.tanggal', [$today->copy()->startOfMonth()->toDateString(), $today->copy()->endOfMonth()->toDateString()])->whereNotExists(fn ($r) => $r->selectRaw('1')->from('financial_transactions as rev')->whereColumn('rev.reversal_of', 'a.transaction_id'))->sum('a.jumlah_bayar');
        $transaksiTerbaru = DB::table('financial_transactions as t')->leftJoin('anggota as a', 'a.id_anggota', '=', 't.member_id')->whereIn('t.kind', ['deposit', 'withdrawal', 'disbursement', 'repayment', 'settlement', 'reversal'])->select('t.*', 'a.nama')->orderByDesc('t.created_at')->orderByDesc('t.id')->limit(10)->get()->map(fn ($t) => ['jenis' => $t->kind, 'anggota' => $t->nama ?: 'Koperasi', 'jumlah' => $t->amount, 'tanggal' => $t->date, 'keterangan' => $t->kind.' · '.$t->number]);
        $outstanding = DB::table('loan_schedules as s')->join('pinjaman as p', 'p.id_pinjaman', '=', 's.loan_id')->where('p.workflow_status', 'disbursed')->whereRaw('s.principal+s.interest>s.paid_principal+s.paid_interest');
        $overdue = (clone $outstanding)->where('s.due_date', '<', $today->toDateString())->count();
        $soon = (clone $outstanding)->whereBetween('s.due_date', [$today->toDateString(), $today->copy()->addDays(7)->toDateString()])->count();
        $trends = [];
        $max = D::of(1);
        for ($n = 5; $n >= 0; $n--) {
            $month = $today->copy()->startOfMonth()->subMonths($n);
            $q = DB::table('ledger_entries as e')->join('financial_transactions as t', 't.id', '=', 'e.transaction_id')->whereBetween('t.date', [$month->toDateString(), $month->copy()->endOfMonth()->toDateString()]);
            $in = (string) (clone $q)->where('e.amount', '>', 0)->sum('e.amount');
            $out = (string) D::of((string) (clone $q)->where('e.amount', '<', 0)->sum('e.amount'))->negated();
            $max = D::max($max, D::of($in), D::of($out));
            $trends[] = ['month' => $month->format('M Y'), 'in' => $in, 'out' => $out];
        }
        foreach ($trends as &$t) {
            $t['in_width'] = (string) D::of($t['in'])->multipliedBy(100)->dividedBy($max, 0, RoundingMode::HalfUp);
            $t['out_width'] = (string) D::of($t['out'])->multipliedBy(100)->dividedBy($max, 0, RoundingMode::HalfUp);
        }unset($t);
        $cash = $finance->balance();
        $book = DB::table('cash_book')->first();

        return view('dashboard.index', compact('totalAnggota', 'totalSimpanan', 'totalPinjaman', 'totalPinjamanBerjalan', 'totalPinjamanLunas', 'pinjamanBerjalan', 'angsuranBulanIni', 'transaksiTerbaru', 'runningPercent', 'paidPercent', 'piutang', 'cash', 'book', 'overdue', 'soon', 'trends'));
    }
}
