@extends('layouts.admin')

@section('title', 'Kelola Administrator')
@section('page_title', 'Kelola Pengguna Administrator')
@section('page_subtitle', 'Manajemen akun petugas dan administrator sistem penyaluran bantuan')

@section('header_actions')
<a href="{{ route('admin.users.create') }}"
   class="inline-flex items-center px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition focus:ring-2 focus:ring-offset-1 focus:ring-blue-600 shadow-sm">
    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
    <span>Tambah Admin Baru</span>
</a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Total Administrator Terdaftar</span>
            <span class="text-xl font-bold text-slate-900 mt-1 block">{{ $users->total() }} Akun</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Akun Anda Saat Ini</span>
            <span class="text-sm font-bold text-blue-700 mt-1 block truncate">{{ auth()->user()->name }}</span>
            <span class="text-[11px] text-slate-400 block">{{ auth()->user()->email }}</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Hak Akses Sistem</span>
            <span class="text-sm font-semibold text-emerald-700 mt-1 block">Akses Penuh (Full Control)</span>
            <span class="text-[11px] text-slate-400 block">Dapat mengelola data dan pengguna</span>
        </div>
    </div>

    <!-- Main Card & Data Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
        <!-- Filter Toolbar -->
        <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <span class="text-xs font-semibold text-slate-700">Daftar Akun Administrator</span>

            <!-- Search Form -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau email..."
                       class="px-3 py-1.5 border border-slate-300 rounded text-xs outline-none focus:border-blue-600 w-56">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-medium transition">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Administrator</th>
                        <th class="py-3 px-4">Alamat Email</th>
                        <th class="py-3 px-4">Tanggal Bergabung</th>
                        <th class="py-3 px-4 text-center">Status</th>
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
                                    <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs">
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
                            <td class="py-3 px-4 text-slate-500">
                                {{ $user->created_at ? $user->created_at->format('d/m/Y, H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    Aktif
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="text-blue-700 hover:underline font-medium">
                                        Edit
                                    </a>

                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $user->name }} ({{ $user->email }})?')">
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
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Tidak ada akun administrator yang cocok dengan pencarian.
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
