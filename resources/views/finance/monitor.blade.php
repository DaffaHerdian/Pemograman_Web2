@extends('layouts.app')
@section('title','Jatuh Tempo — KoperasiKu')
@section('content')
<div class="ui-page-heading"><div><h1>Monitoring Jatuh Tempo</h1><p class="ui-page-description">Pantau jadwal cicilan, pembayaran yang akan datang, dan tunggakan anggota.</p></div></div>
<form class="ui-panel" method="GET">
    <div class="ui-form-grid ui-form-grid--3">
        <label class="ui-field" for="due-as-of">Tanggal acuan<input id="due-as-of" type="date" name="as_of" value="{{ $asOf }}"></label>
        <label class="ui-field" for="due-member">ID anggota<input id="due-member" type="number" min="1" name="member" value="{{ request('member') }}" placeholder="Semua anggota"></label>
        <label class="ui-field" for="due-status">Status cicilan<select id="due-status" name="status"><option value="">Semua status</option>@foreach(['upcoming'=>'Belum jatuh tempo','soon'=>'Segera (7 hari)','overdue'=>'Terlambat','paid'=>'Lunas'] as $key=>$label)<option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>@endforeach</select></label>
    </div>
    <div class="ui-filter-footer"><p class="ui-help">Jadwal otomatis tersedia untuk pinjaman dengan kontrak baru.</p><button class="ui-button ui-button--primary">Terapkan filter</button></div>
</form>
<div class="ui-table-panel">
    <div class="ui-table-heading"><div><h2>Jadwal cicilan</h2><p>{{ $schedules->total() }} jadwal sesuai filter</p></div></div>
    <div class="ui-table-scroll"><table class="ui-table"><thead><tr><th class="text-left">Anggota / pinjaman</th><th class="text-center">Cicilan</th><th class="text-left">Jatuh tempo</th><th class="text-right">Sisa pokok + bunga</th><th class="text-left">Status</th></tr></thead><tbody>
        @forelse($schedules as $s)<tr><td>{{ $s->nama }}<span class="ui-meta">Pinjaman #{{ $s->loan_id }}</span></td><td class="text-center">{{ $s->number }}</td><td>{{ $s->due_date }}</td><td class="text-right ui-table-amount">Rp{{ number_format((string)\Brick\Math\BigDecimal::of((string)$s->principal)->plus((string)$s->interest)->minus((string)$s->paid_principal)->minus((string)$s->paid_interest),2,',','.') }}</td><td><span class="ui-badge ui-badge--{{ ['overdue'=>'danger','soon'=>'warning','paid'=>'success','upcoming'=>'info'][$s->due_status] }}">{{ ['upcoming'=>'Belum jatuh tempo','soon'=>'Segera jatuh tempo','overdue'=>'Terlambat','paid'=>'Lunas'][$s->due_status] }}</span></td></tr>
        @empty<tr><td colspan="5"><div class="ui-empty"><span class="material-symbols-outlined" aria-hidden="true">event</span><strong>Tidak ada jadwal sesuai filter</strong><p>Jadwal cicilan muncul setelah pencairan pinjaman dicatat. Pinjaman lama tetap memakai riwayat sebelumnya.</p></div></td></tr>@endforelse
    </tbody></table></div>
</div>
<div class="mt-6">{{ $schedules->links() }}</div>
@endsection
