<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KoperasiKu - Login Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F766E',
                        'primary-light': '#CCFBF1',
                        secondary: '#14B8A6',
                        background: '#F8FAFC',
                        surface: '#FFFFFF',
                        'main-text': '#0F172A',
                        'secondary-text': '#64748B',
                        border: '#E2E8F0',
                        success: '#16A34A',
                        warning: '#F59E0B',
                        danger: '#DC2626',
                        info: '#2563EB',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-background min-h-screen flex items-center justify-center p-4">
    <div class="relative w-full max-w-md">
        <!-- Subtle Ambient Glow -->
        <div class="absolute -top-12 -left-12 w-64 h-64 bg-primary-light/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-secondary/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <!-- Main Card -->
        <div class="relative w-full bg-surface rounded-xl shadow-xl p-8 sm:p-10 flex flex-col items-center">
            <!-- Brand & Header -->
            <div class="flex flex-col items-center text-center w-full">
                <div class="w-16 h-16 rounded-xl bg-primary flex items-center justify-center mb-4 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="w-10 h-10">
                        <circle cx="24" cy="24" r="15" fill="none" stroke="#CCFBF1" stroke-width="2.5" stroke-dasharray="3 2" opacity="0.6"/>
                        <path d="M24 14L16 20.5V32C16 32.8 16.7 33.5 17.5 33.5H30.5C31.3 33.5 32 32.8 32 32V20.5L24 14Z" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <circle cx="24" cy="24" r="4" fill="#CCFBF1"/>
                        <path d="M20 33.5V26.5C20 25.1 21.8 24 24 24C26.2 24 28 25.1 28 26.5V33.5" stroke="#FFFFFF" stroke-width="2" fill="none"/>
                    </svg>
                </div>
                <span class="text-xs uppercase tracking-widest text-primary font-semibold mb-1">
                    KoperasiKu Simpan Pinjam
                </span>
                <h1 class="text-2xl font-bold text-main-text tracking-tight">
                    Masuk ke KoperasiKu
                </h1>
                <p class="text-sm text-secondary-text mt-1.5 max-w-xs">
                    Kelola data anggota dan transaksi koperasi dengan mudah.
                </p>
            </div>
            
            <!-- Security / Role Tag -->
            <div class="mt-4 px-3 py-1 bg-primary-light/30 rounded-full flex items-center gap-1.5">
                <span class="material-symbols-outlined text-primary text-[16px]">verified_user</span>
                <span class="text-xs text-primary font-semibold">Portal Akses Pengurus & Admin</span>
            </div>
            
            <!-- Error Alert -->
            @if ($errors->any())
            <div class="w-full mt-5 bg-red-50 text-red-800 rounded-lg p-3.5 flex items-start justify-between shadow-sm">
                <div class="flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-danger text-[20px] shrink-0 mt-0.5">error</span>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-danger">Otentikasi Gagal</span>
                        <span class="text-xs text-red-700 leading-tight">{{ $errors->first() }}</span>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Login Form -->
            <form class="w-full mt-6 flex flex-col gap-4" method="POST" action="{{ url('/login') }}">
                @csrf
                
                <!-- Field: Email -->
                <div class="flex flex-col gap-1.5 text-left">
                    <label class="text-sm font-medium text-main-text flex items-center justify-between" for="email">
                        <span>Email Admin</span>
                        <span class="text-danger text-xs">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3 text-secondary-text text-[20px] pointer-events-none">account_circle</span>
                        <input class="w-full h-10 pl-10 pr-3 bg-background rounded-lg text-main-text text-sm focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all placeholder:text-secondary-text" 
                               id="email" 
                               name="email" 
                               placeholder="admin@koperasiku.id" 
                               required 
                               type="email" 
                               value="{{ old('email') }}">
                    </div>
                </div>
                
                <!-- Field: Password -->
                <div class="flex flex-col gap-1.5 text-left">
                    <label class="text-sm font-medium text-main-text flex items-center justify-between" for="password">
                        <span>Kata Sandi Admin</span>
                        <span class="text-danger text-xs">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3 text-secondary-text text-[20px] pointer-events-none">lock</span>
                        <input class="w-full h-10 pl-10 pr-10 bg-background rounded-lg text-main-text text-sm focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all placeholder:text-secondary-text" 
                               id="passwordInput" 
                               name="password" 
                               placeholder="Masukkan kata sandi" 
                               required 
                               type="password">
                        <button type="button" class="absolute right-3 text-secondary-text hover:text-main-text focus:outline-none flex items-center justify-center p-0.5" id="togglePasswordBtn">
                            <span class="material-symbols-outlined text-[20px]" id="eyeIcon">visibility</span>
                        </button>
                    </div>
                </div>
                
                <!-- Options: Remember Me -->
                <div class="flex items-center justify-between mt-1 text-left">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input class="w-4 h-4 rounded text-primary focus:ring-primary/40 accent-primary cursor-pointer" name="remember" type="checkbox">
                        <span class="text-sm text-secondary-text">Ingat Saya</span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button class="w-full h-11 mt-3 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold flex items-center justify-center gap-2 shadow-md hover:shadow-lg transition-all transform active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" type="submit">
                    <span>Masuk</span>
                    <span class="material-symbols-outlined text-[20px]">login</span>
                </button>
            </form>
            
            <!-- Security Guarantee Notice -->
            <div class="mt-6 w-full pt-4 bg-transparent flex flex-col items-center text-center gap-2">
                <div class="flex items-center gap-1.5 text-secondary-text text-center">
                    <span class="material-symbols-outlined text-[16px] text-primary">lock_clock</span>
                    <span class="text-xs">Sesi diamankan dengan enkripsi standar perbankan</span>
                </div>
                <p class="text-xs text-secondary-text tracking-tight mt-1">
                    Sistem Informasi Koperasi Simpan Pinjam v1.0 • Khusus Petugas Admin Koperasi
                </p>
            </div>
        </div>
    </div>
    
    <script>
        (function() {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            if (toggleBtn && passInput && eyeIcon) {
                toggleBtn.addEventListener('click', function () {
                    if (passInput.type === 'password') {
                        passInput.type = 'text';
                        eyeIcon.textContent = 'visibility_off';
                    } else {
                        passInput.type = 'password';
                        eyeIcon.textContent = 'visibility';
                    }
                });
            }
        })();
    </script>
</body>
</html>
