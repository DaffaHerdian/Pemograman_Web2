@extends('layouts.app')

@section('title', 'Detail Anggota - KoperasiKu')

@section('content')
<!-- Header -->
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-secondary-text mb-2">
        <a href="{{ route('anggota.index') }}" class="hover:text-primary">Data Anggota</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-main-text">Detail Anggota</span>
    </div>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-main-text">Detail Anggota</h1>
            <p class="text-sm text-secondary-text mt-1">Informasi lengkap data anggota</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('anggota.edit', $anggota->id_anggota) }}" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-warning text-white text-sm font-semibold shadow-sm hover:bg-warning/90 transition-all">
                <span class="material-symbols-outlined text-[18px]">edit</span>
                <span>Edit</span>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Data Anggota -->
    <div class="lg:col-span-2">
        <div class="bg-surface rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-main-text mb-4">Informasi Anggota</h2>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">ID Anggota</label>
                    <p class="mt-1 text-sm font-semibold text-main-text">{{ $anggota->id_anggota }}</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Status</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $anggota->status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $anggota->status }}
                        </span>
                    </p>
                </div>
                
                <div class="col-span-2">
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Nama Lengkap</label>
                    <p class="mt-1 text-sm font-semibold text-main-text">{{ $anggota->nama }}</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">NIK</label>
                    <p class="mt-1 text-sm text-main-text">{{ $anggota->nik }}</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">No. Telepon</label>
                    <p class="mt-1 text-sm text-main-text">{{ $anggota->telepon }}</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Jenis Kelamin</label>
                    <p class="mt-1 text-sm text-main-text">{{ $anggota->jenis_kelamin }}</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Tanggal Bergabung</label>
                    <p class="mt-1 text-sm text-main-text">{{ $anggota->tanggal_bergabung->format('d F Y') }}</p>
                </div>
                
                <div class="col-span-2">
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Alamat</label>
                    <p class="mt-1 text-sm text-main-text">{{ $anggota->alamat }}</p>
                </div>
            </div>
        </div>
        
        <!-- Riwayat Simpanan -->
        <div class="bg-surface rounded-xl shadow-sm p-6 mt-6">
            <h2 class="text-xl font-semibold text-main-text mb-4">Riwayat Simpanan</h2>
            
            @if($anggota->simpanan->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-background border-b border-border">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary-text">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary-text">Jenis</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-secondary-text">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($anggota->simpanan as $simpanan)
                        <tr>
                            <td class="px-4 py-2 text-sm text-main-text">{{ $simpanan->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-main-text">{{ $simpanan->jenis_simpanan }}</td>
                            <td class="px-4 py-2 text-sm text-main-text text-right font-semibold">Rp{{ number_format($simpanan->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-secondary-text text-center py-4">Belum ada riwayat simpanan</p>
            @endif
        </div>
        
        <!-- Riwayat Pinjaman -->
        <div class="bg-surface rounded-xl shadow-sm p-6 mt-6">
            <h2 class="text-xl font-semibold text-main-text mb-4">Riwayat Pinjaman</h2>
            
            @if($anggota->pinjaman->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-background border-b border-border">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary-text">Tanggal</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-secondary-text">Jumlah</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-secondary-text">Sisa</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-secondary-text">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($anggota->pinjaman as $pinjaman)
                        <tr>
                            <td class="px-4 py-2 text-sm text-main-text">{{ $pinjaman->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-main-text text-right font-semibold">Rp{{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-sm text-main-text text-right font-semibold">Rp{{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $pinjaman->status == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $pinjaman->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-secondary-text text-center py-4">Belum ada riwayat pinjaman</p>
            @endif
        </div>
    </div>
    
    <!-- Summary Card -->
    <div class="lg:col-span-1">
        <div class="bg-surface rounded-xl shadow-sm p-6 sticky top-6">
            <h2 class="text-lg font-semibold text-main-text mb-4">Ringkasan Keuangan</h2>
            
            <div class="space-y-4">
                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-green-700 text-[20px]">savings</span>
                        <span class="text-xs font-semibold text-green-700 uppercase">Total Simpanan</span>
                    </div>
                    <p class="text-2xl font-bold text-green-700">Rp{{ number_format($totalSimpanan, 0, ',', '.') }}</p>
                </div>
                
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-blue-700 text-[20px]">payments</span>
                        <span class="text-xs font-semibold text-blue-700 uppercase">Total Pinjaman</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-700">Rp{{ number_format($totalPinjaman, 0, ',', '.') }}</p>
                </div>
                
                <div class="p-4 bg-orange-50 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-orange-700 text-[20px]">account_balance</span>
                        <span class="text-xs font-semibold text-orange-700 uppercase">Sisa Pinjaman</span>
                    </div>
                    <p class="text-2xl font-bold text-orange-700">Rp{{ number_format($sisaPinjaman, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
