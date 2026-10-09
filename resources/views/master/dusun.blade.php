@extends('layouts.admin')

@section('title', 'Master Data Dusun')
@section('page_title', 'Master Data Dusun & Lingkungan')
@section('page_subtitle', 'Kelola daftar dusun, dukuh, dan rukun warga di tingkat kelurahan/desa')

@section('content')
<div class="space-y-6">

    <!-- Flash Notification -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-xs flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg text-rose-800 text-xs">
            <span class="font-bold block mb-1">Gagal menyimpan data:</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
        <!-- Toolbar & Filter -->
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="flex items-center space-x-3">
                <span class="text-xs font-medium text-slate-500">
                    Total: <b class="text-slate-800">{{ number_format($items->total(), 0, ',', '.') }}</b> Dusun
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <!-- Search & Filter Form -->
                <form method="GET" action="{{ route('master.dusun') }}" class="flex flex-wrap items-center gap-2">
                    <select name="kota_id" onchange="this.form.submit()" class="px-2.5 py-1.5 border border-slate-300 rounded text-xs outline-none bg-white">
                        <option value="">Semua Kabupaten / Kota</option>
                        @foreach($activeKotaList as $kota)
                            <option value="{{ $kota->id }}" {{ request('kota_id') == $kota->id ? 'selected' : '' }}>
                                {{ $kota->nama }}
                            </option>
                        @endforeach
                    </select>

                    @if(isset($kecamatanList) && $kecamatanList->isNotEmpty())
                        <select name="kecamatan_id" onchange="this.form.submit()" class="px-2.5 py-1.5 border border-slate-300 rounded text-xs outline-none bg-white">
                            <option value="">Semua Kecamatan</option>
                            @foreach($kecamatanList as $kec)
                                <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>
                                    {{ $kec->nama }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari dusun, desa, kepala..."
                           class="px-3 py-1.5 border border-slate-300 rounded text-xs outline-none focus:border-blue-600 w-44 sm:w-56">
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-medium transition">
                        Cari
                    </button>
                    @if(request('search') || request('kota_id') || request('kecamatan_id'))
                        <a href="{{ route('master.dusun') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                    @endif
                </form>

                <!-- Tombol Tambah Dusun -->
                <button type="button" onclick="openCreateModal()"
                        class="px-3.5 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition flex items-center justify-center space-x-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Dusun</span>
                </button>
            </div>
        </div>

        <!-- Tabel Dusun -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <th class="py-2.5 px-4 w-12 text-center">No</th>
                        <th class="py-2.5 px-4">Nama Dusun</th>
                        <th class="py-2.5 px-4">Kelurahan / Lembang</th>
                        <th class="py-2.5 px-4">Kepala Dusun</th>
                        <th class="py-2.5 px-4">Wilayah RT / RW</th>
                        <th class="py-2.5 px-4">Koordinat (GIS)</th>
                        <th class="py-2.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 text-center text-slate-400 font-mono">
                                {{ $items->firstItem() + $index }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-slate-900 block">{{ $item->nama }}</span>
                                @if($item->keterangan)
                                    <span class="text-[11px] text-slate-500 block truncate max-w-xs">{{ $item->keterangan }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($item->kelurahan)
                                    <span class="font-medium text-slate-800 block">{{ $item->kelurahan->nama }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $item->kelurahan_id }}</span>
                                @else
                                    <span class="text-slate-400 font-mono">{{ $item->kelurahan_id }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($item->kepala_dusun)
                                    <div class="flex items-center space-x-1.5 text-slate-800">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span>{{ $item->kepala_dusun }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-1">
                                    @if($item->rt)
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-medium">{{ $item->rt }}</span>
                                    @endif
                                    @if($item->rw)
                                        <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-medium">{{ $item->rw }}</span>
                                    @endif
                                    @if(!$item->rt && !$item->rw)
                                        <span class="text-slate-400 italic">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px]">
                                @if($item->latitude && $item->longitude)
                                    <div class="flex items-center space-x-1.5">
                                        <span class="text-slate-600">{{ number_format($item->latitude, 5) }}, {{ number_format($item->longitude, 5) }}</span>
                                        <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                           target="_blank" rel="noopener"
                                           class="text-blue-600 hover:text-blue-800" title="Buka Google Maps">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->nama) }}', '{{ $item->kelurahan_id }}', '{{ addslashes($item->kelurahan->nama ?? '') }}', '{{ addslashes($item->rt ?? '') }}', '{{ addslashes($item->rw ?? '') }}', '{{ addslashes($item->kepala_dusun ?? '') }}', '{{ $item->latitude ?? '' }}', '{{ $item->longitude ?? '' }}', '{{ addslashes($item->keterangan ?? '') }}')"
                                            class="px-2 py-1 text-blue-700 hover:bg-blue-50 rounded transition font-medium">
                                        Edit
                                    </button>
                                    <form action="{{ route('master.dusun.destroy', $item->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus dusun {{ addslashes($item->nama) }}?');"
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 text-rose-600 hover:bg-rose-50 rounded transition font-medium">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="max-w-xs mx-auto text-slate-400 space-y-2">
                                    <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <p class="font-medium text-slate-600 text-xs">Belum ada data dusun</p>
                                    <p class="text-[11px] text-slate-400">Tambahkan data dusun pertama untuk memudahkan pemetaan lokasi distribusi bantuan.</p>
                                    <button type="button" onclick="openCreateModal()"
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition shadow-sm mt-2">
                                        + Tambah Dusun Sekarang
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-3 border-t border-slate-200">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah Dusun -->
<div id="modalCreate" class="fixed inset-0 bg-slate-900/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-lg w-full p-5 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Dusun Baru</span>
            </h3>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
        </div>

        <form action="{{ route('master.dusun.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf

            <!-- Filter Wilayah Pencarian: Kabupaten & Kecamatan -->
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg space-y-2.5">
                <div class="flex items-center gap-1.5 text-slate-700 font-semibold text-[11px]">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter Wilayah (Kabupaten & Kecamatan)</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label for="create_filter_kota_id" class="block font-medium text-slate-600 text-[11px] mb-1">
                            1. Kabupaten / Kota
                        </label>
                        <select id="create_filter_kota_id" class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-blue-600 bg-white text-xs">
                            <option value="">-- Semua Kab/Kota Aktif --</option>
                            @foreach($activeKotaList as $kota)
                                <option value="{{ $kota->id }}" {{ $kota->id == '7326' ? 'selected' : '' }}>
                                    {{ $kota->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="create_filter_kecamatan_id" class="block font-medium text-slate-600 text-[11px] mb-1">
                            2. Kecamatan
                        </label>
                        <select id="create_filter_kecamatan_id" class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-blue-600 bg-white text-xs">
                            <option value="">-- Semua Kecamatan --</option>
                        </select>
                    </div>
                </div>
                <span class="text-[10px] text-slate-500 block">Pilih kabupaten dan kecamatan untuk mempersempit daftar pilihan desa/kelurahan di bawah.</span>
            </div>

            <!-- Kelurahan / Desa Select2 -->
            <div>
                <label for="create_kelurahan_id" class="block font-medium text-slate-700 mb-1">
                    Kelurahan / Lembang <span class="text-rose-600">*</span>
                </label>
                <select id="create_kelurahan_id" name="kelurahan_id" class="w-full" required style="width: 100%;">
                    <option value="">Ketik nama atau kode kelurahan...</option>
                </select>
                <span class="text-[10px] text-slate-500 mt-1 block">Ketik minimal 2 huruf untuk mencari desa / kelurahan</span>
            </div>

            <!-- Nama Dusun -->
            <div>
                <label for="create_nama" class="block font-medium text-slate-700 mb-1">
                    Nama Dusun / Lingkungan <span class="text-rose-600">*</span>
                </label>
                <input type="text" id="create_nama" name="nama" required placeholder="Contoh: Dusun Karassik, Dusun Ba'tan"
                       class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs">
            </div>

            <!-- Kepala Dusun -->
            <div>
                <label for="create_kepala_dusun" class="block font-medium text-slate-700 mb-1">
                    Kepala Dusun / Tokoh Masyarakat
                </label>
                <input type="text" id="create_kepala_dusun" name="kepala_dusun" placeholder="Nama Kepala Dusun (Opsional)"
                       class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs">
            </div>

            <!-- RT & RW -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="create_rt" class="block font-medium text-slate-700 mb-1">RT</label>
                    <input type="text" id="create_rt" name="rt" placeholder="Contoh: RT 01"
                           class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs">
                </div>
                <div>
                    <label for="create_rw" class="block font-medium text-slate-700 mb-1">RW</label>
                    <input type="text" id="create_rw" name="rw" placeholder="Contoh: RW 02"
                           class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs">
                </div>
            </div>

            <!-- Koordinat -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="create_lat" class="block font-medium text-slate-700 mb-1">Latitude</label>
                    <input type="number" step="any" id="create_lat" name="latitude" placeholder="-2.97566"
                           class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 font-mono text-xs">
                </div>
                <div>
                    <label for="create_lng" class="block font-medium text-slate-700 mb-1">Longitude</label>
                    <input type="number" step="any" id="create_lng" name="longitude" placeholder="119.89841"
                           class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 font-mono text-xs">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label for="create_keterangan" class="block font-medium text-slate-700 mb-1">Keterangan / Akses Lokasi</label>
                <textarea id="create_keterangan" name="keterangan" rows="2" placeholder="Catatan tambahan mengenai batas dusun atau patokan jalan..."
                          class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                <button type="button" onclick="closeCreateModal()" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-medium transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded font-medium transition shadow-sm">
                    Simpan Dusun
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Dusun -->
<div id="modalEdit" class="fixed inset-0 bg-slate-900/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-lg w-full p-5 shadow-xl border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Data Dusun</span>
            </h3>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
        </div>

        <form id="formEditDusun" method="POST" class="space-y-3.5 text-xs">
            @csrf
            @method('PUT')

            <!-- Filter Wilayah Pencarian: Kabupaten & Kecamatan -->
            <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg space-y-2.5">
                <div class="flex items-center gap-1.5 text-slate-700 font-semibold text-[11px]">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter Wilayah (Kabupaten & Kecamatan)</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label for="edit_filter_kota_id" class="block font-medium text-slate-600 text-[11px] mb-1">
                            1. Kabupaten / Kota
                        </label>
                        <select id="edit_filter_kota_id" class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-blue-600 bg-white text-xs">
                            <option value="">-- Semua Kab/Kota Aktif --</option>
                            @foreach($activeKotaList as $kota)
                                <option value="{{ $kota->id }}">
                                    {{ $kota->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="edit_filter_kecamatan_id" class="block font-medium text-slate-600 text-[11px] mb-1">
                            2. Kecamatan
                        </label>
                        <select id="edit_filter_kecamatan_id" class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-blue-600 bg-white text-xs">
                            <option value="">-- Semua Kecamatan --</option>
                        </select>
                    </div>
                </div>
                <span class="text-[10px] text-slate-500 block">Pilih kabupaten dan kecamatan untuk mempersempit daftar pilihan desa/kelurahan di bawah.</span>
            </div>

            <!-- Kelurahan / Desa Select2 -->
            <div>
                <label for="edit_kelurahan_id" class="block font-medium text-slate-700 mb-1">
                    Kelurahan / Lembang <span class="text-rose-600">*</span>
                </label>
                <select id="edit_kelurahan_id" name="kelurahan_id" class="w-full" required style="width: 100%;">
                    <option value="">Ketik nama atau kode kelurahan...</option>
                </select>
            </div>

            <!-- Nama Dusun -->
            <div>
                <label for="edit_nama" class="block font-medium text-slate-700 mb-1">
                    Nama Dusun / Lingkungan <span class="text-rose-600">*</span>
                </label>
                <input type="text" id="edit_nama" name="nama" required
                       class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs">
            </div>

            <!-- Kepala Dusun -->
            <div>
                <label for="edit_kepala_dusun" class="block font-medium text-slate-700 mb-1">
                    Kepala Dusun / Tokoh Masyarakat
                </label>
                <input type="text" id="edit_kepala_dusun" name="kepala_dusun"
                       class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs">
            </div>

            <!-- RT & RW -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="edit_rt" class="block font-medium text-slate-700 mb-1">RT</label>
                    <input type="text" id="edit_rt" name="rt"
                           class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs">
                </div>
                <div>
                    <label for="edit_rw" class="block font-medium text-slate-700 mb-1">RW</label>
                    <input type="text" id="edit_rw" name="rw"
                           class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs">
                </div>
            </div>

            <!-- Koordinat -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="edit_lat" class="block font-medium text-slate-700 mb-1">Latitude</label>
                    <input type="number" step="any" id="edit_lat" name="latitude"
                           class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 font-mono text-xs">
                </div>
                <div>
                    <label for="edit_lng" class="block font-medium text-slate-700 mb-1">Longitude</label>
                    <input type="number" step="any" id="edit_lng" name="longitude"
                           class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 font-mono text-xs">
                </div>
            </div>

            <!-- Keterangan -->
            <div>
                <label for="edit_keterangan" class="block font-medium text-slate-700 mb-1">Keterangan / Akses Lokasi</label>
                <textarea id="edit_keterangan" name="keterangan" rows="2"
                          class="w-full px-3 py-2 border border-slate-300 rounded outline-none focus:border-blue-600 text-xs"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                <button type="button" onclick="closeEditModal()" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-medium transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded font-medium transition shadow-sm">
                    Perbarui Dusun
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const searchKelurahanUrl = "{{ route('wilayah.kelurahan-search') }}";
    const urlKecamatan = "{{ url('wilayah/kecamatan') }}";

    function loadKecamatanOptions(kotaId, selectElement, selectedKecId = '', callback = null) {
        const $select = $(selectElement);
        $select.empty().append('<option value="">-- Semua Kecamatan --</option>');

        if (!kotaId) {
            if (callback) callback();
            return;
        }

        $.getJSON(`${urlKecamatan}/${kotaId}`, function(data) {
            if (data && data.results) {
                data.results.forEach(function(item) {
                    const isSelected = selectedKecId && String(selectedKecId) === String(item.id);
                    $select.append(new Option(item.text, item.id, isSelected, isSelected));
                });
            }
            if (callback) callback();
        });
    }

    $(document).ready(function() {
        // Load initial kecamatan for create modal if kota is pre-selected
        const initialCreateKota = $('#create_filter_kota_id').val();
        if (initialCreateKota) {
            loadKecamatanOptions(initialCreateKota, '#create_filter_kecamatan_id');
        }

        // Init Select2 AJAX for Create Modal
        $('#create_kelurahan_id').select2({
            dropdownParent: $('#modalCreate'),
            placeholder: 'Ketik nama kelurahan atau kode...',
            allowClear: true,
            ajax: {
                url: searchKelurahanUrl,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term,
                    kota_id: $('#create_filter_kota_id').val(),
                    kecamatan_id: $('#create_filter_kecamatan_id').val()
                }),
                processResults: data => ({ results: data.results })
            }
        });

        // Event: Ganti Kabupaten pada Create Modal
        $('#create_filter_kota_id').on('change', function() {
            loadKecamatanOptions(this.value, '#create_filter_kecamatan_id');
            $('#create_kelurahan_id').val(null).trigger('change');
        });

        // Event: Ganti Kecamatan pada Create Modal
        $('#create_filter_kecamatan_id').on('change', function() {
            $('#create_kelurahan_id').val(null).trigger('change');
        });

        // Set coordinates automatically when kelurahan selected in create modal
        $('#create_kelurahan_id').on('select2:select', function(e) {
            const data = e.params.data;
            if (data.latitude && !$('#create_lat').val()) {
                $('#create_lat').val(data.latitude);
            }
            if (data.longitude && !$('#create_lng').val()) {
                $('#create_lng').val(data.longitude);
            }
        });

        // Init Select2 AJAX for Edit Modal
        $('#edit_kelurahan_id').select2({
            dropdownParent: $('#modalEdit'),
            placeholder: 'Ketik nama kelurahan atau kode...',
            allowClear: true,
            ajax: {
                url: searchKelurahanUrl,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term,
                    kota_id: $('#edit_filter_kota_id').val(),
                    kecamatan_id: $('#edit_filter_kecamatan_id').val()
                }),
                processResults: data => ({ results: data.results })
            }
        });

        // Event: Ganti Kabupaten pada Edit Modal
        $('#edit_filter_kota_id').on('change', function() {
            loadKecamatanOptions(this.value, '#edit_filter_kecamatan_id');
            $('#edit_kelurahan_id').val(null).trigger('change');
        });

        // Event: Ganti Kecamatan pada Edit Modal
        $('#edit_filter_kecamatan_id').on('change', function() {
            $('#edit_kelurahan_id').val(null).trigger('change');
        });
    });

    function openCreateModal() {
        $('#modalCreate').removeClass('hidden');
    }

    function closeCreateModal() {
        $('#modalCreate').addClass('hidden');
    }

    function openEditModal(id, nama, kelurahanId, kelurahanNama, rt, rw, kepalaDusun, lat, lng, keterangan) {
        $('#formEditDusun').attr('action', `/master/dusun/${id}`);
        $('#edit_nama').val(nama);
        $('#edit_rt').val(rt);
        $('#edit_rw').val(rw);
        $('#edit_kepala_dusun').val(kepalaDusun);
        $('#edit_lat').val(lat);
        $('#edit_lng').val(lng);
        $('#edit_keterangan').val(keterangan);

        // Pre-select kabupaten & kecamatan in edit modal
        const kotaId = (kelurahanId && kelurahanId.length >= 4) ? kelurahanId.substring(0, 4) : '';
        const kecId = (kelurahanId && kelurahanId.length >= 6) ? kelurahanId.substring(0, 6) : '';

        $('#edit_filter_kota_id').val(kotaId);
        loadKecamatanOptions(kotaId, '#edit_filter_kecamatan_id', kecId);

        // Pre-select kelurahan in edit modal Select2
        if (kelurahanId) {
            const label = kelurahanNama ? `${kelurahanNama} (Kode: ${kelurahanId})` : kelurahanId;
            const option = new Option(label, kelurahanId, true, true);
            $('#edit_kelurahan_id').empty().append(option).trigger('change');
        } else {
            $('#edit_kelurahan_id').empty().trigger('change');
        }

        $('#modalEdit').removeClass('hidden');
    }

    function closeEditModal() {
        $('#modalEdit').addClass('hidden');
    }
</script>
@endpush
@endsection
