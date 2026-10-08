@extends('layouts.admin')

@section('title', 'Laporan Perbandingan Penyaluran Wilayah')
@section('page_title', 'Laporan Perbandingan Wilayah')
@section('page_subtitle', 'Monitoring keadilan dan ketercakupan penyaluran bantuan per Kabupaten, Kecamatan, dan Kelurahan/Lembang')

@section('header_actions')
<div class="flex items-center space-x-2">
    <a href="{{ route('rekap.perbandingan.excel', request()->query()) }}"
       class="inline-flex items-center px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs font-medium transition shadow-sm">
        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Download Excel</span>
    </a>
    <a href="{{ route('rekap.perbandingan.cetak', request()->query()) }}" target="_blank"
       class="inline-flex items-center px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition shadow-sm">
        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Cetak / Download PDF</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Rasio Desa Tersalurkan -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block">Desa Tersalurkan</span>
                <span class="text-lg font-black text-emerald-700 leading-tight">
                    {{ $summary['desa_tersalur'] }} <span class="text-xs font-normal text-slate-500">/ {{ $summary['total_kelurahan'] }} Desa</span>
                </span>
                <span class="text-[10px] text-slate-400 block">
                    {{ $summary['total_kelurahan'] > 0 ? round(($summary['desa_tersalur'] / $summary['total_kelurahan']) * 100, 1) : 0 }}% Ketercakupan Wilayah
                </span>
            </div>
        </div>

        <!-- Card 2: Desa Belum Tersalurkan -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block">Desa Belum Salur</span>
                <span class="text-lg font-black text-amber-700 leading-tight">
                    {{ $summary['desa_belum_tersalur'] }} <span class="text-xs font-normal text-slate-500">Desa</span>
                </span>
                <span class="text-[10px] text-slate-400 block">
                    {{ $summary['desa_blank'] }} Belum Tersentuh Alokasi
                </span>
            </div>
        </div>

        <!-- Card 3: Total Volume Realisasi -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <div>
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block">Volume Tersalurkan</span>
                <span class="text-lg font-black text-blue-900 leading-tight">
                    {{ number_format($summary['total_realisasi_volume'], 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">L</span>
                </span>
                <span class="text-[10px] text-slate-400 block">{{ $summary['total_realisasi_titik'] }} Titik Pengiriman Tuntas</span>
            </div>
        </div>

        <!-- Card 4: Kuota Rencana & Sisa -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm flex items-center space-x-3.5">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div class="flex-1">
                <span class="text-[11px] font-medium text-slate-500 uppercase tracking-wider block">Progres Target Rencana</span>
                <div class="flex items-center space-x-2">
                    <span class="text-lg font-black text-slate-900 leading-tight">{{ $summary['total_persentase'] }}%</span>
                    <span class="text-[10px] text-slate-400">Target: {{ number_format($summary['total_target_volume'], 0, ',', '.') }} L</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $summary['total_persentase']) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form Bar -->
    <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('rekap.perbandingan') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 text-xs">
            <!-- Filter Status Penyaluran (Baru) -->
            <div>
                <label for="filter_status" class="block font-bold text-slate-800 mb-1">Status Penyaluran</label>
                <select name="filter_status" id="filter_status" onchange="this.form.submit()" class="w-full py-2 px-3 border border-blue-300 bg-blue-50/40 text-blue-900 font-medium rounded-md focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    <option value="" {{ request('filter_status') == '' ? 'selected' : '' }}>Semua Wilayah</option>
                    <option value="sudah" {{ request('filter_status') == 'sudah' ? 'selected' : '' }}>✅ Sudah Tersalurkan</option>
                    <option value="belum" {{ request('filter_status') == 'belum' ? 'selected' : '' }}>⏳ Belum Tersalurkan</option>
                    <option value="belum_tersentuh" {{ request('filter_status') == 'belum_tersentuh' ? 'selected' : '' }}>⚪ Belum Ada Alokasi (Blank Spot)</option>
                </select>
            </div>

            <!-- Filter Kabupaten / Kota -->
            <div>
                <label for="kota_id" class="block font-medium text-slate-700 mb-1">Kabupaten / Kota</label>
                <select name="kota_id" id="kota_id" onchange="this.form.submit()" class="w-full py-2 px-3 border border-slate-300 rounded-md focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    @foreach($semuaKota as $kt)
                        <option value="{{ $kt->id }}" {{ $selectedKotaId == $kt->id ? 'selected' : '' }}>
                            {{ $kt->nama }}
                        </option>
                    @endforeach
                    <option value="SEMUA" {{ $selectedKotaId == 'SEMUA' ? 'selected' : '' }}>Semua Kabupaten (Ada Data)</option>
                </select>
            </div>

            <!-- Filter Kecamatan -->
            <div>
                <label for="kecamatan_id" class="block font-medium text-slate-700 mb-1">Kecamatan</label>
                <select name="kecamatan_id" id="kecamatan_id" class="w-full py-2 px-3 border border-slate-300 rounded-md focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    <option value="">Semua Kecamatan</option>
                    @foreach($semuaKecamatan as $kec)
                        <option value="{{ $kec->id }}" {{ request('kecamatan_id') == $kec->id ? 'selected' : '' }}>
                            {{ $kec->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Program Bantuan -->
            <div>
                <label for="jenis_bantuan_id" class="block font-medium text-slate-700 mb-1">Program</label>
                <select name="jenis_bantuan_id" id="jenis_bantuan_id" class="w-full py-2 px-3 border border-slate-300 rounded-md focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    <option value="">Semua Program</option>
                    @foreach($semuaJenisBantuan as $jb)
                        <option value="{{ $jb->id }}" {{ request('jenis_bantuan_id') == $jb->id ? 'selected' : '' }}>
                            {{ $jb->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Tanggal Mulai -->
            <div>
                <label for="tanggal_mulai" class="block font-medium text-slate-700 mb-1">Mulai Tanggal</label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                       class="w-full py-2 px-3 border border-slate-300 rounded-md focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
            </div>

            <!-- Filter Tanggal Akhir & Tombol -->
            <div>
                <label for="tanggal_akhir" class="block font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                <div class="flex items-center space-x-1.5">
                    <input type="date" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                           class="w-full py-2 px-3 border border-slate-300 rounded-md focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none">
                    <button type="submit" class="px-3 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-md font-medium transition shadow-sm flex items-center" title="Terapkan Filter">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    @if(request()->hasAny(['jenis_bantuan_id', 'kecamatan_id', 'filter_status', 'tanggal_mulai', 'tanggal_akhir']) || request('kota_id') != '7326')
                        <a href="{{ route('rekap.perbandingan') }}" class="p-2 text-slate-500 hover:text-slate-800 rounded border border-slate-300 hover:bg-slate-50 transition" title="Reset Filter">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Main Comparison Table Card -->
    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
        <!-- Table Top Header with Status Tabs -->
        <div class="px-5 py-3.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Tabel Komparasi Wilayah: Target Rencana vs Realisasi Penyaluran</h2>
                <p class="text-[11px] text-slate-500">Menampilkan seluruh wilayah administratif per Kabupaten, Kecamatan, dan Kelurahan/Lembang</p>
            </div>

            <!-- Right Controls: Page Size & Pills -->
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('rekap.perbandingan') }}" class="inline-flex items-center space-x-1.5 text-xs text-slate-500 mr-2">
                    @foreach(request()->except(['per_page', 'page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <span class="text-[11px] text-slate-500">Tampilkan:</span>
                    <select name="per_page" onchange="this.form.submit()" class="py-1 px-2 border border-slate-300 rounded text-xs bg-white text-slate-800 focus:outline-none">
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 baris</option>
                    </select>
                </form>

                <!-- Quick Filter Status Pills -->
                <div class="flex flex-wrap items-center gap-1 text-xs">
                    @php
                        $currentQuery = request()->query();
                    @endphp
                    <a href="{{ route('rekap.perbandingan', array_merge($currentQuery, ['filter_status' => '', 'page' => 1])) }}"
                       class="px-2.5 py-1 rounded text-[11px] font-semibold transition border {{ request('filter_status') == '' ? 'bg-slate-900 text-white border-slate-900 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                        Semua ({{ $summary['total_kelurahan'] }})
                    </a>
                    <a href="{{ route('rekap.perbandingan', array_merge($currentQuery, ['filter_status' => 'sudah', 'page' => 1])) }}"
                       class="px-2.5 py-1 rounded text-[11px] font-semibold transition border {{ request('filter_status') == 'sudah' ? 'bg-emerald-700 text-white border-emerald-700 shadow-sm' : 'bg-white text-emerald-800 border-emerald-200 hover:bg-emerald-50' }}">
                        Sudah ({{ $summary['desa_tersalur'] }})
                    </a>
                    <a href="{{ route('rekap.perbandingan', array_merge($currentQuery, ['filter_status' => 'belum', 'page' => 1])) }}"
                       class="px-2.5 py-1 rounded text-[11px] font-semibold transition border {{ request('filter_status') == 'belum' ? 'bg-amber-700 text-white border-amber-700 shadow-sm' : 'bg-white text-amber-800 border-amber-200 hover:bg-amber-50' }}">
                        Belum ({{ $summary['desa_belum_tersalur'] }})
                    </a>
                    <a href="{{ route('rekap.perbandingan', array_merge($currentQuery, ['filter_status' => 'belum_tersentuh', 'page' => 1])) }}"
                       class="px-2.5 py-1 rounded text-[11px] font-semibold transition border {{ request('filter_status') == 'belum_tersentuh' ? 'bg-slate-600 text-white border-slate-600 shadow-sm' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                        Nihil ({{ $summary['desa_blank'] }})
                    </a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-[10px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 text-center w-10">No</th>
                        <th class="py-3 px-4">Kabupaten / Kota</th>
                        <th class="py-3 px-4">Kecamatan</th>
                        <th class="py-3 px-4">Kelurahan / Lembang</th>
                        <th class="py-3 px-4 text-center">Target Rencana</th>
                        <th class="py-3 px-4 text-center">Realisasi Tersalur</th>
                        <th class="py-3 px-4 text-center">Sisa Belum Salur</th>
                        <th class="py-3 px-4 text-center w-32">Progres Capaian</th>
                        <th class="py-3 px-4 text-center w-28">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($paginatedItems as $idx => $row)
                        <tr class="hover:bg-slate-50/80 transition {{ $row['status_badge'] === 'Belum Tersentuh' ? 'bg-slate-50/40 text-slate-500' : '' }}">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">{{ $paginatedItems->firstItem() + $idx }}</td>
                            <td class="py-2.5 px-4 font-semibold text-slate-900">
                                <span class="px-2 py-0.5 bg-blue-50 text-blue-800 rounded font-semibold text-[11px] border border-blue-200">
                                    {{ $row['kota'] }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 font-medium text-slate-800">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-800 rounded font-medium text-[11px] border border-slate-200">
                                    {{ $row['kecamatan'] }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 font-medium {{ $row['status_badge'] === 'Belum Tersentuh' ? 'text-slate-600' : 'text-slate-950 font-bold' }}">
                                {{ $row['kelurahan'] }}
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                @if($row['target_volume'] > 0)
                                    <span class="font-bold text-slate-900 block">{{ number_format($row['target_volume'], 0, ',', '.') }} {{ $row['satuan'] }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $row['target_titik'] }} Titik</span>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-center {{ $row['realisasi_volume'] > 0 ? 'bg-emerald-50/40' : '' }}">
                                @if($row['realisasi_volume'] > 0)
                                    <span class="font-black text-emerald-800 block">{{ number_format($row['realisasi_volume'], 0, ',', '.') }} {{ $row['satuan'] }}</span>
                                    <span class="text-[10px] text-emerald-600 block">{{ $row['realisasi_titik'] }} Titik Selesai</span>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                @if($row['sisa_volume'] > 0)
                                    <span class="font-bold text-amber-700 block">{{ number_format($row['sisa_volume'], 0, ',', '.') }} {{ $row['satuan'] }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $row['sisa_titik'] }} Titik Menunggu</span>
                                @elseif($row['target_volume'] > 0 && $row['realisasi_volume'] >= $row['target_volume'])
                                    <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Tuntas</span>
                                @else
                                    <span class="text-slate-400 font-mono">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4">
                                @if($row['target_volume'] > 0)
                                    <div class="flex items-center justify-between text-[11px] mb-1">
                                        <span class="font-mono font-bold {{ $row['persentase'] >= 100 ? 'text-emerald-700' : ($row['persentase'] > 0 ? 'text-blue-700' : 'text-slate-400') }}">
                                            {{ $row['persentase'] }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="h-1.5 rounded-full {{ $row['persentase'] >= 100 ? 'bg-emerald-500' : ($row['persentase'] > 0 ? 'bg-blue-600' : 'bg-slate-300') }}"
                                             style="width: {{ min(100, $row['persentase']) }}%"></div>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[10px] italic">0% (Nihil)</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-center">
                                @if($row['status_badge'] === 'Selesai')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Tuntas
                                    </span>
                                @elseif($row['status_badge'] === 'Sebagian')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                        Sebagian
                                    </span>
                                @elseif($row['status_badge'] === 'Rencana')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        Rencana
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500 border border-slate-300">
                                        Belum Tersentuh
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-xs font-semibold text-slate-600">Tidak ada wilayah pada parameter filter ini</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Silakan ganti status filter atau pilih kabupaten/kecamatan lain.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($paginatedItems->total() > 0)
                    <tfoot class="bg-slate-100 text-slate-900 font-bold border-t-2 border-slate-300 text-xs">
                        <tr>
                            <td colspan="4" class="py-3 px-4 text-left uppercase tracking-wider">
                                TOTAL AKUMULATIF ({{ $paginatedItems->total() }} Wilayah)
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="block text-slate-950 font-black">{{ number_format($summary['total_target_volume'], 0, ',', '.') }} Liter</span>
                                <span class="block text-[10px] text-slate-500 font-normal">{{ $summary['total_target_titik'] }} Titik Target</span>
                            </td>
                            <td class="py-3 px-4 text-center bg-emerald-100/60">
                                <span class="block text-emerald-900 font-black">{{ number_format($summary['total_realisasi_volume'], 0, ',', '.') }} Liter</span>
                                <span class="block text-[10px] text-emerald-700 font-normal">{{ $summary['total_realisasi_titik'] }} Titik Tuntas</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="block text-amber-900 font-black">{{ number_format($summary['total_sisa_volume'], 0, ',', '.') }} Liter</span>
                                <span class="block text-[10px] text-slate-500 font-normal">{{ $summary['total_sisa_titik'] }} Titik Sisa</span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-black text-indigo-900 text-sm">
                                {{ $summary['total_persentase'] }}%
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded text-[10px] font-black {{ $summary['total_persentase'] >= 100 ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-white' }}">
                                    {{ $summary['total_persentase'] >= 100 ? 'TUNTAS 100%' : 'PROSES' }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($paginatedItems->hasPages())
            <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50/80 flex flex-wrap items-center justify-between gap-3 text-xs">
                <span class="text-slate-500 text-[11px]">
                    Menampilkan baris ke-<b>{{ $paginatedItems->firstItem() }}</b> s/d <b>{{ $paginatedItems->lastItem() }}</b> dari total <b>{{ $paginatedItems->total() }}</b> wilayah
                </span>
                <div>
                    {{ $paginatedItems->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
