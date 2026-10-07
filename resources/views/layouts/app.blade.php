<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: true }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KoperasiKu - Sistem Informasi Koperasi')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white;
            }
            
            .print-full-width {
                width: 100% !important;
                margin: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-background">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="no-print fixed lg:static inset-y-0 left-0 z-50 w-64 bg-surface border-r border-border transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col">
            <!-- Logo & Brand -->
            <div class="h-16 flex items-center px-6 border-b border-border">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="w-6 h-6">
                            <path d="M24 14L16 20.5V32C16 32.8 16.7 33.5 17.5 33.5H30.5C31.3 33.5 32 32.8 32 32V20.5L24 14Z" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            <circle cx="24" cy="24" r="4" fill="#CCFBF1"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-bold text-main-text text-sm">KoperasiKu</span>
                        <span class="text-xs text-secondary-text">Simpan Pinjam</span>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 px-3">
                <!-- MAIN -->
                <div class="mb-6">
                    <p class="px-3 mb-2 text-xs font-semibold text-secondary-text uppercase tracking-wider">Main</p>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary-light text-primary font-semibold' : 'text-main-text hover:bg-background' }} transition-colors">
                        <span class="material-symbols-outlined text-[20px]">dashboard</span>
                        <span class="text-sm">Dashboard</span>
                    </a>
                </div>
                
                <!-- DATA -->
                <div class="mb-6">
                    <p class="px-3 mb-2 text-xs font-semibold text-secondary-text uppercase tracking-wider">Data</p>
                    <a href="{{ route('anggota.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('anggota.*') ? 'bg-primary-light text-primary font-semibold' : 'text-main-text hover:bg-background' }} transition-colors">
                        <span class="material-symbols-outlined text-[20px]">group</span>
                        <span class="text-sm">Anggota</span>
                    </a>
                    <a href="{{ route('anggota.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('anggota.create') ? 'bg-primary-light text-primary font-semibold' : 'text-main-text hover:bg-background' }} transition-colors">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                        <span class="text-sm">Form Anggota</span>
                    </a>
                </div>
                
                <!-- TRANSAKSI -->
                <div class="mb-6">
                    <p class="px-3 mb-2 text-xs font-semibold text-secondary-text uppercase tracking-wider">Transaksi</p>
                    <a href="{{ route('simpanan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('simpanan.*') || request()->routeIs('pinjaman.*') || request()->routeIs('angsuran.*') ? 'bg-primary-light text-primary font-semibold' : 'text-main-text hover:bg-background' }} transition-colors">
                        <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                        <span class="text-sm">Transaksi</span>
                    </a>
                </div>
                
                <!-- LAPORAN -->
                <div>
                    <p class="px-3 mb-2 text-xs font-semibold text-secondary-text uppercase tracking-wider">Laporan</p>
                    <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('laporan.*') ? 'bg-primary-light text-primary font-semibold' : 'text-main-text hover:bg-background' }} transition-colors">
                        <span class="material-symbols-outlined text-[20px]">description</span>
                        <span class="text-sm">Laporan</span>
                    </a>
                </div>
            </nav>
            
            <!-- User Profile -->
            <div class="border-t border-border p-4">
                <div class="flex items-center justify-between" x-data="{ profileOpen: false }">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-light flex items-center justify-center">
                            <span class="text-primary font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-main-text">{{ Auth::user()->name }}</span>
                            <span class="text-xs text-secondary-text">Admin</span>
                        </div>
                    </div>
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen" class="p-1 hover:bg-background rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-secondary-text text-[20px]">more_vert</span>
                        </button>
                        <div x-show="profileOpen" @click.away="profileOpen = false" x-transition class="absolute bottom-full right-0 mb-2 w-48 bg-surface rounded-lg shadow-lg border border-border py-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-danger hover:bg-red-50 transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">logout</span>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="no-print h-16 bg-surface border-b border-border flex items-center justify-between px-6">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 hover:bg-background rounded-lg transition-colors">
                    <span class="material-symbols-outlined text-main-text">menu</span>
                </button>
                
                <div class="flex items-center gap-4">
                    <span class="text-sm text-secondary-text">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 bg-green-50 border border-success/20 text-success rounded-lg p-4 flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        <div>
                            <p class="font-semibold text-sm">Berhasil!</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button @click="show = false" class="text-success hover:text-success/80">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                @endif
                
                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 bg-red-50 border border-danger/20 text-danger rounded-lg p-4 flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[20px]">error</span>
                        <div>
                            <p class="font-semibold text-sm">Error!</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                    </div>
                    <button @click="show = false" class="text-danger hover:text-danger/80">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="no-print fixed inset-0 bg-main-text/50 z-40 lg:hidden"></div>
    
    @stack('scripts')
</body>
</html>
