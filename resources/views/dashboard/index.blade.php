@extends('layouts.app')

@section('title', 'Dashboard - KoperasiKu')

@section('content')
<!-- Header Context & Quick Actions -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2">
            <span class="text-xs uppercase tracking-wider text-primary font-bold">Ringkasan Eksekutif</span>
            <span class="w-1.5 h-1.5 rounded-full bg-secondary-text"></span>
            <span class="text-xs text-secondary-text">Tahun Buku 2024</span>
        </div>
        <h1 class="text-3xl font-bold text-main-text tracking-tight">Dashboard Koperasi</h1>
        <p class="text-sm text-secondary-text">Ringkasan operasional simpan pinjam dan aktivitas harian koperasi.</p>
    </div>
    
    <!-- Quick Actions Bar -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('anggota.create') }}" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-primary text-white text-sm font-semibold shadow-sm hover:bg-primary/90 transition-all">
            <span class="material-symbols-outlined text-[18px]">person_add</span>
            <span>Tambah Anggota</span>
        </a>
        <a href="{{ route('simpanan.index') }}" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-secondary text-white text-sm font-semibold shadow-sm hover:bg-secondary/90 transition-all">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>Tambah Transaksi</span>
        </a>
        <a href="{{ route('laporan.index') }}" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-surface text-main-text text-sm font-semibold shadow-sm hover:bg-background transition-all border border-border">
            <span class="material-symbols-outlined text-[18px]">description</span>
            <span>Lihat Laporan</span>
        </a>
    </div>
</div>

<!-- Metric / Stat Cards (4-Column Grid) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Anggota -->
    <div class="p-6 rounded-xl bg-surface shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-primary/5 pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
        <div class="flex items-center justify-between gap-2">
            <span class="text-sm font-medium text-secondary-text">Total Anggota</span>
            <div class="w-10 h-10 rounded-xl bg-primary-light flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-[22px]">group</span>
            </div>
        </div>
        <div class="mt-4 flex flex-col gap-1">
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-bold text-main-text">{{ $totalAnggota }}</span>
                <span class="text-sm font-medium text-secondary-text">Jiwa</span>
            </div>
            <div class="flex items-center gap-1 text-xs text-primary font-semibold">
                <span class="material-symbols-outlined text-[14px]">arrow_upward</span>
                <span>Anggota aktif</span>
            </div>
        </div>
    </div>
    
    <!-- Total Simpanan -->
    <div class="p-6 rounded-xl bg-surface shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-secondary/10 pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
        <div class="flex items-center justify-between gap-2">
            <span class="text-sm font-medium text-secondary-text">Total Simpanan</span>
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-700">
                <span class="material-symbols-outlined text-[22px]">savings</span>
            </div>
        </div>
        <div class="mt-4 flex flex-col gap-1">
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold text-main-text">Rp{{ number_format($totalSimpanan / 1000000, 1) }}</span>
                <span class="text-sm text-secondary-text">Juta</span>
            </div>
            <span class="text-xs text-secondary-text">Pokok, Wajib, Sukarela</span>
        </div>
    </div>
    
    <!-- Total Pinjaman -->
    <div class="p-6 rounded-xl bg-surface shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-blue-100/30 pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
        <div class="flex items-center justify-between gap-2">
            <span class="text-sm font-medium text-secondary-text">Total Pinjaman</span>
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700">
                <span class="material-symbols-outlined text-[22px]">payments</span>
            </div>
        </div>
        <div class="mt-4 flex flex-col gap-1">
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold text-main-text">Rp{{ number_format($totalPinjaman / 1000000, 1) }}</span>
                <span class="text-sm text-secondary-text">Juta</span>
            </div>
            <span class="text-xs text-secondary-text">Total penyaluran</span>
        </div>
    </div>
    
    <!-- Angsuran Bulan Ini -->
    <div class="p-6 rounded-xl bg-surface shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-orange-100/30 pointer-events-none group-hover:scale-125 transition-transform duration-500"></div>
        <div class="flex items-center justify-between gap-2">
            <span class="text-sm font-medium text-secondary-text">Angsuran Bulan Ini</span>
            <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-700">
                <span class="material-symbols-outlined text-[22px]">history</span>
            </div>
        </div>
        <div class="mt-4 flex flex-col gap-1">
            <div class="flex items-baseline gap-1">
                <span class="text-2xl font-bold text-main-text">Rp{{ number_format($angsuranBulanIni / 1000000, 1) }}</span>
                <span class="text-sm text-secondary-text">Juta</span>
            </div>
            <div class="flex items-center justify-between text-xs">
                <span class="text-secondary-text">Pembayaran masuk</span>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Section: Status & Transaksi -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Status Pinjaman -->
    <div class="lg:col-span-5 bg-surface p-6 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex flex-col">
                <h2 class="text-xl font-semibold text-main-text">Status Pinjaman</h2>
                <span class="text-sm text-secondary-text">Distribusi pinjaman aktif</span>
            </div>
            <div class="w-8 h-8 rounded-full bg-background flex items-center justify-center text-secondary-text">
                <span class="material-symbols-outlined text-[18px]">pie_chart</span>
            </div>
        </div>
        
        <div class="flex flex-col gap-4">
            <!-- Berjalan -->
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">Berjalan</span>
                        <span class="text-secondary-text">{{ $pinjamanBerjalan }} Portofolio</span>
                    </div>
                    <span class="font-bold text-main-text">Rp{{ number_format($totalPinjamanBerjalan / 1000000, 1) }}Jt</span>
                </div>
                <div class="w-full bg-background rounded-full h-2 overflow-hidden">
                    <div class="bg-orange-500 h-full rounded-full" style="width: {{ $totalPinjaman > 0 ? ($totalPinjamanBerjalan / $totalPinjaman) * 100 : 0 }}%"></div>
                </div>
            </div>
            
            <!-- Lunas -->
            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Lunas</span>
                        <span class="text-secondary-text">{{ $totalPinjamanLunas }} Portofolio</span>
                    </div>
                    <span class="font-bold text-main-text">{{ $totalPinjaman > 0 ? number_format((($totalPinjaman - $totalPinjamanBerjalan) / $totalPinjaman) * 100, 0) : 0 }}%</span>
                </div>
                <div class="w-full bg-background rounded-full h-2 overflow-hidden">
                    <div class="bg-success h-full rounded-full" style="width: {{ $totalPinjaman > 0 ? (($totalPinjaman - $totalPinjamanBerjalan) / $totalPinjaman) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaksi Terbaru -->
    <div class="lg:col-span-7 bg-surface p-6 rounded-xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex flex-col">
                <h2 class="text-xl font-semibold text-main-text">Transaksi Terbaru</h2>
                <span class="text-sm text-secondary-text">Aktivitas transaksi terkini</span>
            </div>
        </div>
        
        @if($transaksiTerbaru->count() > 0)
        <div class="space-y-3">
            @foreach($transaksiTerbaru->take(5) as $transaksi)
            <div class="flex items-center justify-between py-3 border-b border-border last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg {{ $transaksi['jenis'] == 'Simpanan' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }} flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">{{ $transaksi['jenis'] == 'Simpanan' ? 'savings' : 'payments' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-main-text">{{ $transaksi['anggota'] }}</span>
                        <span class="text-xs text-secondary-text">{{ $transaksi['keterangan'] }}</span>
                    </div>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-sm font-bold text-main-text">Rp{{ number_format($transaksi['jumlah'], 0, ',', '.') }}</span>
                    <span class="text-xs text-secondary-text">{{ \Carbon\Carbon::parse($transaksi['tanggal'])->format('d M Y') }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <span class="material-symbols-outlined text-[48px] text-secondary-text mb-2">receipt_long</span>
            <p class="text-sm text-secondary-text">Belum ada transaksi</p>
        </div>
        @endif
    </div>
</div>
@endsection
