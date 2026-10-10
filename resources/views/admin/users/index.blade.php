@extends('layouts.admin')

@section('title', 'Kelola Pengguna')
@section('page_title', 'Kelola Pengguna & Hak Akses')
@section('page_subtitle', 'Manajemen akun petugas, administrator, dan kewenangan akses sistem')

@section('header_actions')
<a href="{{ route('admin.users.create') }}"
   class="inline-flex items-center px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition focus:ring-2 focus:ring-offset-1 focus:ring-blue-600 shadow-sm">
    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
    <span>Tambah Pengguna Baru</span>
</a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Total Pengguna Terdaftar</span>
            <span class="text-xl font-bold text-slate-900 mt-1 block">{{ $users->total() }} Akun</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Akun Anda Saat Ini</span>
            <div class="flex items-center space-x-2 mt-1">
                <span class="text-sm font-bold text-blue-700 truncate">{{ auth()->user()->name }}</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded font-semibold {{ auth()->user()->isAdmin() ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                    {{ auth()->user()->role_label }}
                </span>
            </div>
            <span class="text-[11px] text-slate-400 block mt-0.5">{{ auth()->user()->email }}</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Model Akses Sistem</span>
            <span class="text-sm font-semibold text-emerald-700 mt-1 block">Role & Permission Fleksibel</span>
            <span class="text-[11px] text-slate-400 block">Dukungan admin penuh, petugas wilayah, & kustom</span>
        </div>
    </div>

    <!-- Main Card & Data Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
        <!-- Filter Toolbar -->
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <span class="text-xs font-semibold text-slate-700">Daftar Akun Pengguna</span>

            <!-- Filter & Search Form -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2">
                <!-- Role Filter -->
                <select name="role" onchange="this.form.submit()" class="px-2.5 py-1.5 border border-slate-300 rounded text-xs outline-none focus:border-blue-600 bg-white text-slate-700">
                    <option value="">Semua Peran</option>
                    @foreach($roles as $key => $label)
                        <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <!-- Search Input -->
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau email..."
                       class="px-3 py-1.5 border border-slate-300 rounded text-xs outline-none focus:border-blue-600 w-48 sm:w-56">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-medium transition">
                    Cari
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-slate-500 hover:underline ml-1">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Pengguna</th>
                        <th class="py-3 px-4">Alamat Email</th>
                        <th class="py-3 px-4 text-center">Peran (Role)</th>
                        <th class="py-3 px-4">Cakupan Hak Akses</th>
                        <th class="py-3 px-4">Bergabung</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-center text-slate-400 font-mono">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                    <div>
                                        <span class="font-semibold text-slate-900 block">{{ $user->name }}</span>
                                        @if(auth()->id() === $user->id)
                                            <span class="text-[10px] text-blue-700 font-semibold">(Akun Anda)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-mono text-slate-700">{{ $user->email }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($user->isAdmin())
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                        Administrator
                                    </span>
                                @elseif($user->isPetugasWilayah())
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Petugas Wilayah
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                        Kustom
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                @if($user->isAdmin())
                                    <span class="text-[11px] text-blue-700 font-medium">Akses Penuh Seluruh Modul</span>
                                @elseif($user->isPetugasWilayah())
                                    <span class="inline-flex items-center text-[11px] text-emerald-700 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Khusus Master Data Wilayah
                                    </span>
                                @else
                                    @php
                                        $perms = $user->permissions ?? [];
                                        $permLabels = array_map(function($p) {
                                            return \App\Models\User::PERMISSIONS[$p]['label'] ?? $p;
                                        }, $perms);
                                    @endphp
                                    @if(count($permLabels) > 0)
                                        <span class="text-[11px] text-slate-700" title="{{ implode(', ', $permLabels) }}">
                                            {{ count($permLabels) }} Modul: {{ implode(', ', array_slice($permLabels, 0, 2)) }}{{ count($permLabels) > 2 ? '...' : '' }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Tanpa izin modul</span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="text-blue-700 hover:underline font-medium">
                                        Edit
                                    </a>

                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna {{ $user->name }} ({{ $user->email }})?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-700 hover:underline font-medium">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-300 cursor-not-allowed" title="Tidak dapat menghapus akun sendiri">Hapus</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Tidak ada data akun pengguna yang cocok dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
