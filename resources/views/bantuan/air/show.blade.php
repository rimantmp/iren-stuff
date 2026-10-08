@extends('layouts.admin')

@section('title', 'Rincian Penyaluran ' . $penyaluran->kode_transaksi)
@section('page_title', 'Rincian Penyaluran Air Bersih')
@section('page_subtitle', $penyaluran->kode_transaksi . ' | ' . $penyaluran->nama_penerima . ' - Kel. ' . ($penyaluran->kelurahan?->nama ?? '-'))

@section('header_actions')
<div class="flex items-center space-x-2">
    <a href="{{ route('bantuan.air.index') }}"
       class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Daftar Bantuan</span>
    </a>
    <button onclick="window.print()"
            class="inline-flex items-center px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-medium transition">
        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        <span>Cetak Lembar Serah Terima</span>
    </button>
    <a href="{{ route('bantuan.air.edit', $penyaluran->id) }}"
       class="inline-flex items-center px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition focus:ring-2 focus:ring-offset-1 focus:ring-blue-600">
        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        <span>Edit Data</span>
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Top Card: Status Operational Stepper & Quick Action -->
    <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-slate-500 font-medium">Nomor Transaksi:</span>
                    <span class="font-mono text-sm font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                        {{ $penyaluran->kode_transaksi }}
                    </span>
                    <span class="text-xs text-slate-400">|</span>
                    <span class="text-xs text-slate-500">Dibuat {{ $penyaluran->created_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            <!-- Quick Status Change Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs text-slate-500 font-medium mr-1">Ubah Status Cepat:</span>
                @if($penyaluran->status !== 'RENCANA')
                    <form action="{{ route('bantuan.air.status', $penyaluran->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="RENCANA">
                        <button type="submit" class="px-2.5 py-1 text-xs font-medium rounded border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition">
                            Set Rencana
                        </button>
                    </form>
                @endif

                @if($penyaluran->status !== 'PROSES')
                    <form action="{{ route('bantuan.air.status', $penyaluran->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="PROSES">
                        <button type="submit" class="px-2.5 py-1 text-xs font-medium rounded border border-blue-300 bg-blue-50 hover:bg-blue-100 text-blue-800 transition">
                            Armada Berangkat (Proses)
                        </button>
                    </form>
                @endif

                @if($penyaluran->status !== 'TERSALURKAN')
                    <form action="{{ route('bantuan.air.status', $penyaluran->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="TERSALURKAN">
                        <button type="submit" class="px-2.5 py-1 text-xs font-medium rounded border border-emerald-300 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 transition">
                            Air Diterima (Tersalurkan)
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- 3-Step Milestone Stepper -->
        <div class="pt-5">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Step 1: Rencana -->
                <div class="p-3 rounded-lg border {{ $penyaluran->status === 'RENCANA' ? 'border-amber-400 bg-amber-50/50' : 'border-slate-200 bg-slate-50/50' }} flex items-start space-x-3">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0 {{ $penyaluran->status === 'RENCANA' ? 'bg-amber-600 text-white ring-4 ring-amber-100' : 'bg-slate-300 text-slate-700' }}">
                        1
                    </span>
                    <div class="text-xs">
                        <span class="font-bold text-slate-900 block">Jadwal Rencana</span>
                        <span class="text-slate-600 block mt-0.5">Tgl: {{ $penyaluran->tanggal_rencana->format('d/m/Y') }}</span>
                        <span class="text-[11px] text-slate-500 block">Alokasi logistik armada</span>
                    </div>
                </div>

                <!-- Step 2: Proses -->
                <div class="p-3 rounded-lg border {{ $penyaluran->status === 'PROSES' ? 'border-blue-500 bg-blue-50/50' : 'border-slate-200 bg-slate-50/50' }} flex items-start space-x-3">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0 {{ $penyaluran->status === 'PROSES' ? 'bg-blue-600 text-white ring-4 ring-blue-100' : ($penyaluran->status === 'TERSALURKAN' ? 'bg-blue-600 text-white' : 'bg-slate-300 text-slate-700') }}">
                        2
                    </span>
                    <div class="text-xs">
                        <span class="font-bold text-slate-900 block">Pengiriman Armada</span>
                        <span class="text-slate-600 block mt-0.5">Plat: {{ $penyaluran->nomor_armada ?: 'Belum dicatat' }}</span>
                        <span class="text-[11px] text-slate-500 block">Petugas: {{ $penyaluran->nama_petugas ?: 'Belum ditugaskan' }}</span>
                    </div>
                </div>

                <!-- Step 3: Tersalurkan -->
                <div class="p-3 rounded-lg border {{ $penyaluran->status === 'TERSALURKAN' ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-200 bg-slate-50/50' }} flex items-start space-x-3">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0 {{ $penyaluran->status === 'TERSALURKAN' ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-slate-300 text-slate-700' }}">
                        3
                    </span>
                    <div class="text-xs">
                        <span class="font-bold text-slate-900 block">Tersalurkan & Selesai</span>
                        <span class="text-slate-600 block mt-0.5">
                            Realisasi: {{ $penyaluran->tanggal_penyaluran ? $penyaluran->tanggal_penyaluran->format('d/m/Y') : 'Menunggu Serah Terima' }}
                        </span>
                        <span class="text-[11px] text-slate-500 block">Diterima warga setempat</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Total Volume Air</span>
            <div class="flex items-baseline space-x-1.5 mt-1">
                <span class="text-2xl font-black text-blue-900 font-mono">{{ number_format($penyaluran->jumlah_bantuan, 0, ',', '.') }}</span>
                <span class="text-xs font-semibold text-blue-700">{{ $penyaluran->satuan }}</span>
            </div>
            <span class="text-[11px] text-slate-500 block mt-0.5">{{ $penyaluran->metode_distribusi ?: 'Truk Tangki' }}</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Penerima Manfaat</span>
            <div class="flex items-baseline space-x-1.5 mt-1">
                <span class="text-2xl font-black text-slate-900 font-mono">{{ $penyaluran->jumlah_kk }}</span>
                <span class="text-xs font-semibold text-slate-600">KK ({{ $penyaluran->jumlah_jiwa }} Jiwa)</span>
            </div>
            <span class="text-[11px] text-slate-500 block mt-0.5">Penanggung jawab terdata</span>
        </div>

        @php
            $rasio = round($penyaluran->jumlah_bantuan / max(1, $penyaluran->jumlah_jiwa));
        @endphp
        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Rasio Pemenuhan per Jiwa</span>
            <div class="flex items-baseline space-x-1.5 mt-1">
                <span class="text-2xl font-black text-emerald-800 font-mono">~{{ $rasio }}</span>
                <span class="text-xs font-semibold text-emerald-700">Liter / Jiwa</span>
            </div>
            <span class="text-[11px] text-slate-500 block mt-0.5">Standar darurat: 15 s/d 20 L/hari</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500 font-medium block">Lokasi Sasaran</span>
            <div class="mt-1">
                <span class="text-sm font-bold text-slate-900 block truncate">Kel. {{ $penyaluran->kelurahan?->nama }}</span>
                <span class="text-xs text-slate-500 block truncate">Kec. {{ $penyaluran->kecamatan?->nama }}, {{ $penyaluran->kota?->nama }}</span>
            </div>
        </div>
    </div>

    <!-- Main Detail Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Details & Photos (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Card: Penanggung Jawab & Kontak -->
            <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm">
                <div class="pb-3 border-b border-slate-200 flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Penerima Manfaat & Penanggung Jawab Lingkungan
                    </h3>
                    <span class="text-[11px] font-medium text-slate-500">Data Sasaran Warga</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block mb-1">Nama Penanggung Jawab:</span>
                        <span class="font-bold text-slate-900 text-sm block">{{ $penyaluran->nama_penerima }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block mb-1">Nomor Kontak / Telepon:</span>
                        @if($penyaluran->kontak_penerima)
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-medium text-slate-800">{{ $penyaluran->kontak_penerima }}</span>
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $penyaluran->kontak_penerima);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded text-[10px] font-medium transition"
                                   title="Kirim pesan WhatsApp ke penanggung jawab">
                                    <svg class="w-3 h-3 mr-1 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.302c-.087.087-.179.181-.077.355.101.173.45 1.018 1.157 1.649.911.813 1.678 1.065 1.915 1.181.237.116.376.101.517-.058.141-.159.607-.707.769-.953.162-.246.325-.203.548-.116.223.087 1.416.668 1.661.79.245.122.408.181.468.283.06.102.06.591-.084.996z"/></svg>
                                    <span>WhatsApp</span>
                                </a>
                            </div>
                        @else
                            <span class="text-slate-400 italic">Nomor kontak belum dicatat</span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 text-xs">
                    <span class="text-slate-500 block mb-1">Alamat Lengkap & Patokan Titik Toren:</span>
                    <div class="p-3 bg-slate-50 rounded border border-slate-200 text-slate-800 leading-relaxed font-medium">
                        {{ $penyaluran->alamat_detail ?: 'Belum ada keterangan alamat spesifik. Silakan cek titik koordinat pada peta.' }}
                    </div>
                </div>
            </div>

            <!-- Card: Logistik Armada & Petugas Lapangan -->
            <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm">
                <div class="pb-3 border-b border-slate-200 flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Armada Distribusi & Petugas Lapangan
                    </h3>
                    <span class="text-[11px] font-medium text-slate-500">Informasi Operasional</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block mb-0.5">Nomor Truk Tangki:</span>
                        <span class="font-mono font-bold text-slate-900 text-sm block">
                            {{ $penyaluran->nomor_armada ?: '-' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-500 block mb-0.5">Petugas / Driver:</span>
                        <span class="font-semibold text-slate-800 block">
                            {{ $penyaluran->nama_petugas ?: '-' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-500 block mb-0.5">Sumber Pengambilan:</span>
                        <span class="font-medium text-slate-800 block">
                            {{ $penyaluran->sumber_air ?: 'PDAM / Sumber Alami' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-slate-500 block mb-0.5">Metode Distribusi:</span>
                        <span class="font-medium text-slate-800 block">
                            {{ $penyaluran->metode_distribusi ?: 'Truk Tangki' }}
                        </span>
                    </div>
                </div>

                @if($penyaluran->catatan)
                    <div class="mt-4 pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-500 block mb-1">Catatan Khusus Lapangan:</span>
                        <p class="text-slate-700 bg-slate-50 p-2.5 rounded border border-slate-200">
                            {{ $penyaluran->catatan }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Card: Foto Dokumentasi Lapangan -->
            <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm">
                <div class="pb-3 border-b border-slate-200 flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Dokumentasi Serah Terima & Kondisi Lapangan
                    </h3>
                    @if($penyaluran->foto_dokumentasi)
                        <span class="text-[11px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            Foto Terverifikasi
                        </span>
                    @else
                        <span class="text-[11px] font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
                            Belum Ada Foto
                        </span>
                    @endif
                </div>

                @if($penyaluran->foto_dokumentasi)
                    <div class="rounded-lg overflow-hidden border border-slate-200 bg-slate-900/5 flex items-center justify-center max-h-96">
                        <img src="{{ asset('storage/' . $penyaluran->foto_dokumentasi) }}"
                             alt="Dokumentasi Penyaluran Air {{ $penyaluran->kode_transaksi }}"
                             class="w-full object-contain max-h-96">
                    </div>
                @else
                    <div class="p-8 text-center border-2 border-dashed border-slate-200 rounded-lg bg-slate-50 text-xs text-slate-500">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="font-medium text-slate-700 mb-1">Foto bukti dokumentasi belum diunggah</p>
                        <p class="text-[11px] text-slate-400 mb-3">Foto serah terima dapat diunggah melalui formulir edit data saat armada telah tiba di lokasi.</p>
                        <a href="{{ route('bantuan.air.edit', $penyaluran->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition">
                            Unggah Foto Dokumentasi
                        </a>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right Column: Wilayah, Map, Navigation & Print Template (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Card: Peta Interaktif & Navigasi GIS -->
            <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm">
                <div class="pb-3 border-b border-slate-200 flex items-center justify-between mb-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Lokasi & Peta GIS
                    </h3>
                    <span class="text-[11px] font-mono text-slate-500">Kemendagri</span>
                </div>

                <!-- Administrative Hierarchy -->
                <div class="space-y-1.5 text-xs mb-3 bg-slate-50 p-3 rounded border border-slate-200">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Provinsi:</span>
                        <span class="font-medium text-slate-800">{{ $penyaluran->provinsi?->nama }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kabupaten/Kota:</span>
                        <span class="font-medium text-slate-800">{{ $penyaluran->kota?->nama }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kecamatan:</span>
                        <span class="font-medium text-slate-800">{{ $penyaluran->kecamatan?->nama }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kelurahan/Desa:</span>
                        <span class="font-bold text-blue-900">{{ $penyaluran->kelurahan?->nama }}</span>
                    </div>
                </div>

                <!-- Leaflet Container -->
                @if($penyaluran->latitude && $penyaluran->longitude && ($penyaluran->latitude != 0 || $penyaluran->longitude != 0))
                    <div class="rounded-lg overflow-hidden border border-slate-300">
                        <div id="detailMap" class="w-full h-52 bg-slate-100"></div>
                        <div class="p-2 bg-slate-50 text-[11px] text-slate-600 flex items-center justify-between border-t border-slate-200">
                            <span class="font-mono">Lat: {{ number_format($penyaluran->latitude, 5) }}, Lng: {{ number_format($penyaluran->longitude, 5) }}</span>
                        </div>
                    </div>

                    <!-- Direct Navigation Action -->
                    <div class="mt-3">
                        <a href="https://www.google.com/maps?q={{ $penyaluran->latitude }},{{ $penyaluran->longitude }}"
                           target="_blank" rel="noopener noreferrer"
                           class="w-full py-2 px-3 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition flex items-center justify-center space-x-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Buka Navigasi di Google Maps</span>
                        </a>
                    </div>
                @else
                    <div class="p-4 bg-slate-50 rounded border border-slate-200 text-center text-xs text-slate-500">
                        Koordinat GPS belum tercatat pada transaksi ini.
                    </div>
                @endif
            </div>

            <!-- Card: Tindakan Data -->
            <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm space-y-3">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-200">
                    Opsi Manajemen
                </h3>

                <form action="{{ route('bantuan.air.destroy', $penyaluran->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penyaluran {{ $penyaluran->kode_transaksi }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2 px-3 bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 rounded text-xs font-medium transition flex items-center justify-center space-x-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus Transaksi Penyaluran</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>

<!-- Printable Delivery Receipt (Hidden on screen, visible on print) -->
<div id="printReceipt" class="hidden print:block p-8 text-black bg-white">
    <div class="text-center border-b-2 border-black pb-4 mb-6">
        <h1 class="text-lg font-bold uppercase tracking-wider">BERITA ACARA SERAH TERIMA BANTUAN AIR BERSIH</h1>
        <p class="text-xs mt-1">Sistem Tanggap Darurat dan Distribusi Logistik Air Bersih</p>
        <p class="text-xs font-mono font-bold mt-1">NO: {{ $penyaluran->kode_transaksi }}</p>
    </div>

    <table class="w-full text-xs mb-6 border-collapse">
        <tr>
            <td class="py-1.5 font-bold w-48">Tanggal Rencana</td>
            <td class="py-1.5">: {{ $penyaluran->tanggal_rencana->format('d F Y') }}</td>
        </tr>
        <tr>
            <td class="py-1.5 font-bold">Tanggal Realisasi</td>
            <td class="py-1.5">: {{ $penyaluran->tanggal_penyaluran ? $penyaluran->tanggal_penyaluran->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="py-1.5 font-bold">Wilayah Distribusi</td>
            <td class="py-1.5">: Kel. {{ $penyaluran->kelurahan?->nama }}, Kec. {{ $penyaluran->kecamatan?->nama }}, {{ $penyaluran->kota?->nama }}</td>
        </tr>
        <tr>
            <td class="py-1.5 font-bold">Alamat / Titik Toren</td>
            <td class="py-1.5">: {{ $penyaluran->alamat_detail ?: '-' }}</td>
        </tr>
        <tr>
            <td class="py-1.5 font-bold">Penanggung Jawab / Penerima</td>
            <td class="py-1.5">: {{ $penyaluran->nama_penerima }} ({{ $penyaluran->kontak_penerima ?: 'Tanpa kontak' }})</td>
        </tr>
        <tr>
            <td class="py-1.5 font-bold">Estimasi Warga Terbantu</td>
            <td class="py-1.5">: {{ $penyaluran->jumlah_kk }} KK / {{ $penyaluran->jumlah_jiwa }} Jiwa</td>
        </tr>
        <tr>
            <td class="py-1.5 font-bold">Volume Air Bersih</td>
            <td class="py-1.5 font-bold text-sm">: {{ number_format($penyaluran->jumlah_bantuan, 0, ',', '.') }} {{ $penyaluran->satuan }}</td>
        </tr>
        <tr>
            <td class="py-1.5 font-bold">Nomor Truk Tangki</td>
            <td class="py-1.5">: {{ $penyaluran->nomor_armada ?: '-' }}</td>
        </tr>
        <tr>
            <td class="py-1.5 font-bold">Petugas / Pengemudi</td>
            <td class="py-1.5">: {{ $penyaluran->nama_petugas ?: '-' }}</td>
        </tr>
    </table>

    <div class="grid grid-cols-3 gap-6 text-center text-xs mt-16 pt-8 border-t border-slate-300">
        <div>
            <p class="mb-16">Petugas / Pengemudi</p>
            <p class="font-bold underline">{{ $penyaluran->nama_petugas ?: '..............................' }}</p>
        </div>
        <div>
            <p class="mb-16">Penanggung Jawab / Penerima</p>
            <p class="font-bold underline">{{ $penyaluran->nama_penerima }}</p>
        </div>
        <div>
            <p class="mb-16">Mengetahui Kepala Desa / Lurah</p>
            <p class="font-bold underline">..............................</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($penyaluran->latitude && $penyaluran->longitude && ($penyaluran->latitude != 0 || $penyaluran->longitude != 0))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lat = {{ $penyaluran->latitude }};
        const lng = {{ $penyaluran->longitude }};

        const map = L.map('detailMap', {
            zoomControl: true,
            attributionControl: false
        }).setView([lat, lng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18
        }).addTo(map);

        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup("<b>{{ $penyaluran->nama_penerima }}</b><br>Volume: {{ number_format($penyaluran->jumlah_bantuan, 0, ',', '.') }} L<br>Kel. {{ $penyaluran->kelurahan?->nama }}").openPopup();
    });
</script>
@endif
@endpush
