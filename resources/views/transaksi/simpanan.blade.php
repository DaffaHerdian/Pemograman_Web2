@extends('layouts.app')

@section('title', 'Transaksi - KoperasiKu')

@section('content')
<div x-data="{ activeTab: '{{ request('tab', 'simpanan') }}', showModal: false, modalType: '' }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-main-text">Transaksi</h1>
            <p class="text-sm text-secondary-text mt-1">Kelola simpanan, pinjaman, dan angsuran</p>
        </div>
    </div>
    
    <!-- Tabs -->
    <div class="bg-surface rounded-xl shadow-sm mb-6">
        <div class="border-b border-border px-6">
            <div class="flex gap-6">
                <button @click="activeTab = 'simpanan'" 
                        :class="activeTab === 'simpanan' ? 'border-primary text-primary' : 'border-transparent text-secondary-text hover:text-main-text'"
                        class="pb-3 pt-4 border-b-2 font-semibold text-sm transition-colors">
                    Simpanan
                </button>
                <button @click="activeTab = 'pinjaman'" 
                        :class="activeTab === 'pinjaman' ? 'border-primary text-primary' : 'border-transparent text-secondary-text hover:text-main-text'"
                        class="pb-3 pt-4 border-b-2 font-semibold text-sm transition-colors">
                    Pinjaman
                </button>
                <button @click="activeTab = 'angsuran'" 
                        :class="activeTab === 'angsuran' ? 'border-primary text-primary' : 'border-transparent text-secondary-text hover:text-main-text'"
                        class="pb-3 pt-4 border-b-2 font-semibold text-sm transition-colors">
                    Angsuran
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <!-- Simpanan Tab -->
            <div x-show="activeTab === 'simpanan'" x-transition>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-main-text">Data Simpanan</h2>
                    <button @click="modalType = 'simpanan'; showModal = true" 
                            class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        <span>Tambah Simpanan</span>
                    </button>
                </div>
                
                @php
                    $simpanan = \App\Models\Simpanan::with('anggota')->latest()->paginate(10, ['*'], 'simpanan_page');
                @endphp
                
                @if($simpanan->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-background border-b border-border">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Anggota</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Jenis</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($simpanan as $item)
                            <tr class="hover:bg-background transition-colors">
                                <td class="px-4 py-3 text-sm text-main-text">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-main-text">{{ $item->anggota->nama }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        {{ $item->jenis_simpanan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $simpanan->appends(['tab' => 'simpanan'])->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-[48px] text-secondary-text">savings</span>
                    <p class="text-sm text-secondary-text mt-2">Belum ada data simpanan</p>
                </div>
                @endif
            </div>
            
            <!-- Pinjaman Tab -->
            <div x-show="activeTab === 'pinjaman'" x-transition>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-main-text">Data Pinjaman</h2>
                    <button @click="modalType = 'pinjaman'; showModal = true" 
                            class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        <span>Tambah Pinjaman</span>
                    </button>
                </div>
                
                @php
                    $pinjaman = \App\Models\Pinjaman::with('anggota')->latest()->paginate(10, ['*'], 'pinjaman_page');
                @endphp
                
                @if($pinjaman->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-background border-b border-border">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Anggota</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Jumlah</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Sisa</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-text uppercase">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-text uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($pinjaman as $item)
                            <tr class="hover:bg-background transition-colors">
                                <td class="px-4 py-3 text-sm text-main-text">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-main-text">{{ $item->anggota->nama }}</td>
                                <td class="px-4 py-3 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->sisa_pinjaman, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $item->status == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('pinjaman.show', $item->id_pinjaman) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-info text-white rounded-lg text-xs font-semibold hover:bg-info/90 transition-all">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $pinjaman->appends(['tab' => 'pinjaman'])->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-[48px] text-secondary-text">payments</span>
                    <p class="text-sm text-secondary-text mt-2">Belum ada data pinjaman</p>
                </div>
                @endif
            </div>
            
            <!-- Angsuran Tab -->
            <div x-show="activeTab === 'angsuran'" x-transition>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-main-text">Data Angsuran</h2>
                    <button @click="modalType = 'angsuran'; showModal = true" 
                            class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        <span>Tambah Angsuran</span>
                    </button>
                </div>
                
                @php
                    $angsuran = \App\Models\Angsuran::with(['pinjaman.anggota'])->latest()->paginate(10, ['*'], 'angsuran_page');
                @endphp
                
                @if($angsuran->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-background border-b border-border">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-text uppercase">Anggota</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-text uppercase">Angsuran Ke</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Jumlah Bayar</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-secondary-text uppercase">Sisa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach($angsuran as $item)
                            <tr class="hover:bg-background transition-colors">
                                <td class="px-4 py-3 text-sm text-main-text">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm text-main-text">{{ $item->pinjaman->anggota->nama }}</td>
                                <td class="px-4 py-3 text-sm text-main-text text-center font-semibold">{{ $item->angsuran_ke }}</td>
                                <td class="px-4 py-3 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-main-text text-right font-semibold">Rp{{ number_format($item->sisa_pinjaman, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $angsuran->appends(['tab' => 'angsuran'])->links() }}
                </div>
                @else
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-[48px] text-secondary-text">history</span>
                    <p class="text-sm text-secondary-text mt-2">Belum ada data angsuran</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah Simpanan -->
    <div x-show="showModal && modalType === 'simpanan'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-main-text/50 p-4">
        <div @click.away="showModal = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-surface rounded-xl shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-main-text">Tambah Simpanan</h3>
                    <button @click="showModal = false" class="text-secondary-text hover:text-main-text">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('simpanan.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Anggota <span class="text-danger">*</span></label>
                            <select name="id_anggota" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                                <option value="">Pilih Anggota</option>
                                @foreach(\App\Models\Anggota::where('status', 'Aktif')->get() as $anggota)
                                <option value="{{ $anggota->id_anggota }}">{{ $anggota->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Jenis Simpanan <span class="text-danger">*</span></label>
                            <select name="jenis_simpanan" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                                <option value="">Pilih Jenis</option>
                                <option value="Pokok">Pokok</option>
                                <option value="Wajib">Wajib</option>
                                <option value="Sukarela">Sukarela</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" min="1" step="0.01" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" placeholder="Masukkan jumlah">
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="flex-1 h-10 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all">
                            Simpan
                        </button>
                        <button type="button" @click="showModal = false" class="px-4 h-10 bg-surface border border-border text-main-text rounded-lg text-sm font-semibold hover:bg-background transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah Pinjaman -->
    <div x-show="showModal && modalType === 'pinjaman'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center bg-main-text/50 p-4">
        <div @click.away="showModal = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-surface rounded-xl shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-main-text">Tambah Pinjaman</h3>
                    <button @click="showModal = false" class="text-secondary-text hover:text-main-text">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('pinjaman.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Anggota <span class="text-danger">*</span></label>
                            <select name="id_anggota" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                                <option value="">Pilih Anggota</option>
                                @foreach(\App\Models\Anggota::where('status', 'Aktif')->get() as $anggota)
                                <option value="{{ $anggota->id_anggota }}">{{ $anggota->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Jumlah Pinjaman (Max: Rp10.000.000) <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_pinjaman" min="1" max="10000000" step="0.01" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" placeholder="Masukkan jumlah">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Tenor (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" name="tenor" min="1" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" placeholder="Masukkan tenor">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Angsuran per Bulan <span class="text-danger">*</span></label>
                            <input type="number" name="angsuran_per_bulan" min="1" step="0.01" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" placeholder="Masukkan angsuran">
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="flex-1 h-10 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all">
                            Simpan
                        </button>
                        <button type="button" @click="showModal = false" class="px-4 h-10 bg-surface border border-border text-main-text rounded-lg text-sm font-semibold hover:bg-background transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Tambah Angsuran -->
    <div x-show="showModal && modalType === 'angsuran'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center bg-main-text/50 p-4">
        <div @click.away="showModal = false" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-surface rounded-xl shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-main-text">Tambah Angsuran</h3>
                    <button @click="showModal = false" class="text-secondary-text hover:text-main-text">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <form method="POST" action="{{ route('angsuran.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Pinjaman <span class="text-danger">*</span></label>
                            <select name="id_pinjaman" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                                <option value="">Pilih Pinjaman</option>
                                @foreach(\App\Models\Pinjaman::with('anggota')->where('status', 'Berjalan')->get() as $pinjaman)
                                <option value="{{ $pinjaman->id_pinjaman }}">{{ $pinjaman->anggota->nama }} - Sisa: Rp{{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Jumlah Bayar <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_bayar" min="1" step="0.01" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" placeholder="Masukkan jumlah">
                        </div>
                    </div>
                    
                    <div class="flex gap-3 mt-6">
                        <button type="submit" class="flex-1 h-10 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all">
                            Simpan
                        </button>
                        <button type="button" @click="showModal = false" class="px-4 h-10 bg-surface border border-border text-main-text rounded-lg text-sm font-semibold hover:bg-background transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
