@extends('layouts.app')
@section('title','Buku Kas — KoperasiKu')
@section('content')
<div class="ui-page-heading">
    <div><h1>Buku Kas Koperasi</h1><p class="ui-page-description">Pantau posisi kas, telusuri transaksi, dan kelola kuitansi koperasi.</p></div>
</div>
<div class="ui-panel ui-balance">
    <div><p class="ui-balance-label">Saldo kas saat ini</p><p class="ui-balance-value">Rp{{ number_format($balance,2,',','.') }}</p></div>
    <span class="ui-badge {{ $book->reconciled?'ui-badge--success':'ui-badge--warning' }}">{{ $book->reconciled?'Kas terverifikasi':'Belum direkonsiliasi' }}</span>
</div>
@if(!$book->reconciled)
<form method="POST" action="{{ route('finance.opening') }}" id="cash-opening" class="ui-panel">
    @include('finance.key')
    <div class="ui-section-heading"><h2>Verifikasi kas pembukaan</h2><p>Isi hasil hitung kas yang tersedia beserta sumbernya. Gunakan nol jika belum ada kas. Saldo awal ini dicatat sekali sebelum transaksi baru dimulai.</p></div>
    <div class="ui-form-grid">
        <label class="ui-field" for="opening-date">Tanggal pembukaan<input id="opening-date" type="date" name="tanggal" value="{{ old('tanggal',now('Asia/Jakarta')->toDateString()) }}" required></label>
        <label class="ui-field" for="opening-amount">Kas terverifikasi (Rp)<input id="opening-amount" name="amount" type="number" min="0" step="0.01" value="{{ old('amount') }}" placeholder="Contoh: 20000000" required></label>
        <label class="ui-field ui-span-full" for="opening-reason">Sumber dan alasan<textarea id="opening-reason" name="reason" minlength="10" maxlength="2000" placeholder="Jelaskan sumber dana dan hasil pemeriksaan kas" required>{{ old('reason') }}</textarea><span class="ui-help">Riwayat simpanan dan pinjaman sebelum pembukaan tetap tersimpan.</span></label>
        <label class="ui-check ui-span-full"><input type="checkbox" name="confirm" value="1" required><span>Saya sudah memeriksa sumber dan jumlah kas pembukaan.</span></label>
    </div>
    <div class="ui-actions"><button class="ui-button ui-button--primary">Konfirmasi kas pembukaan</button></div>
</form>
@endif
<form method="GET" class="ui-panel">
    <div class="ui-section-heading"><h2>Filter transaksi</h2><p>Tentukan rentang tanggal untuk menelusuri riwayat kas.</p></div>
    <div class="ui-form-grid">
        <label class="ui-field" for="cash-from">Dari tanggal<input id="cash-from" type="date" name="from" value="{{ request('from') }}"></label>
        <label class="ui-field" for="cash-to">Sampai tanggal<input id="cash-to" type="date" name="to" value="{{ request('to') }}"></label>
    </div>
    <div class="ui-actions">@if(request('from') || request('to'))<a href="{{ route('finance.index') }}" class="ui-button ui-button--secondary">Reset filter</a>@endif<button class="ui-button ui-button--primary"><span class="material-symbols-outlined" aria-hidden="true">filter_list</span>Terapkan filter</button></div>
</form>
<div class="ui-table-panel">
    <div class="ui-table-heading"><div><h2>Riwayat transaksi</h2><p>Kuitansi dan pembatalan dapat diakses dari setiap transaksi.</p></div><span class="ui-badge">{{ $transactions->total() }} transaksi</span></div>
    <div class="ui-table-scroll"><table class="ui-table"><thead><tr><th class="text-left">Transaksi / tanggal</th><th class="text-left">Jenis / admin</th><th class="text-right">Nominal</th><th class="text-left">Status / tindakan</th></tr></thead><tbody>
    @forelse($transactions as $t)
        @php($kindLabel=['opening'=>'Kas pembukaan','deposit'=>'Setoran simpanan','withdrawal'=>'Penarikan','loan_application'=>'Pengajuan pinjaman','loan_approve'=>'Persetujuan','loan_reject'=>'Penolakan','disbursement'=>'Pencairan pinjaman','repayment'=>'Bayar angsuran','settlement'=>'Pelunasan','reversal'=>'Pembatalan'][$t->kind]??$t->kind)
        <tr>
            <td><span class="ui-reference">{{ $t->number }}</span><span class="ui-meta">{{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}</span></td>
            <td>{{ $kindLabel }}<span class="ui-meta">{{ $t->actor }}</span></td>
            <td class="text-right ui-table-amount font-semibold">Rp{{ number_format($t->amount,2,',','.') }}</td>
            <td><div class="flex flex-col items-start gap-3">
                @if($t->reversed_by)<span class="ui-badge ui-badge--danger">Dibatalkan</span>
                @elseif($t->reversal_of)<span class="ui-badge ui-badge--warning">Transaksi pembalik</span>
                @else<span class="ui-badge ui-badge--success">Tercatat</span>@endif
                @if(in_array($t->kind,['opening','deposit','withdrawal','repayment','settlement','disbursement','reversal']))<a class="ui-link" href="{{ route('operations.receipt',$t->id) }}"><span class="material-symbols-outlined text-[16px]" aria-hidden="true">download</span>Kuitansi PDF</a>@endif
                @if(!$t->reversed_by && !$t->reversal_of && in_array($t->kind,['opening','deposit','withdrawal','repayment','settlement','disbursement']))
                    <details class="ui-action-menu"><summary>Koreksi / pembatalan</summary><form method="POST" action="{{ route('finance.reverse',$t->id) }}">
                        @include('finance.key')
                        <label class="ui-field">Tanggal pembatalan<input type="date" name="tanggal" value="{{ now('Asia/Jakarta')->toDateString() }}" required></label>
                        <label class="ui-field">Alasan pembatalan<textarea name="reason" minlength="10" maxlength="2000" placeholder="Jelaskan alasan koreksi" required></textarea></label>
                        <label class="ui-check"><input type="checkbox" name="confirm" value="1" required><span>Saya mengonfirmasi koreksi ini.</span></label>
                        <button class="ui-button ui-button--danger">Catat reversal</button>
                    </form></details>
                @endif
            </div></td>
        </tr>
    @empty<tr><td colspan="4"><div class="ui-empty"><span class="material-symbols-outlined" aria-hidden="true">receipt_long</span><strong>Belum ada transaksi baru</strong><p>Transaksi yang dicatat setelah pembukaan kas akan tampil di sini. Riwayat sebelumnya tersedia di Simpanan, Pinjaman, dan Angsuran.</p></div></td></tr>@endforelse
    </tbody></table></div>
</div>
<div class="mt-6">{{ $transactions->links() }}</div>
@endsection
