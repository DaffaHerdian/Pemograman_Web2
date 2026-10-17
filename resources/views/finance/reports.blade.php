@extends('layouts.app')
@section('title','Laporan — KoperasiKu')
@section('content')
<div class="ui-page-heading"><div><h1>Laporan Koperasi</h1><p class="ui-page-description">Lihat siapa yang menyimpan, siapa yang meminjam, dan siapa yang sudah membayar angsuran. Cetak setiap jenis laporan secara terpisah.</p></div></div>
<nav class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6" aria-label="Jenis laporan">
    @foreach(['simpanan'=>['Simpanan','Daftar setoran dan penarikan tabungan anggota.'],'pinjaman'=>['Pinjaman','Daftar peminjam, proses pinjaman, dan sisa tagihan.'],'angsuran'=>['Angsuran','Daftar pembayaran yang diterima dari peminjam.']] as $value=>[$label,$description])
    <a href="{{ route('laporan.index', array_merge(request()->only('tanggal_dari','tanggal_sampai','anggota'), ['jenis'=>$value])) }}" class="ui-panel block" style="margin:0;{{ $type === $value ? 'border-color:#0f766e;background:#f0fdfa' : '' }}" @if($type === $value) aria-current="page" @endif><strong class="text-primary">Laporan {{ $label }}</strong><p class="ui-help mt-2">{{ $description }}</p></a>
    @endforeach
</nav>
<form method="GET" class="ui-panel">
    <div class="ui-section-heading"><h2>Filter laporan</h2><p>Lengkapi filter yang diperlukan. Kolom lainnya boleh dikosongkan.</p></div>
    <div class="ui-filter-fields">
        <input type="hidden" name="jenis" value="{{ $type }}">
        <label class="ui-field" for="report-from">Dari tanggal<input id="report-from" name="tanggal_dari" type="date" value="{{ request('tanggal_dari') }}"></label>
        <label class="ui-field" for="report-to">Sampai tanggal<input id="report-to" name="tanggal_sampai" type="date" value="{{ request('tanggal_sampai') }}"></label>
    </div>
    <details class="ui-advanced-filters" @if(request()->filled('anggota') || request()->filled('status') || request()->filled('jenis_simpanan')) open @endif>
        <summary>Filter anggota dan rincian laporan</summary>
        <div class="ui-filter-fields">
        <label class="ui-field" for="report-member">ID anggota<input id="report-member" name="anggota" type="number" min="1" value="{{ request('anggota') }}" placeholder="Semua anggota"></label>
        @if($type === 'pinjaman')
        <label class="ui-field" for="report-status">Status<input id="report-status" name="status" value="{{ request('status') }}" placeholder="Berjalan atau Lunas" maxlength="32"></label>
        @endif
        @if($type === 'simpanan')
        <label class="ui-field" for="report-savings">Jenis simpanan<select id="report-savings" name="jenis_simpanan"><option value="">Semua jenis</option>@foreach(['Pokok','Wajib','Sukarela'] as $j)<option @selected(request('jenis_simpanan')===$j)>{{ $j }}</option>@endforeach</select></label>
        @endif
    </div>
    </details>

    <div class="ui-filter-footer"><p class="ui-help">Maksimal 5.000 baris per ekspor. Gunakan filter untuk mempersempit hasil.</p><div class="ui-page-actions"><a class="ui-button ui-button--secondary" href="{{ route('laporan.index',['jenis'=>$type]) }}">Reset</a><button class="ui-button ui-button--primary"><span class="material-symbols-outlined" aria-hidden="true">filter_list</span>Terapkan filter</button></div></div>
</form>
<div class="ui-table-panel">
    <div class="ui-table-heading"><div><h2>Laporan {{ ucfirst($type) }}</h2><p>{{ $rows->count() }} catatan sesuai filter yang dipilih</p></div><div class="ui-page-actions">
        <a class="ui-button ui-button--secondary" href="{{ route('laporan.print',request()->query()) }}" target="_blank" rel="noopener"><span class="material-symbols-outlined" aria-hidden="true">print</span>Cetak</a>
        <a class="ui-button ui-button--secondary" href="{{ route('laporan.pdf',request()->query()) }}"><span class="material-symbols-outlined" aria-hidden="true">download</span>PDF</a>
        <a class="ui-button ui-button--secondary" href="{{ route('laporan.excel',request()->query()) }}"><span class="material-symbols-outlined" aria-hidden="true">download</span>Excel</a>
    </div></div>
    <div class="ui-table-scroll"><table class="ui-table"><thead><tr>@foreach($headers as $h)<th class="text-left">{{ $h }}</th>@endforeach</tr></thead><tbody>
        @forelse($rows as $row)<tr>@foreach((array)$row as $v)<td>{{ $v }}</td>@endforeach</tr>
        @empty<tr><td colspan="{{ count($headers) }}"><div class="ui-empty"><span class="material-symbols-outlined" aria-hidden="true">description</span><strong>Tidak ada data sesuai filter</strong><p>Coba ubah periode atau kosongkan sebagian filter untuk melihat hasil lainnya.</p></div></td></tr>@endforelse
    </tbody></table></div>
    @if($hasTotal)<div class="ui-table-total"><span>{{ $type==='pinjaman'?'Total sisa kewajiban':($type==='simpanan'?'Total setoran dikurangi penarikan':($type==='angsuran'?'Total pembayaran diterima':'Total sesuai filter')) }}</span><strong>Rp{{ number_format($total,2,',','.') }}</strong></div>@endif
</div>
@if($type==='kas')<p class="ui-help mt-4">Total di atas adalah mutasi kas dalam filter. Saldo kas keseluruhan tersedia di Buku Kas.</p>@endif
@endsection
