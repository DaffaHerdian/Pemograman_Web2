@extends('layouts.app')

@section('title', 'Edit Anggota - KoperasiKu')

@section('content')
<!-- Header -->
<div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-secondary-text mb-2">
        <a href="{{ route('anggota.index') }}" class="hover:text-primary">Data Anggota</a>
        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
        <span class="text-main-text">Edit Anggota</span>
    </div>
    <h1 class="text-3xl font-bold text-main-text">Edit Data Anggota</h1>
    <p class="text-sm text-secondary-text mt-1">Perbarui data anggota koperasi</p>
</div>

<!-- Form -->
<div class="bg-surface rounded-xl shadow-sm p-6 max-w-3xl">
    <form method="POST" action="{{ route('anggota.update', $anggota->id_anggota) }}">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-semibold text-main-text mb-2">
                    Nama Lengkap <span class="text-danger">*</span>
                </label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $anggota->nama) }}" required
                       class="w-full h-11 px-4 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all @error('nama') ring-2 ring-danger @enderror"
                       placeholder="Masukkan nama lengkap">
                @error('nama')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- NIK -->
            <div>
                <label for="nik" class="block text-sm font-semibold text-main-text mb-2">
                    NIK (Nomor Induk Kependudukan) <span class="text-danger">*</span>
                </label>
                <input type="text" id="nik" name="nik" value="{{ old('nik', $anggota->nik) }}" required maxlength="16"
                       class="w-full h-11 px-4 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all @error('nik') ring-2 ring-danger @enderror"
                       placeholder="Masukkan 16 digit NIK">
                @error('nik')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Telepon -->
            <div>
                <label for="telepon" class="block text-sm font-semibold text-main-text mb-2">
                    Nomor Telepon <span class="text-danger">*</span>
                </label>
                <input type="text" id="telepon" name="telepon" value="{{ old('telepon', $anggota->telepon) }}" required
                       class="w-full h-11 px-4 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all @error('telepon') ring-2 ring-danger @enderror"
                       placeholder="08xxxxxxxxxx">
                @error('telepon')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Jenis Kelamin -->
            <div>
                <label for="jenis_kelamin" class="block text-sm font-semibold text-main-text mb-2">
                    Jenis Kelamin <span class="text-danger">*</span>
                </label>
                <select id="jenis_kelamin" name="jenis_kelamin" required
                        class="w-full h-11 px-4 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all @error('jenis_kelamin') ring-2 ring-danger @enderror">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="Laki-laki" {{ old('jenis_kelamin', $anggota->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('jenis_kelamin', $anggota->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Alamat -->
            <div>
                <label for="alamat" class="block text-sm font-semibold text-main-text mb-2">
                    Alamat Lengkap <span class="text-danger">*</span>
                </label>
                <textarea id="alamat" name="alamat" required rows="3"
                          class="w-full px-4 py-3 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all @error('alamat') ring-2 ring-danger @enderror"
                          placeholder="Masukkan alamat lengkap">{{ old('alamat', $anggota->alamat) }}</textarea>
                @error('alamat')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Tanggal Bergabung -->
            <div>
                <label for="tanggal_bergabung" class="block text-sm font-semibold text-main-text mb-2">
                    Tanggal Bergabung <span class="text-danger">*</span>
                </label>
                <input type="date" id="tanggal_bergabung" name="tanggal_bergabung" value="{{ old('tanggal_bergabung', $anggota->tanggal_bergabung->format('Y-m-d')) }}" required
                       class="w-full h-11 px-4 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all @error('tanggal_bergabung') ring-2 ring-danger @enderror">
                @error('tanggal_bergabung')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-semibold text-main-text mb-2">
                    Status <span class="text-danger">*</span>
                </label>
                <select id="status" name="status" required
                        class="w-full h-11 px-4 bg-background rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all @error('status') ring-2 ring-danger @enderror">
                    <option value="Aktif" {{ old('status', $anggota->status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Tidak Aktif" {{ old('status', $anggota->status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
                @error('status')
                <p class="mt-1 text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Actions -->
        <div class="flex items-center gap-3 mt-8 pt-6 border-t border-border">
            <button type="submit" class="px-6 h-11 bg-primary text-white rounded-lg text-sm font-semibold hover:bg-primary/90 transition-all shadow-sm">
                Update Data Anggota
            </button>
            <a href="{{ route('anggota.index') }}" class="px-6 h-11 bg-surface border border-border text-main-text rounded-lg text-sm font-semibold hover:bg-background transition-all flex items-center">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
