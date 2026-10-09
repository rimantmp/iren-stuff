<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Administrator - Sistem Penyaluran Bantuan</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased flex flex-col justify-center">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- Left Column: Official Hero Banner Visual (Desktop & Tablet) -->
        <div class="hidden lg:flex lg:w-7/12 bg-slate-950 relative overflow-hidden flex-col justify-between border-r border-slate-800">
            <!-- Background Hero Image -->
            <img src="{{ asset('images/login-hero.png') }}"
                 alt="Aspirasi Penyaluran Bantuan DPR RI"
                 class="absolute inset-0 w-full h-full object-cover object-center filter brightness-95">

            <!-- Gradient Overlays for Readability -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-slate-950/60 pointer-events-none"></div>

            <!-- Top Header Overlay -->
            <div class="relative z-10 p-8 flex items-center justify-between">
                <div class="flex items-center space-x-3 bg-slate-950/80 backdrop-blur-md px-3.5 py-2 rounded-lg border border-slate-700/60 shadow-lg">
                    <span class="w-8 h-8 rounded bg-blue-600 text-white font-black text-xs flex items-center justify-center tracking-wider shadow-sm">
                        SB
                    </span>
                    <div>
                        <span class="text-xs font-bold text-white uppercase tracking-wider block">Sistem Penyaluran Bantuan</span>
                        <span class="text-[10px] text-slate-300 block">Aspirasi Dapil Sulawesi Selatan III</span>
                    </div>
                </div>

                <!-- <span class="px-2.5 py-1 rounded bg-blue-900/80 backdrop-blur-md text-blue-200 border border-blue-700/60 text-[10px] font-semibold tracking-wide shadow-sm">
                    Portal Resmi Pengelola
                </span> -->
            </div>

            <!-- Bottom Caption & Highlights Card Overlay -->
            <div class="relative z-10 p-8 text-white space-y-3">
                <div class="bg-slate-950/85 backdrop-blur-md p-5 rounded-xl border border-slate-700/70 max-w-xl shadow-2xl">
                    <div class="flex items-center space-x-2 mb-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider">Penyaluran Aspirasi & Logistik Lapangan</span>
                    </div>
                    <p class="text-xs text-slate-200 leading-relaxed">
                        Pengelolaan penyaluran bantuan air bersih, paket sembako, bantuan tunai gereja, dan pengadaan sarana umum untuk masyarakat Kabupaten Toraja Utara serta wilayah sekitarnya.
                    </p>
                    <div class="pt-3 mt-3 border-t border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
                        <span class="flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Integrasi 83.762 Wilayah Kemendagri & Titik GIS</span>
                        </span>
                        <span class="font-mono text-slate-300">Versi 1.2</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Login Form -->
        <div class="flex-1 flex items-center justify-center p-6 sm:p-10 lg:p-12 bg-slate-50">
            <div class="w-full max-w-md">

                <!-- Mobile Hero Banner (Shown only on mobile & small screens) -->
                <div class="mb-5 rounded-xl overflow-hidden border border-slate-200 shadow-sm lg:hidden relative">
                    <img src="{{ asset('images/login-hero.png') }}"
                         alt="Aspirasi Penyaluran Bantuan"
                         class="w-full h-44 sm:h-52 object-cover object-top">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent flex items-end p-3.5">
                        <div class="text-white">
                            <span class="text-xs font-bold block">Sistem Penyaluran Bantuan</span>
                            <span class="text-[10px] text-slate-300 block">Aspirasi Sulawesi Selatan III</span>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="bg-white border border-slate-200 rounded-xl p-6 sm:p-8 shadow-sm">
                    <div class="mb-6">
                        <h2 class="text-lg font-bold text-slate-900">Masuk ke Panel Pengelola</h2>
                        <p class="text-xs text-slate-500 mt-1">Masukkan alamat email dan kata sandi administrator Anda</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-5 p-3.5 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-xs flex items-start space-x-2.5">
                            <svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-medium leading-relaxed">{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="mb-5 p-3.5 rounded-lg border border-blue-200 bg-blue-50 text-blue-800 text-xs flex items-start space-x-2.5">
                            <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="leading-relaxed">{{ session('info') }}</span>
                        </div>
                    @endif

                    <form id="formLogin" action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs">
                        @csrf

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block font-medium text-slate-800 mb-1.5">
                                Alamat Email Administrator
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/></svg>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                       placeholder="nama@email.com"
                                       class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition text-xs text-slate-900 font-medium">
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="block font-medium text-slate-800">
                                    Kata Sandi
                                </label>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" id="password" name="password" required
                                       placeholder="••••••••"
                                       class="w-full pl-9 pr-10 py-2.5 rounded-lg border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none transition text-xs text-slate-900 font-medium">
                                <button type="button" onclick="togglePasswordVisibility()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition focus:outline-none"
                                        title="Tampilkan / Sembunyikan Sandi" aria-label="Toggle password visibility">
                                    <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg id="eyeSlashIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center space-x-2 cursor-pointer select-none">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer">
                                <span class="text-slate-600 font-medium">Ingat perangkat ini</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btnSubmit"
                                class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white font-medium text-xs rounded-lg transition focus:ring-2 focus:ring-offset-1 focus:ring-blue-600 shadow-sm flex items-center justify-center space-x-2">
                            <span>Masuk ke Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </form>
                </div>

                <!-- Footer Security Badge -->
                <div class="text-center mt-6 text-[11px] text-slate-500 flex items-center justify-center space-x-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Koneksi aman terenkripsi. Hak cipta dilindungi.</span>
                </div>

            </div>
        </div>

    </div>

    <script>
        function togglePasswordVisibility() {
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }

        document.getElementById('formLogin').addEventListener('submit', function() {
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<span class="inline-block animate-spin mr-1.5">&#9696;</span> Memproses Verifikasi...';
        });
    </script>
</body>
</html>
