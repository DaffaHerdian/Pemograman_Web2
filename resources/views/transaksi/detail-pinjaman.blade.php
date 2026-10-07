@extends('layouts.app')

@section('title', 'Detail Pinjaman - KoperasiKu')

@section('content')
<!-- Header -->
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-secondary-text mb-2">
        <a href="{{ route('simpanan.index') }}" class="hover:text-primary">Transaksi</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-main-text">Detail Pinjaman</span>
    </div>
    <h1 class="text-3xl font-bold text-main-text">Detail Pinjaman</h1>
    <p class="text-sm text-secondary-text mt-1">Informasi lengkap pinjaman dan riwayat angsuran</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Detail Pinjaman -->
    <div class="lg:col-span-2">
        <div class="bg-surface rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold text-main-text mb-4">Informasi Pinjaman</h2>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">ID Pinjaman</label>
                    <p class="mt-1 text-sm font-semibold text-main-text">{{ $pinjaman->id_pinjaman }}</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Status</label>
                    <p class="mt-1">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $pinjaman->status == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $pinjaman->status }}
                        </span>
                    </p>
                </div>
                
                <div class="col-span-2">
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Nama Anggota</label>
                    <p class="mt-1 text-sm font-semibold text-main-text">{{ $pinjaman->anggota->nama }}</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Tanggal Pinjaman</label>
                    <p class="mt-1 text-sm text-main-text">{{ $pinjaman->tanggal->format('d F Y') }}</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Tenor</label>
                    <p class="mt-1 text-sm text-main-text">{{ $pinjaman->tenor }} Bulan</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Jumlah Pinjaman</label>
                    <p class="mt-1 text-sm font-bold text-main-text">Rp{{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-secondary-text uppercase tracking-wider">Angsuran per Bulan</label>
                    <p class="mt-1 text-sm font-bold text-main-text">Rp{{ number_format($pinjaman->angsuran_per_bulan, 0, ',', '.') }}</p>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mt-6 pt-6 border-t border-border">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-main-text">Progress Pembayaran</span>
                    <span class="text-sm font-bold text-primary">{{ $pinjaman->jumlah_pinjaman > 0 ? number_format((($pinjaman->jumlah_pinjaman - $pinjaman->sisa_pinjaman) / $pinjaman->jumlah_pinjaman) * 100, 0) : 0 }}%</span>
                </div>
                <div class="w-full bg-background rounded-full h-3 overflow-hidden">
                    <div class="bg-primary h-full rounded-full transition-all" style="width: {{ $pinjaman->jumlah_pinjaman > 0 ? (($pinjaman->jumlah_pinjaman - $pinjaman->sisa_pinjaman) / $pinjaman->jumlah_pinjaman) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
        
        <!-- Riwayat Angsuran -->
        <div class="bg-surface rounded-xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-main-text mb-4">Riwayat Angsuran</h2>
            
            @if($pinjaman->angsuran->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-background border-b border-border">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-secondary-text">Tanggal</th>
                            <th class="px-4 py-2 text-center text-xs font-semibold text-secondary-text">Angsuran Ke</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-secondary-text">Jumlah</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold text-secondary-text">Sisa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($pinjaman->angsuran as $angsuran)
                        <tr>
                            <td class="px-4 py-2 text-sm text-main-text">{{ $angsuran->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-sm text-main-text text-center font-semibold">{{ $angsuran->angsuran_ke }}</td>
                            <td class="px-4 py-2 text-sm text-main-text text-right font-semibold">Rp{{ number_format($angsuran->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="px-4 py-2 text-sm text-main-text text-right font-semibold">Rp{{ number_format($angsuran->sisa_pinjaman, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-secondary-text text-center py-4">Belum ada riwayat angsuran</p>
            @endif
        </div>
    </div>
    
    <!-- Summary Card -->
    <div class="lg:col-span-1">
        <div class="bg-surface rounded-xl shadow-sm p-6 sticky top-6">
            <h2 class="text-lg font-semibold text-main-text mb-4">Ringkasan</h2>
            
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-blue-700 text-[20px]">payments</span>
                        <span class="text-xs font-semibold text-blue-700 uppercase">Jumlah Pinjaman</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-700">Rp{{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</p>
                </div>
                
                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-green-700 text-[20px]">check_circle</span>
                        <span class="text-xs font-semibold text-green-700 uppercase">Total Dibayar</span>
                    </div>
                    <p class="text-2xl font-bold text-green-700">Rp{{ number_format($totalDibayar, 0, ',', '.') }}</p>
                </div>
                
                <div class="p-4 bg-orange-50 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-orange-700 text-[20px]">account_balance</span>
                        <span class="text-xs font-semibold text-orange-700 uppercase">Sisa Pinjaman</span>
                    </div>
                    <p class="text-2xl font-bold text-orange-700">Rp{{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</p>
                </div>
                
                <div class="p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-purple-700 text-[20px]">history</span>
                        <span class="text-xs font-semibold text-purple-700 uppercase">Total Angsuran</span>
                    </div>
                    <p class="text-2xl font-bold text-purple-700">{{ $pinjaman->angsuran->count() }}x</p>
                </div>
            </div>
            
            @if($pinjaman->status == 'Berjalan')
            <div class="mt-6 pt-6 border-t border-border">
                <a href="{{ route('simpanan.index') }}#angsuran" class="w-full inline-flex items-center justify-center gap-2 px-4 h-10 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-all">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Tambah Angsuran</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
