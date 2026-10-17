<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('fonts/fonts.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KoperasiKu - Sistem Informasi Koperasi')</title>
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
    <link rel="stylesheet" href="{{ asset('css/ui.css') }}?v={{ filemtime(public_path('css/ui.css')) }}">
    @stack('styles')
</head>
<body class="bg-background app-shell">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
               class="app-sidebar no-print fixed lg:static inset-y-0 left-0 z-50 w-64 bg-surface border-r border-border transition-transform duration-300 ease-in-out lg:translate-x-0 flex flex-col">
            <!-- Logo & Brand -->
            <div class="app-brand flex items-center px-6 border-b border-border">
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

            <nav class="flex-1 overflow-y-auto" aria-label="Menu utama">
                @php
                    $navigation = [
                        'Utama' => [['dashboard','dashboard','Dashboard',['dashboard']]],
                        'Anggota' => [
                            ['anggota.index','group','Anggota',['anggota.*']],
                        ],
                        'Keuangan' => [
                            ['simpanan.index','savings','Simpanan',['simpanan.*']],
                            ['finance.loans','account_balance','Pinjaman',['pinjaman.*','finance.loans','finance.quote','finance.simulator']],
                            ['angsuran.index','payments','Angsuran',['angsuran.*']],
                            ['finance.monitor','event','Jatuh Tempo',['finance.monitor']],
                            ['finance.index','account_balance_wallet','Buku Kas',['finance.index']],
                            ['laporan.index','description','Laporan',['laporan.*']],
                        ],
                        'Administrasi' => [
                            ['operations.activity','history','Aktivitas Admin',['operations.activity']],
                            ['operations.settings','settings','Pengaturan',['operations.settings']],
                            ['backup.index','backup','Backup Database',['backup.index']],
                        ],
                    ];
                @endphp
                @foreach($navigation as $group=>$links)
                    <div class="ui-nav-group">
                        <p class="ui-nav-label">{{ $group }}</p>
                        @foreach($links as [$name,$icon,$label,$patterns])
                            <a href="{{ route($name) }}" class="ui-nav-link {{ request()->routeIs(...$patterns)?'is-active':'' }}" @if(request()->routeIs(...$patterns)) aria-current="page" @endif>
                                <span class="material-symbols-outlined" aria-hidden="true">{{ $icon }}</span>
                                <span>{{ $label }}</span>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </nav>
            <!-- User Profile -->
            <div class="border-t border-border p-4">
                <div class="flex items-center justify-between" x-data="{ profileOpen: false }">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-light flex items-center justify-center">
                            <span class="text-primary font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="user-name text-sm font-semibold text-main-text" title="{{ Auth::user()->name }}">{{ Auth::user()->name }}</span>
                            <span class="text-xs text-secondary-text">Admin</span>
                        </div>
                    </div>
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen" class="p-1 hover:bg-background rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-secondary-text text-[20px]">more_vert</span>
                        </button>
                        <div x-cloak x-show="profileOpen" @click.away="profileOpen = false" x-transition class="absolute bottom-full right-0 mb-2 w-48 bg-surface rounded-lg shadow-lg border border-border py-2">
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
        <div class="app-workspace flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="app-header no-print bg-surface border-b border-border flex items-center justify-between">
                <div class="flex items-center gap-3 min-w-0">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-background" aria-label="Buka atau tutup menu">
                        <span class="material-symbols-outlined text-main-text">menu</span>
                    </button>
                    <span class="hidden lg:inline text-xs text-secondary-text">Ruang kerja koperasi</span>
                </div>
                <div class="flex items-center gap-4 sm:gap-5">
                    <a href="{{ route('finance.monitor') }}" class="ui-reminder">
                        <span class="material-symbols-outlined text-[17px]" aria-hidden="true">notifications</span>
                        <span class="hidden sm:inline">Pengingat jatuh tempo</span>
                        <span class="ui-reminder-count">{{ $dueReminderCount ?? 0 }}</span>
                    </a>
                    <span class="ui-header-date text-xs text-secondary-text">{{ now('Asia/Jakarta')->format('d M Y') }}</span>
                </div>
            </header>

            <!-- Page Content -->
            <main class="app-main flex-1 overflow-y-auto"><div class="app-content">
                @if(session('success'))
                <div x-data="{ show: true }" x-cloak x-show="show" x-transition class="mb-6 bg-green-50 border border-success/20 text-success rounded-lg p-4 flex items-start justify-between">
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
                <div x-data="{ show: true }" x-cloak x-show="show" x-transition class="mb-6 bg-red-50 border border-danger/20 text-danger rounded-lg p-4 flex items-start justify-between">
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

                @if(($cashOpeningRequired ?? false) && request()->routeIs('dashboard','simpanan.*','angsuran.*','pinjaman.*','finance.loans','finance.quote'))
                <div class="ui-panel" role="status" style="border-color:#fcd89b;background:#fffbf2">
                    <div class="ui-section-heading"><h2>Siapkan kas sebelum mencatat transaksi</h2><p>Saldo kas pembukaan belum dikonfirmasi. Buka Buku Kas, isi uang yang tersedia beserta sumbernya, lalu konfirmasi satu kali. Saldo simpanan anggota di tabel tidak otomatis menjadi saldo kas.</p></div>
                    <a class="ui-button ui-button--primary" href="{{ route('finance.index') }}#cash-opening">Isi kas pembukaan</a>
                </div>
                @endif
                @if($errors->any())<div class="mb-6 bg-red-50 border border-danger/20 text-danger rounded-lg p-4" role="alert"><ul class="list-disc pl-5">@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </div></main>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div x-cloak x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="no-print fixed inset-0 bg-main-text/50 z-40 lg:hidden"></div>

    <script defer src="{{ asset('js/input-constraints.js') }}?v={{ filemtime(public_path('js/input-constraints.js')) }}"></script>
    @stack('scripts')
</body>
</html>
