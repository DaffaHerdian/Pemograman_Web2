@php
    $isPdf = true;
    $kindLabel = ['opening'=>'Kas pembukaan','deposit'=>'Setoran simpanan','withdrawal'=>'Penarikan simpanan','disbursement'=>'Pencairan pinjaman','repayment'=>'Pembayaran angsuran','settlement'=>'Pelunasan pinjaman','reversal'=>'Pembatalan transaksi'][$transaction->kind] ?? $transaction->kind;
@endphp
@extends('layouts.document')
@section('document-title','Kuitansi '.$receipt->number)
@section('document-content')
<p class="eyebrow">Bukti transaksi koperasi</p><h1>Kuitansi</h1><p class="subtitle">{{ $kindLabel }} &middot; {{ $receipt->number }}</p>
@if($cancelled)<div class="cancelled">DIBATALKAN &mdash; kuitansi ini tidak berlaku sebagai bukti transaksi aktif.</div>@endif
<table class="meta"><tr><td><p class="meta-label">Nomor kuitansi</p><p class="meta-value">{{ $receipt->number }}</p></td><td><p class="meta-label">Tanggal transaksi</p><p class="meta-value">{{ $transaction->date }}</p></td></tr></table>
<div class="receipt-amount"><p class="meta-label">Nominal transaksi</p><strong>Rp{{ number_format($transaction->amount,2,',','.') }}</strong></div>
<table class="detail"><tr><td class="key">Nomor transaksi</td><td class="value">{{ $transaction->number }}</td></tr><tr><td class="key">Jenis transaksi</td><td class="value">{{ $kindLabel }}</td></tr><tr><td class="key">Metode pencatatan</td><td class="value">Kas &mdash; diterima/dikeluarkan melalui admin</td></tr><tr><td class="key">Dicatat oleh</td><td class="value">{{ $actor }}</td></tr></table>
@if($qr)<table class="verification"><tr><td class="qr"><img src="data:image/svg+xml;base64,{{ base64_encode($qr) }}" alt="QR verifikasi"></td><td><p class="meta-value">Verifikasi kuitansi</p><p class="note">Pindai untuk memeriksa keabsahan dan status kuitansi. QR ini bukan kode pembayaran QRIS.</p><p class="note">{{ $verificationUrl }}</p></td></tr></table>@else<p class="note">Nomor kuitansi tetap sama saat dicetak ulang. Verifikasi QR publik belum tersedia.</p>@endif
<table class="signatures"><tr><td>Pihak anggota</td><td>Admin koperasi</td></tr><tr><td class="signature-space"></td><td></td></tr><tr><td>________________________</td><td class="signature-name">{{ $actor }}</td></tr></table>
<p class="note">Kuitansi diterbitkan dari transaksi yang tercatat. Koreksi dilakukan melalui pembatalan dengan riwayat tetap tersimpan.</p>
@endsection
