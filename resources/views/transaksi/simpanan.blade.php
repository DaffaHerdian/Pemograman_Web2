@extends('layouts.app')

@section('title', ($page === 'angsuran' ? 'Angsuran' : 'Simpanan').' - KoperasiKu')

@section('content')
<div x-data="{
        showModal: {{ ($errors->penarikan->any() || ($page === 'angsuran' && ($errors->any() || $pinjamanBayar->contains('id_pinjaman', request('loan'))))) ? 'true' : 'false' }},
        modalType: '{{ $page === 'angsuran' ? 'angsuran' : ($errors->penarikan->any() ? 'penarikan' : '') }}'
    }"
    >
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-main-text">{{ $page === 'angsuran' ? 'Angsuran' : 'Simpanan' }}</h1>
            <p class="text-sm text-secondary-text mt-1">{{ $page === 'angsuran' ? 'Catat pembayaran pinjaman yang sudah dicairkan dan lihat riwayatnya.' : 'Catat setoran, tarik simpanan sukarela, dan lihat saldo anggota.' }}</p>
        </div>
        @if($page === 'simpanan')
                    <div class="flex flex-wrap gap-2">
                    <button type="button" @click="modalType = 'penarikan'; showModal = true"
                            class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-warning text-white text-sm font-semibold hover:bg-warning/90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">payments</span>
                        <span>Tarik Sukarela</span>
                    </button>
                    <button @click="modalType = 'simpanan'; showModal = true"
                            class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        <span>Catat Setoran</span>
                    </button>
                    </div>
        @endif
    </div>

    <div class="bg-surface rounded-xl shadow-sm mb-6">
        <div class="p-6">
            @if($page === 'simpanan')
            <!-- Simpanan -->
            <div>
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-main-text mb-2">Saldo Anggota</h2>
                    <p class="text-sm text-secondary-text mb-3">Saldo = setoran dikurangi penarikan. Hanya saldo sukarela yang dapat ditarik.</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-background border-b border-border">
                                <tr>
                                    <th class="px-4 py-3 text-left">Anggota</th>
                                    <th class="px-4 py-3 text-right">Saldo Total</th>
                                    <th class="px-4 py-3 text-right">Saldo Sukarela (Bisa Ditarik)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($saldoAnggota as $anggotaSaldo)
                                    <tr>
                                        <td class="px-4 py-3"><a href="{{ route('anggota.show', $anggotaSaldo->id_anggota) }}" class="text-primary hover:underline">{{ $anggotaSaldo->id_anggota }} — {{ $anggotaSaldo->nama }}</a></td>
                                        <td class="px-4 py-3 text-right">Rp{{ number_format($anggotaSaldo->saldo_total ?? 0, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-semibold">Rp{{ number_format($anggotaSaldo->saldo_sukarela ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-4 py-3 text-center text-secondary-text">Belum ada anggota.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $saldoAnggota->appends(['tab' => 'simpanan'])->links() }}</div>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-main-text">Riwayat Simpanan</h2>

                </div>

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
                                        {{ $item->jenis_simpanan }} · {{ $item->jenis_transaksi }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-main-text text-right font-semibold">{{ $item->jumlah < 0 ? '−' : '+' }}Rp{{ number_format(abs($item->jumlah), 2, ',', '.') }}</td>
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

            @endif
            @if($page === 'angsuran')
            <!-- Angsuran -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-main-text">Data Angsuran</h2>
                    <button @click="modalType = 'angsuran'; showModal = true"
                            class="inline-flex items-center gap-2 px-4 h-10 rounded-lg bg-primary text-white text-sm font-semibold hover:bg-primary/90 transition-all">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        <span>Catat Pembayaran</span>
                    </button>
                </div>

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
                                <td class="px-4 py-3 text-sm text-main-text text-center font-semibold">{{ $item->angsuran_ke }} @if($item->dibatalkan)<span class="text-danger block text-xs">Dibatalkan</span>@endif</td>
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
            @endif
        </div>
    </div>

    @if($page === 'simpanan')
    <div style="display: none;" x-cloak x-show="showModal && modalType === 'penarikan'"
         class="fixed inset-0 z-50 flex items-center justify-center bg-main-text/50 p-4">
        <div @click.away="showModal = false" class="bg-surface rounded-xl shadow-xl w-full max-w-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-main-text">Tarik Simpanan Sukarela</h3>
                <button type="button" @click="showModal = false" aria-label="Tutup" class="text-secondary-text hover:text-main-text">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <p class="text-sm text-secondary-text mb-4">Penarikan mengurangi saldo sukarela dan tetap tercatat dalam riwayat. Simpanan pokok dan wajib tidak berubah.</p>
            @if($errors->penarikan->any())
                <ul role="alert" class="mb-4 rounded-lg bg-red-50 p-3 text-sm text-danger">
                    @foreach($errors->penarikan->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            @if($anggotaPenarikan->isEmpty())
                <p class="text-sm text-secondary-text">Belum ada anggota dengan saldo sukarela yang bisa ditarik.</p>
            @else
                <form method="POST" action="{{ route('simpanan.withdraw') }}" class="space-y-4">
                    @csrf <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                    <div>
                        <label for="penarikan-anggota" class="block text-sm font-semibold mb-2">Anggota dan Saldo Tersedia</label>
                        <select id="penarikan-anggota" name="id_anggota" required class="w-full h-10 px-3 bg-background rounded-lg text-sm">
                            <option value="">Pilih Anggota</option>
                            @foreach($anggotaPenarikan as $anggotaTarik)
                                <option value="{{ $anggotaTarik->id_anggota }}" @selected(old('id_anggota') == $anggotaTarik->id_anggota)>{{ $anggotaTarik->id_anggota }} — {{ $anggotaTarik->nama }} · Rp{{ number_format($anggotaTarik->saldo_sukarela, 2, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="penarikan-tanggal" class="block text-sm font-semibold mb-2">Tanggal</label>
                        <input id="penarikan-tanggal" type="date" name="tanggal" value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}" required class="w-full h-10 px-3 bg-background rounded-lg text-sm">
                    </div>
                    <div>
                        <label for="penarikan-jumlah" class="block text-sm font-semibold mb-2">Jumlah Penarikan (Rp)</label>
                        <input id="penarikan-jumlah" type="number" name="jumlah" value="{{ old('jumlah') }}" min="0.01" max="9999999999999.99" step="0.01" required class="w-full h-10 px-3 bg-background rounded-lg text-sm">
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 h-10 rounded-lg bg-warning text-white text-sm font-semibold">Catat Penarikan</button>
                        <button type="button" @click="showModal = false" class="px-4 h-10 rounded-lg border border-border text-sm">Batal</button>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Modal Catat Setoran -->
    <div x-cloak x-show="showModal && modalType === 'simpanan'"
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
                    <h3 class="text-xl font-semibold text-main-text">Catat Setoran</h3>
                    <button @click="showModal = false" class="text-secondary-text hover:text-main-text">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('simpanan.store') }}">
                    @csrf <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Anggota <span class="text-danger">*</span></label>
                            <select name="id_anggota" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                                <option value="">Pilih Anggota</option>
                                @foreach($anggotaAktif as $anggota)
                                <option value="{{ $anggota->id_anggota }}">{{ $anggota->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" value="{{ now('Asia/Jakarta')->toDateString() }}" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
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

    @endif
    @if($page === 'angsuran')
    <!-- Modal Catat Pembayaran -->
    <div x-cloak x-show="showModal && modalType === 'angsuran'"
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
                    <h3 class="text-xl font-semibold text-main-text">Catat Pembayaran</h3>
                    <button @click="showModal = false" class="text-secondary-text hover:text-main-text">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('angsuran.store') }}">
                    @csrf <input type="hidden" name="idempotency_key" value="{{ (string) \Illuminate\Support\Str::uuid() }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Pinjaman <span class="text-danger">*</span></label>
                            <select name="id_pinjaman" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                                <option value="">Pilih Pinjaman</option>
                                @foreach($pinjamanBayar as $pinjaman)
                                <option value="{{ $pinjaman->id_pinjaman }}" @selected(old('id_pinjaman', request('loan')) == $pinjaman->id_pinjaman)>{{ $pinjaman->anggota->nama }} - Sisa: Rp{{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" value="{{ now('Asia/Jakarta')->toDateString() }}" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-main-text mb-2">Jumlah Bayar <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_bayar" value="{{ old('jumlah_bayar') }}" min="1" step="0.01" required class="w-full h-10 px-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" placeholder="Masukkan jumlah">
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
    @endif
</div>
@endsection
