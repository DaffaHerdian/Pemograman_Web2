@extends('layouts.app')
@section('title','Pinjaman — KoperasiKu')
@section('content')
<div class="ui-page-heading"><div><h1>Pinjaman</h1><p class="ui-page-description">Kelola pengajuan, persetujuan, pencairan, dan riwayat pinjaman di satu tempat.</p></div><div class="ui-page-actions"><a href="{{ route('finance.simulator') }}" class="ui-button ui-button--secondary">Simulasi</a><a href="{{ route('pinjaman.create') }}" class="ui-button ui-button--primary"><span class="material-symbols-outlined" aria-hidden="true">add</span>Buat pengajuan</a></div></div>
<div class="ui-panel"><p class="ui-help">Alur pinjaman: <strong>Buat pengajuan &rarr; Tinjau dan setujui &rarr; Cairkan dana &rarr; Bayar angsuran &rarr; Lunas.</strong> Pengajuan dan persetujuan belum mengurangi kas.</p></div>
<div class="ui-table-panel">
    <div class="ui-table-heading"><div><h2>Daftar pinjaman</h2><p>Keputusan, pencairan, dan pelunasan tersedia pada kolom tindakan.</p></div><span class="ui-badge">{{ $loans->total() }} pinjaman</span></div>
    <div class="ui-table-scroll"><table class="ui-table ui-table--loans"><thead><tr><th class="text-left">Anggota / pinjaman</th><th class="text-right">Pokok / bunga</th><th class="text-left">Status</th><th class="text-left">Tindakan</th></tr></thead><tbody>
    @forelse($loans as $l)
        @php
            $statusLabel=['submitted'=>'Menunggu persetujuan','approved'=>'Disetujui','disbursed'=>'Sedang berjalan','closed'=>'Lunas','rejected'=>'Ditolak','reversed'=>'Dibatalkan','legacy'=>$l->status==='Lunas'?'Lunas':'Sedang berjalan'][$l->workflow_status]??$l->status;
            $statusColor=['submitted'=>'warning','approved'=>'info','disbursed'=>'success','closed'=>'success','rejected'=>'danger','reversed'=>'danger','legacy'=>$l->status==='Lunas'?'success':'info'][$l->workflow_status]??'info';
        @endphp
        <tr>
            <td><a class="ui-link" href="{{ route('pinjaman.show',$l->id_pinjaman) }}">{{ $l->nama }}</a><span class="ui-meta">Pinjaman #{{ $l->id_pinjaman }} · {{ $l->tenor }} bulan</span></td>
            <td class="text-right ui-table-amount"><span class="font-semibold text-main-text">Rp{{ number_format($l->jumlah_pinjaman,2,',','.') }}</span><span class="ui-meta">Bunga Rp{{ number_format($l->total_jasa,2,',','.') }}</span></td>
            <td><span class="ui-badge ui-badge--{{ $statusColor }}">{{ $statusLabel }}</span>@if($l->workflow_status==='legacy')<span class="ui-meta">Riwayat sebelumnya</span>@endif</td>
            <td>
            @if($l->workflow_status==='submitted')
                <details class="ui-action-menu"><summary>Tinjau pengajuan</summary><form method="POST" action="{{ route('finance.decision',$l->id_pinjaman) }}">
                    @include('finance.key')
                    <label class="ui-field">Keputusan<select name="decision"><option value="approve">Setujui</option><option value="reject">Tolak</option></select></label>
                    <label class="ui-field">Alasan keputusan<textarea name="reason" minlength="5" maxlength="2000" placeholder="Tuliskan hasil peninjauan" required></textarea></label>
                    <label class="ui-check"><input name="confirm" type="checkbox" value="1" required><span>Saya mengonfirmasi keputusan ini.</span></label>
                    <button class="ui-button ui-button--primary">Simpan keputusan</button>
                </form></details>
            @elseif($l->workflow_status==='approved')
                <details class="ui-action-menu"><summary>Catat pencairan</summary><form method="POST" action="{{ route('finance.disburse',$l->id_pinjaman) }}">
                    @include('finance.key')
                    <label class="ui-field">Tanggal pencairan<input name="tanggal" type="date" value="{{ now('Asia/Jakarta')->toDateString() }}" required></label>
                    <label class="ui-check"><input name="confirm" type="checkbox" value="1" required><span>Dana benar-benar dicairkan dan kas akan berkurang.</span></label>
                    <button class="ui-button ui-button--primary">Konfirmasi pencairan</button>
                </form></details>
            @elseif(in_array($l->workflow_status,['disbursed','legacy']) && $l->status==='Berjalan')
                <div class="ui-page-actions"><a class="ui-button ui-button--primary" href="{{ route('angsuran.index',['loan'=>$l->id_pinjaman]) }}">Bayar angsuran</a><a class="ui-button ui-button--secondary" href="{{ route('finance.quote',$l->id_pinjaman) }}">Tinjau pelunasan</a></div>
            @else<span class="ui-help">{{ $l->decision_reason ?: 'Tidak ada tindakan lanjutan' }}</span>@endif
            </td>
        </tr>
    @empty<tr><td colspan="4"><div class="ui-empty"><span class="material-symbols-outlined" aria-hidden="true">fact_check</span><strong>Belum ada pengajuan</strong><p>Mulai dari simulasi atau buat pengajuan baru untuk anggota.</p></div></td></tr>@endforelse
    </tbody></table></div>
</div>
<div class="mt-6">{{ $loans->links() }}</div>
@endsection
