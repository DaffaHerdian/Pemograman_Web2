@extends('layouts.app')

@section('title', 'Laporan - KoperasiKu')

@section('content')
<div x-data="{ jenis: '{{ $jenis }}' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-main-text">Laporan</h1>
            <p class="text-sm text-secondary-text mt-1">Laporan dan analisis data koperasi</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('laporan.print', ['jenis' => $jenis] + request()->all()) }}" target="_blank" 
               class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-info text-white text-sm font-semibold hover:bg-info/90 transition-all">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak</span>
            </a>
        </div>
    </div>
    
    <!-- Filter -->
    <div class="bg-surface rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('laporan.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-main-text mb-2">Jenis Laporan</label>
                    <select name="jenis" x-model="jenis" class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                        <option value="anggota">Data Anggota</option>
                        <option value="simpanan">Simpanan</option>
                        <option value="pinjaman">Pinjaman</option>
                        <option value="angsuran">Angsuran</option>
                    </select>
                </div>
                
                <div x-show="jenis === 'anggota'">
                    <label class="block text-sm font-semibold text-main-text mb-2">Status</label>
                    <select name="status" class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                        <option value="">Semua</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ request('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                
                <div x-show="jenis === 'simpanan' || jenis === 'pinjaman' || jenis === 'angsuran'">
                    <label class="block text-sm font-semibold text-main-text mb-2">Tanggal Dari</label>
                    <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                </div>
                
                <div x-show="jenis === 'simpanan' || jenis === 'pinjaman' || jenis === 'angsuran'">
                    <label class="block text-sm font-semibold text-main-text mb-2">Tanggal Sampai</label>
                    <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                </div>
                
                <div x-show="jenis === 'simpanan'">
                    <label class="block text-sm font-semibold text-main-text mb-2">Jenis Simpanan</label>
                    <select name="jenis_simpanan" class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                        <option value="">Semua</option>
                        <option value="Pokok" {{ request('jenis_simpanan') == 'Pokok' ? 'selected' : '' }}>Pokok</option>
                        <option value="Wajib" {{ request('jenis_simpanan') == 'Wajib' ? 'selected' : '' }}>Wajib</option>
                        <option value="Sukarela" {{ request('jenis_simpanan') == 'Sukarela' ? 'selected' : '' }}>Sukarela</option>
                    </select>
                </div>
                
                <div x-show="jenis === 'pinjaman'">
                    <label class="block text-sm font-semibold text-main-text mb-2">Status</label>
                    <select name="status" class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                        <option value="">Semua</option>
                        <option value="Berjalan" {{ request('status') == 'Berjalan' ? 'selected' : '' }}>Berjalan</option>
                        <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-6 h-10 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all">
                    Tampilkan Laporan
                </button>
                <a href="{{ route('laporan.index') }}" class="px-6 h-10 bg-surface border border-border text-main-text rounded-lg text-sm font-semibold hover:bg-background transition-all flex items-center">
                    Reset
                </a>
            </div>
        </form>
    </div>
    
    <!-- Data Laporan -->
    <div class="bg-surface rounded-xl shadow-sm overflow-hidden">
        @if($jenis == 'anggota')
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-background border-b border-border">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">NIK</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Telepon</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Tanggal Bergabung</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($data as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-main-text">{{ $item->id_anggota }}</td>
                            <td class="px-6 py-4 text-sm text-main-text">{{ $item->nama }}</td>
                            <td class="px-6 py-4 text-sm text-secondary-text">{{ $item->nik }}</td>
                            <td class="px-6 py-4 text-sm text-secondary-text">{{ $item->telepon }}</td>
                            <td class="px-6 py-4 text-sm text-secondary-text">{{ $item->tanggal_bergabung->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item->status == 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @elseif($jenis == 'simpanan')
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-background border-b border-border">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Anggota</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Jenis</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($data as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-main-text">{{ $item->tanggal->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-main-text">{{ $item->anggota->nama }}</td>
                            <td class="px-6 py-4 text-sm text-secondary-text">{{ $item->jenis_simpanan }}</td>
                            <td class="px-6 py-4 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-background border-t border-border">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm font-semibold text-main-text text-right">Total:</td>
                            <td class="px-6 py-4 text-sm font-bold text-main-text text-right">Rp{{ number_format($data->sum('jumlah'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @elseif($jenis == 'pinjaman')
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-background border-b border-border">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Anggota</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Sisa</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-secondary-text uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($data as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-main-text">{{ $item->tanggal->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-main-text">{{ $item->anggota->nama }}</td>
                            <td class="px-6 py-4 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->sisa_pinjaman, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item->status == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-background border-t border-border">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-sm font-semibold text-main-text text-right">Total:</td>
                            <td class="px-6 py-4 text-sm font-bold text-main-text text-right">Rp{{ number_format($data->sum('jumlah_pinjaman'), 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-main-text text-right">Rp{{ number_format($data->sum('sisa_pinjaman'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @elseif($jenis == 'angsuran')
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-background border-b border-border">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Anggota</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-secondary-text uppercase">Angsuran Ke</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Jumlah</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Sisa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($data as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm text-main-text">{{ $item->tanggal->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-main-text">{{ $item->pinjaman->anggota->nama }}</td>
                            <td class="px-6 py-4 text-sm text-main-text text-center font-semibold">{{ $item->angsuran_ke }}</td>
                            <td class="px-6 py-4 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->sisa_pinjaman, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-background border-t border-border">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-sm font-semibold text-main-text text-right">Total:</td>
                            <td class="px-6 py-4 text-sm font-bold text-main-text text-right">Rp{{ number_format($data->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
        
        @if($data->count() == 0)
        <div class="text-center py-12">
            <span class="material-symbols-outlined text-[48px] text-secondary-text">description</span>
            <p class="text-sm text-secondary-text mt-2">Tidak ada data untuk ditampilkan</p>
        </div>
        @endif
    </div>
</div>
@endsection
