@php
    $orientation = count($headers) >= 8 ? 'landscape' : 'portrait';
    $moneyKeys = ['jumlah','jumlah_pinjaman','sisa_pinjaman','sisa_jasa','jumlah_bayar','jasa_bayar','remaining_principal','remaining_interest','amount'];
    $columns = $rows->isNotEmpty() ? array_keys((array)$rows->first()) : [];
    $totalLabel = match($type) { 'simpanan'=>'Total setoran dikurangi penarikan', 'pinjaman'=>'Total sisa kewajiban', 'angsuran'=>'Total pembayaran diterima', default=>'Total sesuai filter' };
    $description = match($type) { 'simpanan'=>'Rincian setoran dan penarikan simpanan anggota.', 'pinjaman'=>'Daftar peminjam, proses pinjaman, dan sisa kewajiban.', 'angsuran'=>'Rincian pembayaran angsuran yang telah diterima.', default=>'Rekap data koperasi sesuai periode dan filter yang dipilih.' };
@endphp
@extends('layouts.document')
@section('document-title','Laporan '.ucfirst($type))
@section('document-actions')
<a href="{{ route('laporan.index', $filters) }}">Kembali ke laporan</a><button type="button" onclick="window.print()">Cetak laporan</button><a href="{{ route('laporan.pdf', $filters) }}">Unduh PDF</a>
@endsection
@section('document-content')
<p class="eyebrow">Laporan operasional</p><h1>Laporan {{ ucfirst($type) }}</h1><p class="subtitle">{{ $description }}</p>
<table class="meta"><tr><td style="width:50%"><p class="meta-label">Periode pencatatan</p><p class="meta-value">{{ $filters['tanggal_dari'] ?? 'Awal pencatatan' }} &mdash; {{ $filters['tanggal_sampai'] ?? 'Saat ini' }}</p></td><td style="width:25%"><p class="meta-label">Jumlah catatan</p><p class="meta-value">{{ $rows->count() }} transaksi / data</p></td><td style="width:25%"><p class="meta-label">Cakupan anggota</p><p class="meta-value">{{ !empty($filters['anggota']) ? 'ID '.$filters['anggota'] : 'Semua anggota' }}</p></td></tr></table>
@if(!empty($filters['status']) || !empty($filters['jenis_simpanan']))<p class="note" style="margin:0 0 14px">@if(!empty($filters['status']))Status: {{ $filters['status'] }}@endif @if(!empty($filters['jenis_simpanan']))Jenis simpanan: {{ $filters['jenis_simpanan'] }}@endif</p>@endif
<table class="data"><thead><tr><th class="number">No.</th>@foreach($headers as $index=>$header)@php($key=$columns[$index] ?? '')<th class="{{ in_array($key,$moneyKeys) ? 'money' : (in_array($key,['tanggal','date','due_date','tanggal_bergabung'])?'date':(in_array($key,['id_anggota','id_pinjaman','angsuran_ke','number','loan_id'])?'identifier':($key==='nama'?'person':''))) }}">{{ $header }}</th>@endforeach</tr></thead><tbody>
@forelse($rows as $row)<tr class="{{ $loop->even?'alternate':'' }}"><td class="number">{{ $loop->iteration }}</td>@foreach((array)$row as $key=>$value)<td class="{{ in_array($key,$moneyKeys)?'money':'' }}">{{ in_array($key,$moneyKeys) ? number_format((float)$value,2,',','.') : $value }}</td>@endforeach</tr>
@empty<tr><td class="empty" colspan="{{ count($headers)+1 }}">Tidak ada data untuk periode dan filter ini.</td></tr>@endforelse
</tbody></table>
@if($hasTotal)<table class="summary"><tr><td class="summary-label">{{ $totalLabel }}</td><td class="summary-amount">Rp{{ number_format($total,2,',','.') }}</td></tr></table>@endif
<p class="note">Nominal dalam rupiah. Laporan mengikuti periode dan filter yang dipilih. Pembayaran yang dibatalkan tidak dihitung sebagai penerimaan efektif.@if($type==='simpanan') Penarikan ditampilkan dengan nominal negatif; total merupakan mutasi bersih dalam periode, bukan saldo tabungan keseluruhan.@endif @if($type==='pinjaman')Sisa kewajiban menunjukkan posisi saat laporan diterbitkan.@endif</p>
<table class="signatures"><tr><td>Disiapkan oleh</td><td>Diperiksa oleh</td></tr><tr><td class="signature-space"></td><td></td></tr><tr><td class="signature-name">{{ auth()->user()?->name ?? 'Admin koperasi' }}</td><td>________________________</td></tr><tr><td class="signature-caption">Admin koperasi</td><td class="signature-caption">Pengurus koperasi</td></tr></table>
@endsection
