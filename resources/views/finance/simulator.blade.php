@extends('layouts.app')
@section('content')
<div class="ui-page-actions mb-6"><a class="ui-button ui-button--secondary" href="{{ route('finance.loans') }}">Kembali ke Pinjaman</a><a class="ui-button ui-button--primary" href="{{ route('pinjaman.create') }}">Buat pengajuan</a></div>
<h1 class="text-3xl font-bold mb-2">Simulasi Pinjaman</h1><p class="text-secondary-text mb-6">{{ $rules['label'] }} · Flat {{ $rules['rate'] }}%/bulan. Hasil simulasi tidak mencatat pencairan.</p>
<div class="ui-panel max-w-5xl"><form id="simulator" class="ui-form-grid ui-form-grid--3">@csrf<label class="ui-field">Pokok (Rp)<input class="w-full border rounded p-2" name="jumlah_pinjaman" type="number" min="1" max="{{ $rules['max_principal'] }}" step="1" value="6000000" required></label><label class="ui-field">Tenor (bulan)<input class="w-full border rounded p-2" name="tenor" type="number" min="1" max="{{ $rules['max_months'] }}" value="12" required></label><label class="ui-field">Tanggal pencairan<input class="w-full border rounded p-2" name="tanggal" type="date" value="{{ now('Asia/Jakarta')->toDateString() }}" required></label><button class="ui-button ui-button--primary ui-span-full">Hitung simulasi</button></form><p id="simulation-error" class="text-danger mt-4" role="alert"></p><div id="simulation-result" class="mt-4" aria-live="polite"></div></div>
@endsection
@push('scripts')
<script>
document.getElementById('simulator').addEventListener('submit',async function(event){
 event.preventDefault();const button=this.querySelector('button');button.disabled=true;button.textContent='Menghitung…';
 const out=document.getElementById('simulation-result'),error=document.getElementById('simulation-error');out.replaceChildren();error.textContent='';
 try { const response=await fetch(@json(route('finance.calculate')),{method:'POST',headers:{'Accept':'application/json'},body:new FormData(this)});const data=await response.json();if(!response.ok)throw new Error(Object.values(data.errors||{}).flat().join(' ')||'Simulasi gagal.');
 const money=v=>'Rp'+BigInt(v).toLocaleString('id-ID');const summary=document.createElement('p');summary.className='font-semibold mb-4';summary.textContent=`Pokok ${money(data.principal)} · Bunga ${money(data.interest)} · Total ${money(data.total)} · Cicilan acuan ${money(data.monthly)}`;out.append(summary);
 const table=document.createElement('table');table.className='w-full text-sm';const head=table.createTHead().insertRow();['Ke','Jatuh tempo','Pokok','Bunga','Total'].forEach(t=>{const cell=document.createElement('th');cell.className='text-left p-2';cell.textContent=t;head.append(cell);});const body=table.createTBody();data.schedule.forEach(s=>{const row=body.insertRow();[s.number,s.due_date,money(s.principal),money(s.interest),money(s.total)].forEach(v=>{const cell=row.insertCell();cell.className='border-t p-2';cell.textContent=v;});});out.append(table);
 const note=document.createElement('p');note.className='mt-4 text-sm text-secondary-text';note.textContent='Pembulatan HALF_UP; selisih pada cicilan terakhir. Akhir bulan mengikuti kalender, hari libur tidak bergeser. Pembayaran bunga dahulu lalu pokok pada jadwal tertua. Tanpa denda/biaya. Pelunasan awal tetap menanggung bunga kontrak.';out.append(note);
 }catch(e){error.textContent=e.message;}finally{button.disabled=false;button.textContent='Hitung simulasi';}
});
</script>
@endpush
