<?php

namespace App\Services;

use Brick\Math\BigDecimal as D;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ReportData
{
    public const MAIN_TYPES = ['simpanan', 'pinjaman', 'angsuran'];

    public const TYPES = ['anggota', 'simpanan', 'pinjaman', 'angsuran', 'tunggakan', 'kas'];

    public function filters(Request $r): array
    {
        return $r->validate(['jenis' => 'nullable|in:'.implode(',', self::TYPES), 'tanggal_dari' => 'nullable|date_format:Y-m-d', 'tanggal_sampai' => 'nullable|date_format:Y-m-d'.($r->filled('tanggal_dari') ? '|after_or_equal:tanggal_dari' : ''), 'anggota' => 'nullable|integer|min:1', 'status' => 'nullable|string|max:32', 'jenis_simpanan' => 'nullable|in:Pokok,Wajib,Sukarela']);
    }

    public function data(array $f): array
    {
        $type = $f['jenis'] ?? 'simpanan';
        $q = null;
        $headers = [];
        $amount = null;
        if ($type === 'anggota') {
            $headers = ['ID', 'Nama', 'Status', 'Tanggal bergabung'];
            $q = DB::table('anggota')->select('id_anggota', 'nama', 'status', 'tanggal_bergabung')->orderBy('id_anggota');
            $member = 'id_anggota';
            $date = 'tanggal_bergabung';
            if (! empty($f['status'])) {
                $q->where('status', $f['status']);
            }
        } elseif ($type === 'simpanan') {
            $headers = ['Tanggal', 'ID anggota', 'Anggota', 'Jenis simpanan', 'Transaksi', 'Nominal (Rp)'];
            $q = DB::table('simpanan as s')->join('anggota as a', 'a.id_anggota', '=', 's.id_anggota')->select('s.tanggal', 'a.id_anggota', 'a.nama', 's.jenis_simpanan')->selectRaw("CASE WHEN s.jumlah < 0 THEN 'Penarikan' ELSE 'Setoran' END as transaksi")->addSelect('s.jumlah')->orderBy('s.tanggal')->orderBy('s.id_simpanan');
            $member = 's.id_anggota';
            $date = 's.tanggal';
            $amount = 'jumlah';
            if (! empty($f['jenis_simpanan'])) {
                $q->where('s.jenis_simpanan', $f['jenis_simpanan']);
            }
        } elseif ($type === 'pinjaman') {
            $headers = ['ID pinjaman', 'Tanggal', 'ID anggota', 'Anggota', 'Status', 'Proses', 'Pokok (Rp)', 'Sisa pokok (Rp)', 'Sisa bunga (Rp)'];
            $q = DB::table('pinjaman as p')->join('anggota as a', 'a.id_anggota', '=', 'p.id_anggota')->select('p.id_pinjaman', 'p.tanggal', 'a.id_anggota', 'a.nama', 'p.status', 'p.workflow_status', 'p.jumlah_pinjaman', 'p.sisa_pinjaman', 'p.sisa_jasa')->orderBy('p.id_pinjaman');
            $member = 'p.id_anggota';
            $date = 'p.tanggal';
            $amount = 'sisa_pinjaman';
            if (! empty($f['status'])) {
                $q->where('p.status', $f['status']);
            }
        } elseif ($type === 'angsuran') {
            // Reversed receipts remain in transaction history; this report shows effective payments only.
            $headers = ['Tanggal', 'ID anggota', 'Anggota', 'ID pinjaman', 'Pembayaran ke', 'Jumlah bayar (Rp)', 'Bunga dibayar (Rp)'];
            $q = DB::table('angsuran as x')->join('pinjaman as p', 'p.id_pinjaman', '=', 'x.id_pinjaman')->join('anggota as a', 'a.id_anggota', '=', 'p.id_anggota')->whereNotExists(fn ($r) => $r->selectRaw('1')->from('financial_transactions as rev')->whereColumn('rev.reversal_of', 'x.transaction_id'))->select('x.tanggal', 'a.id_anggota', 'a.nama', 'x.id_pinjaman', 'x.angsuran_ke', 'x.jumlah_bayar', 'x.jasa_bayar')->orderBy('x.tanggal')->orderBy('x.id_angsuran');
            $member = 'p.id_anggota';
            $date = 'x.tanggal';
            $amount = 'jumlah_bayar';
        } elseif ($type === 'tunggakan') {
            $headers = ['Jatuh tempo', 'Anggota', 'Pinjaman', 'Cicilan', 'Sisa pokok', 'Sisa bunga'];
            $q = DB::table('loan_schedules as s')->join('pinjaman as p', 'p.id_pinjaman', '=', 's.loan_id')->join('anggota as a', 'a.id_anggota', '=', 'p.id_anggota')->where('p.workflow_status', 'disbursed')->where('s.due_date', '<', now('Asia/Jakarta')->toDateString())->whereRaw('s.principal+s.interest>s.paid_principal+s.paid_interest')->select('s.due_date', 'a.nama', 's.loan_id', 's.number')->selectRaw('s.principal-s.paid_principal as remaining_principal, s.interest-s.paid_interest as remaining_interest')->orderBy('s.due_date')->orderBy('s.id');
            $member = 'p.id_anggota';
            $date = 's.due_date';
            $amount = 'remaining_principal';
        } else {
            $headers = ['Tanggal', 'Nomor transaksi', 'Jenis', 'Mutasi kas'];
            $q = DB::table('ledger_entries as e')->join('financial_transactions as t', 't.id', '=', 'e.transaction_id')->select('t.date', 't.number', 't.kind', 'e.amount')->orderBy('t.date')->orderBy('e.id');
            $member = 't.member_id';
            $date = 't.date';
            $amount = 'amount';
        }
        if (! empty($f['tanggal_dari'])) {
            $q->whereDate($date, '>=', $f['tanggal_dari']);
        }if (! empty($f['tanggal_sampai'])) {
            $q->whereDate($date, '<=', $f['tanggal_sampai']);
        }if (! empty($f['anggota'])) {
            $q->where($member, $f['anggota']);
        }
        // Bounded export prevents exhausting shared hosting memory; users narrow date/member filters.
        $rows = $q->limit(5001)->get();
        if ($rows->count() > 5000) {
            throw ValidationException::withMessages(['laporan' => 'Maksimal 5.000 baris per ekspor. Persempit filter.']);
        }
        $total = D::of(0);
        if ($amount) {
            foreach ($rows as $r) {
                $total = $total->plus((string) $r->$amount);
                if ($type === 'tunggakan') {
                    $total = $total->plus((string) $r->remaining_interest);
                }if ($type === 'pinjaman') {
                    $total = $total->plus((string) $r->sisa_jasa);
                }
            }
        }

        if ($type === 'pinjaman') {
            foreach ($rows as $row) {
                $row->workflow_status = ['submitted' => 'Menunggu persetujuan', 'approved' => 'Disetujui', 'disbursed' => 'Sudah dicairkan', 'closed' => 'Lunas', 'rejected' => 'Ditolak', 'reversed' => 'Dibatalkan', 'legacy' => 'Riwayat sebelumnya'][$row->workflow_status] ?? $row->workflow_status;
            }
        }

        return ['type' => $type, 'headers' => $headers, 'rows' => $rows, 'total' => (string) $total->toScale(2), 'hasTotal' => $amount !== null, 'filters' => $f, 'identity' => Identity::get()];
    }
}
