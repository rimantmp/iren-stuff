@extends('layouts.admin')

@section('title', 'Edit Administrator: ' . $user->name)
@section('page_title', 'Perbarui Akun Administrator')
@section('page_subtitle', $user->name . ' (' . $user->email . ')')

@section('header_actions')
<a href="{{ route('admin.users.index') }}"
   class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition">
    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    <span>Kembali ke Daftar Admin</span>
</a>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

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
                <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Perbarui Informasi Administrator</h2>
                <p class="text-[11px] text-slate-500">Ubah data identitas atau atur ulang kata sandi pengguna</p>
            </div>
            <span class="text-[11px] font-mono text-slate-500">ID: #{{ $user->id }}</span>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-5 space-y-5 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium text-slate-800 mb-1" for="name">
                    Nama Lengkap Administrator <span class="text-rose-600">*</span>
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

            <div class="pt-3 border-t border-slate-100">
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
@endsection
