<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - Sistem Penyaluran Bantuan</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            800: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- jQuery & Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        .select2-container--default .select2-selection--single {
            height: 42px;
            border-radius: 0.375rem;
            border-color: #cbd5e1;
            display: flex;
            align-items: center;
            padding-left: 0.75rem;
            font-size: 0.875rem;
            color: #0f172a;
            background-color: #ffffff;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 1px #1d4ed8;
            outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }
        .select2-dropdown {
            border-radius: 0.375rem;
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
    @stack('styles')
</head>
<body class="h-full text-slate-800 antialiased bg-slate-50 flex flex-col font-sans">

    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Sidebar Backdrop -->
        <div id="mobileBackdrop" class="fixed inset-0 z-40 bg-slate-900/50 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-200 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-200 ease-in-out flex flex-col border-r border-slate-800">
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2.5">
                    <span class="w-7 h-7 rounded bg-blue-600 text-white font-bold text-xs flex items-center justify-center">SB</span>
                    <div class="leading-tight">
                        <span class="text-xs font-bold text-white tracking-wider block uppercase">Sistem Bantuan</span>
                        <span class="text-[10px] text-slate-400 block">Penyaluran Wilayah</span>
                    </div>
                </a>
                <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white p-1" aria-label="Tutup menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-xs">
                <!-- Section: Navigasi Utama -->
                <div>
                    <span class="px-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">Menu Utama</span>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center space-x-2.5 px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>
                </div>

                <!-- Section: Program Bantuan -->
                <div>
                    <span class="px-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">Penyaluran Bantuan</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('bantuan.air.index') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('bantuan.air.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-2.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                <span>Air Bersih</span>
                            </div>
                            <span class="text-[10px] px-1.5 py-0.2 text-blue-200 bg-blue-900 rounded font-medium">Utama</span>
                        </a>

                        <a href="{{ route('bantuan.sembako') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('bantuan.sembako') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-2.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                <span>Sembako</span>
                            </div>
                            <span class="text-[10px] px-1.5 py-0.2 text-slate-400 bg-slate-800 rounded font-medium">Segera</span>
                        </a>

                        <a href="{{ route('bantuan.tunai-gereja') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('bantuan.tunai-gereja') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-2.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>Tunai Gereja</span>
                            </div>
                            <span class="text-[10px] px-1.5 py-0.2 text-slate-400 bg-slate-800 rounded font-medium">Segera</span>
                        </a>

                        <a href="{{ route('bantuan.pengadaan') }}"
                           class="flex items-center justify-between px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('bantuan.pengadaan') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-2.5">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span>Pengadaan</span>
                            </div>
                            <span class="text-[10px] px-1.5 py-0.2 text-slate-400 bg-slate-800 rounded font-medium">Segera</span>
                        </a>

                        <a href="{{ route('bantuan.jenis.index') }}"
                           class="flex items-center space-x-2.5 px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('bantuan.jenis.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Kelola Jenis Bantuan</span>
                        </a>
                    </div>
                </div>

                <!-- Section: Master Wilayah -->
                <div>
                    <span class="px-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">Master Wilayah</span>
                    <div class="space-y-0.5">
                        <a href="{{ route('master.provinsi') }}"
                           class="flex items-center space-x-2.5 px-3 py-1.5 rounded-md font-medium transition {{ request()->routeIs('master.provinsi') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('master.provinsi') ? 'bg-blue-400' : 'bg-slate-600' }}"></span>
                            <span>Provinsi</span>
                        </a>
                        <a href="{{ route('master.kota') }}"
                           class="flex items-center space-x-2.5 px-3 py-1.5 rounded-md font-medium transition {{ request()->routeIs('master.kota') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('master.kota') ? 'bg-blue-400' : 'bg-slate-600' }}"></span>
                            <span>Kota dan Kabupaten</span>
                        </a>
                        <a href="{{ route('master.kecamatan') }}"
                           class="flex items-center space-x-2.5 px-3 py-1.5 rounded-md font-medium transition {{ request()->routeIs('master.kecamatan') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('master.kecamatan') ? 'bg-blue-400' : 'bg-slate-600' }}"></span>
                            <span>Kecamatan</span>
                        </a>
                        <a href="{{ route('master.kelurahan') }}"
                           class="flex items-center space-x-2.5 px-3 py-1.5 rounded-md font-medium transition {{ request()->routeIs('master.kelurahan') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-slate-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('master.kelurahan') ? 'bg-blue-400' : 'bg-slate-600' }}"></span>
                            <span>Kelurahan dan Desa</span>
                        </a>
                    </div>
                </div>

                <!-- Section: Laporan -->
                <div>
                    <span class="px-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">Laporan</span>
                    <a href="{{ route('rekap.index') }}"
                       class="flex items-center space-x-2.5 px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('rekap.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Rekapitulasi Penyaluran</span>
                    </a>
                </div>

                <!-- Section: Pengaturan Akun -->
                <div>
                    <span class="px-3 text-[10px] font-semibold text-slate-400 uppercase tracking-wider block mb-1">Pengaturan & Akun</span>
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center space-x-2.5 px-3 py-2 rounded-md font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>Kelola Administrator</span>
                    </a>
                </div>
            </nav>

            <!-- User Info & Logout -->
            <div class="p-3 border-t border-slate-800 bg-slate-950">
                <div class="flex items-center justify-between">
                    <div class="overflow-hidden pr-2">
                        <span class="text-xs font-semibold text-white block truncate">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <span class="text-[10px] text-slate-400 block truncate">{{ auth()->user()->email ?? 'admin@bantuan.id' }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded transition" aria-label="Keluar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- Header -->
            <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center space-x-3">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-600 hover:text-slate-900 p-1" aria-label="Buka menu navigasi">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="text-sm sm:text-base font-semibold text-slate-900 leading-tight">@yield('page_title', 'Dashboard')</h1>
                        <p class="text-[11px] text-slate-500 hidden sm:block">@yield('page_subtitle', 'Sistem Administrasi Penyaluran Bantuan')</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    @yield('header_actions')
                </div>
            </header>

            <!-- Flash Alert Messages -->
            <div class="px-4 sm:px-6 pt-4">
                @if(session('success'))
                    <div class="p-3.5 rounded border border-emerald-300 bg-emerald-50 text-emerald-900 flex items-start justify-between text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-emerald-700">Sukses:</span>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 font-bold ml-2" aria-label="Tutup pesan">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-3.5 rounded border border-rose-300 bg-rose-50 text-rose-900 flex items-start justify-between text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-rose-700">Kesalahan:</span>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 font-bold ml-2" aria-label="Tutup pesan">&times;</button>
                    </div>
                @endif
            </div>

            <!-- Page Body -->
            <main class="flex-1 p-4 sm:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobileBackdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
