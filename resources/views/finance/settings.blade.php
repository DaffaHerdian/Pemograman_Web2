@extends('layouts.app')
@section('title','Pengaturan — KoperasiKu')
@section('content')
<div class="ui-page-heading"><div><h1>Pengaturan Koperasi</h1><p class="ui-page-description">Kelola identitas koperasi dan kebijakan untuk pengajuan baru.</p></div></div>
<form method="POST" action="{{ route('operations.settings.save') }}" enctype="multipart/form-data" class="max-w-5xl">
    @csrf
    <section class="ui-panel">
        <div class="ui-section-heading"><h2>Identitas koperasi</h2><p>Informasi ini digunakan pada laporan dan kuitansi.</p></div>
        <div class="ui-form-grid">
            <label class="ui-field" for="coop-name">Nama koperasi<input id="coop-name" name="name" value="{{ old('name',$identity->name) }}" required maxlength="255"></label>
            <label class="ui-field" for="coop-telephone">Nomor telepon<input id="coop-telephone" name="telephone" type="tel" inputmode="numeric" maxlength="15" pattern="[0-9]{1,15}" data-input-kind="digits" title="Nomor telepon hanya angka, maksimal 15 digit, tanpa spasi atau tanda tambah." value="{{ old('telephone',$identity->telephone) }}" placeholder="Nomor telepon koperasi"></label>
            <label class="ui-field ui-span-full" for="coop-address">Alamat<textarea id="coop-address" name="address" maxlength="2000" placeholder="Alamat lengkap koperasi">{{ old('address',$identity->address) }}</textarea></label>
            <div class="ui-span-full"><label class="ui-field mb-3" for="coop-logo">Logo koperasi</label><div class="ui-upload">@if($identity->logo_data)<img src="{{ $identity->logo_data }}" class="w-16 h-16 object-contain" alt="Logo koperasi">@else<span class="material-symbols-outlined text-secondary-text text-[30px]" aria-hidden="true">image</span>@endif<div class="min-w-0"><input id="coop-logo" type="file" name="logo" accept="image/png,image/jpeg"><p class="ui-help mt-2">PNG atau JPG, maksimal 1 MB dengan ukuran hingga 2000 × 2000 px.</p></div></div></div>
        </div>
    </section>
    <section class="ui-panel">
        <div class="ui-section-heading"><h2>Kebijakan pengajuan baru</h2><p>Setiap kontrak menyimpan kebijakan saat diajukan. Perubahan ini tidak mengubah pinjaman yang sudah ada.</p></div>
        <div class="ui-form-grid ui-form-grid--3">
            <label class="ui-field" for="policy-rate">Bunga flat / bulan (%)<input id="policy-rate" name="rate" value="{{ old('rate',$rules['rate']) }}" type="number" min="0" max="99.99" step="0.01" required></label>
            <label class="ui-field" for="policy-limit">Limit pokok (Rp)<input id="policy-limit" name="max_principal" value="{{ old('max_principal',$rules['max_principal']) }}" type="number" min="1" max="10000000" step="1" required></label>
            <label class="ui-field" for="policy-tenor">Tenor maksimal (bulan)<input id="policy-tenor" name="max_months" value="{{ old('max_months',$rules['max_months']) }}" type="number" min="1" max="36" required></label>
            <details class="ui-note ui-span-full"><summary>Lihat ketentuan pembayaran</summary><p>Rupiah bulat HALF_UP, selisih pada cicilan terakhir; jadwal bulanan tanpa penyesuaian hari libur; bunga dahulu lalu pokok pada jadwal tertua. Denda dan biaya nol. Bunga kontrak tetap saat pelunasan dini. Jasa simpanan belum diaktifkan karena tarifnya belum disetujui.</p><p class="mt-2">Versi saat ini: {{ $rules['label'] }}</p></details>
            <label class="ui-check ui-span-full"><input type="checkbox" name="confirm_policy" value="1" required><span>Saya menyetujui parameter di atas untuk pengajuan baru.</span></label>
        </div>
        <div class="ui-actions"><button class="ui-button ui-button--primary">Simpan pengaturan</button></div>
    </section>
</form>
@endsection
