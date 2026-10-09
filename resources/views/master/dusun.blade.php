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
                    @if(request('search') || request('kota_id') || request('kecamatan_id') || request('kelurahan_id'))
                        <a href="{{ route('master.dusun') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                    @endif
                </form>

                <!-- Tombol Tambah Dusun -->
                <button type="button" onclick="openCreateModal()"
                        class="px-3.5 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition flex items-center justify-center space-x-1.5 shadow-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Dusun</span>
                </button>

                <!-- Tombol Tambah Banyak Dusun (Batch) -->
                <button type="button" onclick="openBatchCreateModal()"
                        class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs font-medium transition flex items-center justify-center space-x-1.5 shadow-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Tambah Banyak Dusun</span>
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
                                    <button type="button"
                                            onclick="confirmDeleteDusun('{{ $item->id }}', '{{ addslashes($item->nama) }}')"
                                            class="px-2 py-1 text-rose-600 hover:bg-rose-50 rounded transition font-medium cursor-pointer">
                                        Hapus
                                    </button>
                                    <form id="formDeleteDusun_{{ $item->id }}" action="{{ route('master.dusun.destroy', $item->id) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
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

            <!-- Switch to Batch -->
            <div class="p-2.5 bg-emerald-50 border border-emerald-200 rounded-lg text-emerald-800 text-[11px] flex items-center justify-between">
                <span>Ingin menambah banyak dusun sekaligus di satu kelurahan?</span>
                <button type="button" onclick="closeCreateModal(); openBatchCreateModal();" class="text-emerald-700 font-semibold underline hover:text-emerald-900 cursor-pointer">
                    Tambah Banyak &rarr;
                </button>
            </div>

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

<!-- Modal Tambah Banyak Dusun Sekaligus (Batch) -->
<div id="modalBatchCreate" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-xl max-w-4xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
        <!-- Header -->
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Tambah Banyak Dusun Sekaligus</h3>
                    <p class="text-[11px] text-slate-500">Tambahkan beberapa dusun/lingkungan sekaligus ke dalam satu kelurahan/lembang terpilih.</p>
                </div>
            </div>
            <button type="button" onclick="closeBatchCreateModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold leading-none p-1">&times;</button>
        </div>

        <!-- Body Scrollable -->
        <form id="formBatchCreate" action="{{ route('master.dusun.store-batch') }}" method="POST" class="flex-1 overflow-y-auto p-5 space-y-4 text-xs">
            @csrf

            <!-- Banner Switch ke Satuan -->
            <div class="p-2.5 bg-emerald-50/80 border border-emerald-200/90 rounded-lg text-emerald-800 flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 text-[11px]">
                <div class="flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pilih kelurahan/lembang tujuan, lalu isi atau tempel daftar dusun di bawah.</span>
                </div>
                <button type="button" onclick="closeBatchCreateModal(); openCreateModal();" class="text-emerald-700 font-semibold underline hover:text-emerald-900 whitespace-nowrap cursor-pointer">
                    Beralih ke Input Satuan &rarr;
                </button>
            </div>

            <!-- Pilih Wilayah Tujuan (Kabupaten, Kecamatan, Kelurahan) -->
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        1. Pilih Wilayah Kelurahan / Lembang Tujuan
                    </span>
                    <span class="text-[10px] text-slate-500 font-medium">Contoh: Toraja Utara &rarr; Rantepao &rarr; Laang Tanduk</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label for="batch_filter_kota_id" class="block font-medium text-slate-700 text-[11px] mb-1">
                            Kabupaten / Kota <span class="text-rose-600">*</span>
                        </label>
                        <select id="batch_filter_kota_id" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg outline-none focus:border-blue-600 bg-white text-xs">
                            <option value="">-- Pilih Kab/Kota --</option>
                            @foreach($activeKotaList as $kota)
                                <option value="{{ $kota->id }}" {{ $kota->id == '7326' ? 'selected' : '' }}>
                                    {{ $kota->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="batch_filter_kecamatan_id" class="block font-medium text-slate-700 text-[11px] mb-1">
                            Kecamatan <span class="text-rose-600">*</span>
                        </label>
                        <select id="batch_filter_kecamatan_id" class="w-full px-2.5 py-1.5 border border-slate-300 rounded-lg outline-none focus:border-blue-600 bg-white text-xs">
                            <option value="">-- Semua Kecamatan --</option>
                        </select>
                    </div>

                    <div>
                        <label for="batch_kelurahan_id" class="block font-medium text-slate-700 text-[11px] mb-1">
                            Kelurahan / Lembang <span class="text-rose-600">*</span>
                        </label>
                        <select id="batch_kelurahan_id" name="kelurahan_id" required class="w-full" style="width: 100%;">
                            <option value="">Ketik nama kelurahan...</option>
                        </select>
                    </div>
                </div>

                <div id="batch_kelurahan_badge" class="hidden p-2.5 bg-blue-50 border border-blue-200 rounded-lg text-blue-900 text-[11px] flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Kelurahan Terpilih: <strong id="batch_kelurahan_text">-</strong></span>
                    </span>
                    <span class="text-[10px] text-blue-600 font-mono font-medium" id="batch_kelurahan_code"></span>
                </div>
            </div>

            <!-- Opsi Tempel Cepat (Quick Paste) -->
            <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
                <button type="button" onclick="toggleQuickPaste()" class="w-full px-3.5 py-2.5 flex items-center justify-between text-left text-slate-700 hover:bg-slate-100/70 transition cursor-pointer">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span class="font-bold text-[11px] uppercase tracking-wider text-slate-800">2. Fitur Cepat: Tempel Banyak Nama Dusun Sekaligus (Opsional)</span>
                    </div>
                    <div class="flex items-center space-x-1.5 text-[11px] text-slate-500 font-medium">
                        <span id="quickPasteToggleText">Buka Tempel Cepat</span>
                        <svg id="quickPasteArrow" class="w-3.5 h-3.5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
                <div id="quickPasteBody" class="hidden p-3.5 border-t border-slate-200 bg-white space-y-2">
                    <p class="text-[11px] text-slate-500">
                        Punya daftar nama dusun dari WhatsApp atau Excel? Tempel langsung di bawah (1 baris = 1 dusun). Klik tombol <b>"Masukkan ke Tabel"</b> untuk membuat baris secara otomatis.
                    </p>
                    <textarea id="batch_quick_text" rows="3" placeholder="Contoh:&#10;Dusun Karassik&#10;Dusun Ba'tan&#10;Dusun Tallunglipu&#10;Dusun Malango" class="w-full px-3 py-2 border border-slate-300 rounded-lg outline-none focus:border-indigo-600 font-sans text-xs"></textarea>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-slate-400">Baris kosong akan diabaikan secara otomatis.</span>
                        <button type="button" onclick="parseQuickPasteText()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-xs transition flex items-center space-x-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            <span>Masukkan ke Tabel di Bawah</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Baris Dusun (Dynamic Repeater) -->
            <div class="border border-slate-200 rounded-xl overflow-hidden shadow-xs bg-white">
                <div class="p-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <span class="font-bold text-[11px] uppercase tracking-wider text-slate-800">3. Daftar Baris Dusun</span>
                        <span id="batch_count_badge" class="px-2 py-0.5 bg-emerald-100 text-emerald-800 font-bold rounded-full text-[10px]">
                            1 Dusun
                        </span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="clearBatchRows()" class="px-2.5 py-1 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded text-xs transition cursor-pointer">
                            Bersihkan Semua
                        </button>
                        <button type="button" onclick="addBatchRow()" class="px-3 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 font-medium rounded text-xs transition flex items-center space-x-1 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Baris</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse" id="tableBatchDusun">
                        <thead>
                            <tr class="bg-slate-100/70 text-slate-600 font-semibold border-b border-slate-200 text-[11px]">
                                <th class="py-2 px-3 w-10 text-center">#</th>
                                <th class="py-2 px-3 min-w-[200px]">Nama Dusun / Lingkungan <span class="text-rose-600">*</span></th>
                                <th class="py-2 px-3 w-20">RT</th>
                                <th class="py-2 px-3 w-20">RW</th>
                                <th class="py-2 px-3 min-w-[150px]">Kepala Dusun</th>
                                <th class="py-2 px-3 min-w-[160px]">Keterangan</th>
                                <th class="py-2 px-3 w-12 text-center">Hapus</th>
                            </tr>
                        </thead>
                        <tbody id="batch_rows_container" class="divide-y divide-slate-100">
                            <!-- Rows generated dynamically -->
                        </tbody>
                    </table>
                </div>

                <div class="p-2.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <button type="button" onclick="addBatchRow()" class="text-xs text-blue-700 hover:text-blue-900 font-medium flex items-center space-x-1 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Klik untuk menambah baris lagi</span>
                    </button>
                    <span class="text-[11px] text-slate-500">Kolom RT, RW, Kepala Dusun, dan Keterangan bersifat opsional.</span>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                <button type="button" onclick="closeBatchCreateModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitBatch" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-semibold transition shadow-md flex items-center space-x-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Semua Dusun</span>
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

<!-- Modal Konfirmasi Hapus Dusun -->
<div id="modalConfirmAction" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-start gap-3.5">
            <div id="confirmIconWrapper" class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-rose-50 text-rose-600 border border-rose-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 id="confirmTitle" class="text-sm font-bold text-slate-900 leading-tight">Konfirmasi Hapus Dusun</h3>
                <div id="confirmMessage" class="mt-2 text-xs text-slate-600 leading-relaxed"></div>
                <div class="mt-3.5 p-2.5 bg-slate-50 border border-slate-200/80 rounded-lg text-[11px] text-slate-600 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Tindakan ini hanya menghapus data master dusun yang dipilih.</span>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
            <button type="button" onclick="closeConfirmModal()" class="px-3.5 py-2 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer">
                Batal
            </button>
            <button type="button" id="confirmSubmitBtn" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>Ya, Hapus Dusun</span>
            </button>
        </div>
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

        // Load initial kecamatan for batch modal
        const initialBatchKota = $('#batch_filter_kota_id').val();
        if (initialBatchKota) {
            loadKecamatanOptions(initialBatchKota, '#batch_filter_kecamatan_id');
        }

        // Init Select2 AJAX for Batch Modal
        $('#batch_kelurahan_id').select2({
            dropdownParent: $('#modalBatchCreate'),
            placeholder: 'Ketik nama kelurahan atau kode...',
            allowClear: true,
            ajax: {
                url: searchKelurahanUrl,
                dataType: 'json',
                delay: 250,
                data: params => ({
                    q: params.term,
                    kota_id: $('#batch_filter_kota_id').val(),
                    kecamatan_id: $('#batch_filter_kecamatan_id').val()
                }),
                processResults: data => ({ results: data.results })
            }
        });

        // Event: Ganti Kabupaten pada Batch Modal
        $('#batch_filter_kota_id').on('change', function() {
            loadKecamatanOptions(this.value, '#batch_filter_kecamatan_id');
            $('#batch_kelurahan_id').val(null).trigger('change');
            $('#batch_kelurahan_badge').addClass('hidden');
        });

        // Event: Ganti Kecamatan pada Batch Modal
        $('#batch_filter_kecamatan_id').on('change', function() {
            $('#batch_kelurahan_id').val(null).trigger('change');
            $('#batch_kelurahan_badge').addClass('hidden');
        });

        // Event: Pilih Kelurahan pada Batch Modal
        $('#batch_kelurahan_id').on('select2:select', function(e) {
            const data = e.params.data;
            if (data) {
                $('#batch_kelurahan_text').text(data.text);
                $('#batch_kelurahan_code').text('Kode: ' + data.id);
                $('#batch_kelurahan_badge').removeClass('hidden');
            }
        });

        $('#batch_kelurahan_id').on('select2:clear', function() {
            $('#batch_kelurahan_badge').addClass('hidden');
        });

        // Form Submit Validation for Batch Modal
        $('#formBatchCreate').on('submit', function(e) {
            const kelurahanId = $('#batch_kelurahan_id').val();
            if (!kelurahanId) {
                e.preventDefault();
                alert('Silakan pilih Kelurahan / Lembang tujuan terlebih dahulu.');
                $('#batch_kelurahan_id').select2('open');
                return false;
            }

            let hasValidName = false;
            $('.batch-input-nama').each(function() {
                if ($(this).val().trim().length > 0) {
                    hasValidName = true;
                }
            });

            if (!hasValidName) {
                e.preventDefault();
                alert('Minimal satu nama dusun harus diisi.');
                $('.batch-input-nama').first().focus();
                return false;
            }
        });
    });

    // Batch Rows Manager
    let batchRowIndex = 0;

    function createBatchRowHtml(index, data = {}) {
        const nama = (data.nama || '').replace(/"/g, '&quot;');
        const rt = (data.rt || '').replace(/"/g, '&quot;');
        const rw = (data.rw || '').replace(/"/g, '&quot;');
        const kepala = (data.kepala || '').replace(/"/g, '&quot;');
        const keterangan = (data.keterangan || '').replace(/"/g, '&quot;');

        return `
        <tr class="batch-row hover:bg-slate-50/60 transition" data-index="${index}">
            <td class="py-2 px-3 text-center text-slate-400 font-mono row-number">
                ${index + 1}
            </td>
            <td class="py-2 px-3">
                <input type="text" name="items[${index}][nama]" value="${nama}" placeholder="Contoh: Dusun Karassik" required
                       class="batch-input-nama w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-emerald-600 text-xs">
            </td>
            <td class="py-2 px-3">
                <input type="text" name="items[${index}][rt]" value="${rt}" placeholder="01"
                       class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-emerald-600 text-xs">
            </td>
            <td class="py-2 px-3">
                <input type="text" name="items[${index}][rw]" value="${rw}" placeholder="02"
                       class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-emerald-600 text-xs">
            </td>
            <td class="py-2 px-3">
                <input type="text" name="items[${index}][kepala_dusun]" value="${kepala}" placeholder="Bpk. Paulus"
                       class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-emerald-600 text-xs">
            </td>
            <td class="py-2 px-3">
                <input type="text" name="items[${index}][keterangan]" value="${keterangan}" placeholder="Catatan batas/lokasi..."
                       class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none focus:border-emerald-600 text-xs">
            </td>
            <td class="py-2 px-3 text-center">
                <button type="button" onclick="removeBatchRow(this)" title="Hapus baris ini"
                        class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </td>
        </tr>
        `;
    }

    function updateBatchRowNumbers() {
        const rows = $('#batch_rows_container tr.batch-row');
        rows.each(function(idx) {
            $(this).find('.row-number').text(idx + 1);
        });
        const total = rows.length;
        $('#batch_count_badge').text(`${total} Dusun`);
        $('#btnSubmitBatch span').text(`Simpan ${total} Dusun`);
    }

    function addBatchRow(data = {}) {
        const html = createBatchRowHtml(batchRowIndex++, data);
        $('#batch_rows_container').append(html);
        updateBatchRowNumbers();
    }

    function removeBatchRow(btn) {
        const rows = $('#batch_rows_container tr.batch-row');
        if (rows.length <= 1) {
            $(btn).closest('tr').find('input').val('');
            return;
        }
        $(btn).closest('tr').remove();
        updateBatchRowNumbers();
    }

    function clearBatchRows() {
        $('#batch_rows_container').empty();
        batchRowIndex = 0;
        addBatchRow();
    }

    function toggleQuickPaste() {
        const $body = $('#quickPasteBody');
        const isHidden = $body.hasClass('hidden');
        if (isHidden) {
            $body.removeClass('hidden');
            $('#quickPasteToggleText').text('Tutup Tempel Cepat');
            $('#quickPasteArrow').addClass('rotate-180');
        } else {
            $body.addClass('hidden');
            $('#quickPasteToggleText').text('Buka Tempel Cepat');
            $('#quickPasteArrow').removeClass('rotate-180');
        }
    }

    function parseQuickPasteText() {
        const raw = $('#batch_quick_text').val();
        if (!raw.trim()) return;

        const lines = raw.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0);
        if (lines.length === 0) return;

        const existingRows = $('#batch_rows_container tr.batch-row');
        let hasOnlyEmptyRow = false;
        if (existingRows.length === 1) {
            const val = existingRows.find('.batch-input-nama').val();
            if (!val || val.trim() === '') {
                hasOnlyEmptyRow = true;
            }
        }

        if (hasOnlyEmptyRow) {
            $('#batch_rows_container').empty();
            batchRowIndex = 0;
        }

        lines.forEach(line => {
            addBatchRow({ nama: line });
        });

        $('#batch_quick_text').val('');
        updateBatchRowNumbers();
    }

    function openBatchCreateModal() {
        if ($('#batch_rows_container tr.batch-row').length === 0) {
            clearBatchRows();
        }
        $('#modalBatchCreate').removeClass('hidden');
    }

    function closeBatchCreateModal() {
        $('#modalBatchCreate').addClass('hidden');
    }

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

    // Modal Konfirmasi Hapus Dusun
    let currentDeleteDusunId = null;

    function confirmDeleteDusun(id, nama) {
        currentDeleteDusunId = id;
        $('#confirmTitle').text('Hapus Data Dusun');
        $('#confirmMessage').html(`Apakah Anda yakin ingin menghapus <b>${nama}</b> dari daftar master dusun?`);
        $('#modalConfirmAction').removeClass('hidden');
    }

    function closeConfirmModal() {
        currentDeleteDusunId = null;
        $('#modalConfirmAction').addClass('hidden');
    }

    $('#confirmSubmitBtn').on('click', function() {
        if (currentDeleteDusunId) {
            $(`#formDeleteDusun_${currentDeleteDusunId}`).submit();
        }
    });
</script>
@endpush
@endsection
