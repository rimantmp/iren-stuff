@extends('layouts.admin')

@section('title', 'Edit Pengguna: ' . $user->name)
@section('page_title', 'Perbarui Akun & Hak Akses Pengguna')
@section('page_subtitle', $user->name . ' (' . $user->email . ')')

@section('header_actions')
<a href="{{ route('admin.users.index') }}"
   class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition">
    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    <span>Kembali ke Daftar Pengguna</span>
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    @if($errors->any())
        <div class="p-4 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-xs">
            <div class="flex items-center space-x-2 font-semibold text-rose-900 mb-1.5">
                <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Mohon periksa data yang belum sesuai:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Perbarui Informasi & Hak Akses Pengguna</h2>
                <p class="text-[11px] text-slate-500">Ubah identitas, peran kewenangan, atau atur ulang kata sandi</p>
            </div>
            <span class="text-[11px] font-mono text-slate-500">ID: #{{ $user->id }}</span>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-5 space-y-5 text-xs">
            @csrf
            @method('PUT')

            <!-- Section 1: Informasi Akun -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-900 pb-1 border-b border-slate-100 flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 inline-flex items-center justify-center text-[10px]">1</span>
                    <span>Informasi Akun</span>
                </h3>

                <div>
                    <label class="block font-medium text-slate-800 mb-1" for="name">
                        Nama Lengkap Pengguna <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition">
                </div>

                <div>
                    <label class="block font-medium text-slate-800 mb-1" for="email">
                        Alamat Email <span class="text-rose-600">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition">
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-800 block mb-1">Ubah Kata Sandi (Opsional)</span>
                    <span class="text-[11px] text-slate-500 block mb-3">Biarkan kolom di bawah kosong jika Anda tidak ingin mengubah kata sandi saat ini.</span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="password">
                                Kata Sandi Baru
                            </label>
                            <input type="password" id="password" name="password" minlength="6"
                                   placeholder="Kosongkan jika tetap"
                                   class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="password_confirmation">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" minlength="6"
                                   placeholder="Ulangi kata sandi baru"
                                   class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Pilihan Peran / Role -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-bold text-slate-900 pb-1 border-b border-slate-100 flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 inline-flex items-center justify-center text-[10px]">2</span>
                    <span>Pilihan Peran & Hak Akses (Role)</span>
                </h3>

                @php
                    $isSelfAdmin = auth()->id() === $user->id && $user->isAdmin();
                    $currentRole = old('role', $user->role ?? 'admin');
                    $currentPermissions = old('permissions', $user->permissions ?? []);
                @endphp

                @if($isSelfAdmin)
                    <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 rounded text-[11px] leading-relaxed">
                        <strong>Catatan Keamanan:</strong> Ini adalah akun Anda sendiri yang sedang aktif digunakan sebagai Administrator. Anda tidak dapat menurunkan peran akun Anda sendiri agar tidak terkunci dari sistem.
                    </div>
                    <input type="hidden" name="role" value="admin">
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Option 1: Admin -->
                    <label class="relative flex flex-col p-3 rounded-lg border transition hover:border-blue-500 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-blue-600 {{ $isSelfAdmin ? 'cursor-default' : 'cursor-pointer' }}">
                        <div class="flex items-center space-x-2.5 mb-1.5">
                            <input type="radio" name="role" value="admin" class="text-blue-600 focus:ring-blue-500"
                                   {{ $currentRole === 'admin' ? 'checked' : '' }}
                                   {{ $isSelfAdmin ? 'disabled' : '' }}
                                   onchange="handleRoleChange(this.value)">
                            <span class="font-bold text-slate-900 text-xs">Administrator</span>
                        </div>
                        <span class="text-[11px] text-slate-600 leading-relaxed">
                            Akses penuh ke semua modul sistem termasuk manajemen pengguna dan backup data.
                        </span>
                    </label>

                    <!-- Option 2: Petugas Wilayah -->
                    <label class="relative flex flex-col p-3 rounded-lg border transition hover:border-emerald-500 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-1 has-[:checked]:ring-emerald-600 {{ $isSelfAdmin ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                        <div class="flex items-center space-x-2.5 mb-1.5">
                            <input type="radio" name="role" value="petugas_wilayah" class="text-emerald-600 focus:ring-emerald-500"
                                   {{ $currentRole === 'petugas_wilayah' ? 'checked' : '' }}
                                   {{ $isSelfAdmin ? 'disabled' : '' }}
                                   onchange="handleRoleChange(this.value)">
                            <span class="font-bold text-slate-900 text-xs">Petugas Wilayah</span>
                        </div>
                        <span class="text-[11px] text-slate-600 leading-relaxed">
                            Hanya mengelola master wilayah (Provinsi, Kota, Kecamatan, Kelurahan, Dusun).
                        </span>
                    </label>

                    <!-- Option 3: Custom -->
                    <label class="relative flex flex-col p-3 rounded-lg border transition hover:border-amber-500 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50 has-[:checked]:ring-1 has-[:checked]:ring-amber-600 {{ $isSelfAdmin ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
                        <div class="flex items-center space-x-2.5 mb-1.5">
                            <input type="radio" name="role" value="custom" class="text-amber-600 focus:ring-amber-500"
                                   {{ $currentRole === 'custom' ? 'checked' : '' }}
                                   {{ $isSelfAdmin ? 'disabled' : '' }}
                                   onchange="handleRoleChange(this.value)">
                            <span class="font-bold text-slate-900 text-xs">Kustom (Granular)</span>
                        </div>
                        <span class="text-[11px] text-slate-600 leading-relaxed">
                            Tentukan hak akses mandiri dengan mencentang modul yang diizinkan di bawah.
                        </span>
                    </label>
                </div>
            </div>

            <!-- Section 3: Izin Granular (Conditional / Interactive) -->
            <div id="permissionsWrapper" class="space-y-3 pt-2 {{ $currentRole === 'custom' && ! $isSelfAdmin ? '' : 'hidden' }}">
                <h3 class="text-xs font-bold text-slate-900 pb-1 border-b border-slate-100 flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 inline-flex items-center justify-center text-[10px]">3</span>
                    <span>Pilihan Hak Akses Modul Spesifik</span>
                </h3>

                <div class="p-3 bg-slate-50 border border-slate-200 rounded-md">
                    <p class="text-[11px] text-slate-600 mb-3">Centang modul yang diizinkan untuk diakses oleh pengguna ini:</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($permissions as $key => $meta)
                            <label class="flex items-start space-x-2.5 p-2 rounded bg-white border border-slate-200 hover:border-slate-300 cursor-pointer">
                                <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                       class="mt-0.5 text-blue-600 rounded focus:ring-blue-500"
                                       {{ in_array($key, $currentPermissions, true) ? 'checked' : '' }}>
                                <div>
                                    <span class="font-semibold text-slate-900 block text-xs">{{ $meta['label'] }}</span>
                                    <span class="text-[10px] text-slate-500 block leading-tight">{{ $meta['desc'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Dynamic Helper Banner -->
            <div id="roleInfoBanner" class="p-3 rounded text-[11px] leading-relaxed transition">
                <!-- Content will be managed by JS -->
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('admin.users.index') }}" class="text-slate-600 hover:underline">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded font-medium transition focus:ring-2 focus:ring-offset-1 focus:ring-blue-600 shadow-sm flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function handleRoleChange(selectedRole) {
        const permWrapper = document.getElementById('permissionsWrapper');
        const infoBanner = document.getElementById('roleInfoBanner');

        if (selectedRole === 'custom') {
            permWrapper.classList.remove('hidden');
            infoBanner.className = 'p-3 rounded text-[11px] bg-amber-50 border border-amber-200 text-amber-900';
            infoBanner.innerHTML = '<strong>Peran Kustom Aktif:</strong> Silakan pilih satu atau lebih modul di atas yang diizinkan untuk diakses akun ini.';
        } else if (selectedRole === 'petugas_wilayah') {
            permWrapper.classList.add('hidden');
            infoBanner.className = 'p-3 rounded text-[11px] bg-emerald-50 border border-emerald-200 text-emerald-900';
            infoBanner.innerHTML = '<strong>Petugas Wilayah:</strong> Akun ini secara otomatis hanya diberikan akses ke <strong>Master Data Wilayah</strong> (Provinsi, Kota/Kabupaten, Kecamatan, Kelurahan/Desa, dan Dusun). Akun tidak dapat mengakses modul penyaluran air, laporan rekap, akun admin lain, atau backup sistem.';
        } else {
            permWrapper.classList.add('hidden');
            infoBanner.className = 'p-3 rounded text-[11px] bg-blue-50 border border-blue-200 text-blue-900';
            infoBanner.innerHTML = '<strong>Administrator:</strong> Akun memiliki akses penuh (Full Control) ke seluruh modul operasional, data master, pelaporan, dan pengaturan sistem.';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkedRadio = document.querySelector('input[name="role"]:checked');
        if (checkedRadio) {
            handleRoleChange(checkedRadio.value);
        } else {
            handleRoleChange('{{ $currentRole }}');
        }
    });
</script>
@endsection
