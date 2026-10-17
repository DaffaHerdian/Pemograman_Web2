@extends('layouts.app')
@section('title','Buat Pengajuan - KoperasiKu')
@section('content')
<div class="ui-page-heading"><div><h1>Buat Pengajuan Pinjaman</h1><p class="ui-page-description">Langkah 1: isi permohonan. Setelah disimpan, tinjau pengajuan di halaman Pinjaman sebelum mencairkan dana.</p></div><a class="ui-button ui-button--secondary" href="{{ route('finance.loans') }}">Kembali ke Pinjaman</a></div>
<form method="POST" action="{{ route('pinjaman.store') }}" class="ui-panel max-w-5xl">
    @include('finance.key')
    <div class="ui-section-heading"><h2>Permohonan anggota</h2><p>{{ $rules['label'] }} &middot; Bunga flat {{ $rules['rate'] }}% per bulan. Angsuran dihitung otomatis.</p></div>
    <div class="ui-form-grid">
        <label class="ui-field">Anggota aktif<select name="id_anggota" required><option value="">Pilih anggota</option>@foreach($members as $member)<option value="{{ $member->id_anggota }}" @selected(old('id_anggota') == $member->id_anggota)>{{ $member->id_anggota }} - {{ $member->nama }}</option>@endforeach</select></label>
        <label class="ui-field">Tanggal pengajuan<input name="tanggal" type="date" max="{{ now('Asia/Jakarta')->toDateString() }}" value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}" required></label>
        <label class="ui-field">Jumlah pinjaman (Rp)<input name="jumlah_pinjaman" type="number" min="1" max="{{ $rules['max_principal'] }}" step="1" value="{{ old('jumlah_pinjaman') }}" placeholder="Contoh: 1000000" required><span class="ui-help">Maksimal Rp{{ number_format($rules['max_principal'],0,',','.') }}</span></label>
        <label class="ui-field">Tenor (bulan)<input name="tenor" type="number" min="1" max="{{ $rules['max_months'] }}" value="{{ old('tenor') }}" placeholder="Contoh: 12" required><span class="ui-help">1 sampai {{ $rules['max_months'] }} bulan</span></label>
    </div>
    <div class="ui-actions"><a class="ui-button ui-button--secondary" href="{{ route('finance.simulator') }}">Cek simulasi</a><button class="ui-button ui-button--primary">Simpan pengajuan</button></div>
</form>
@endsection
