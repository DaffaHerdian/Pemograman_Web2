@extends('layouts.app')

@section('title', 'Data Anggota - KoperasiKu')

@section('content')
<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-3xl font-bold text-main-text">Data Anggota</h1>
        <p class="text-sm text-secondary-text mt-1">Kelola data anggota koperasi</p>
    </div>
    <a href="{{ route('anggota.create') }}" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-primary text-white text-sm font-semibold shadow-sm hover:bg-primary/90 transition-all">
        <span class="material-symbols-outlined text-[18px]">person_add</span>
        <span>Tambah Anggota</span>
    </a>
</div>

<!-- Search & Filter -->
<div class="bg-surface rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('anggota.index') }}" class="flex flex-col md:flex-row gap-3">
        <div class="flex-1">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-secondary-text text-[20px]">search</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, atau ID anggota..." class="w-full h-10 pl-10 pr-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all">
            </div>
        </div>
        <select name="status" class="h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
            <option value="">Semua Status</option>
            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
        <button type="submit" class="px-4 h-10 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all">
            Filter
        </button>
        @if(request('search') || request('status'))
        <a href="{{ route('anggota.index') }}" class="px-4 h-10 bg-surface border border-border text-main-text rounded-lg text-sm font-semibold hover:bg-background transition-all flex items-center">
            Reset
        </a>
        @endif
    </form>
</div>

<!-- Data Table -->
@if($anggota->count() > 0)
<div class="bg-surface rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-background border-b border-border">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">NIK</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">Jenis Kelamin</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach($anggota as $item)
                <tr class="hover:bg-background transition-colors">
                    <td class="px-6 py-4 text-sm text-main-text font-medium">{{ $item->id_anggota }}</td>
                    <td class="px-6 py-4 text-sm text-main-text">{{ $item->nama }}</td>
                    <td class="px-6 py-4 text-sm text-secondary-text">{{ $item->nik }}</td>
                    <td class="px-6 py-4 text-sm text-secondary-text">{{ $item->telepon }}</td>
                    <td class="px-6 py-4 text-sm text-secondary-text">{{ $item->jenis_kelamin }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item->status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $item->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('anggota.show', $item->id_anggota) }}" class="p-1.5 hover:bg-background rounded-lg transition-colors" title="Detail">
                                <span class="material-symbols-outlined text-[18px] text-info">visibility</span>
                            </a>
                            <a href="{{ route('anggota.edit', $item->id_anggota) }}" class="p-1.5 hover:bg-background rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-[18px] text-warning">edit</span>
                            </a>
                            <button onclick="confirmDelete({{ $item->id_anggota }})" class="p-1.5 hover:bg-background rounded-lg transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-[18px] text-danger">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-border">
        {{ $anggota->links() }}
    </div>
</div>
@else
<!-- Empty State -->
<div class="bg-surface rounded-xl shadow-sm p-12 text-center">
    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-primary-light flex items-center justify-center">
        <span class="material-symbols-outlined text-[48px] text-primary">group</span>
    </div>
    <h3 class="text-xl font-semibold text-main-text mb-2">Belum Ada Data Anggota</h3>
    <p class="text-sm text-secondary-text mb-6">Mulai tambahkan anggota koperasi untuk mengelola data simpan pinjam</p>
    <a href="{{ route('anggota.create') }}" class="inline-flex items-center gap-2 px-6 h-11 rounded-lg bg-primary text-white text-sm font-semibold shadow-sm hover:bg-primary/90 transition-all">
        <span class="material-symbols-outlined text-[18px]">person_add</span>
        <span>Tambah Anggota Pertama</span>
    </a>
</div>
@endif
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus anggota ini? Anggota dengan transaksi tidak dapat dihapus.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/anggota/' + id;
        
        const csrfField = document.createElement('input');
        csrfField.type = 'hidden';
        csrfField.name = '_token';
        csrfField.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfField);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
