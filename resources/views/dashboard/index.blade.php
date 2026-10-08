@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Ringkasan Penyaluran Bantuan')
@section('page_subtitle', 'Pemantauan volume penyaluran air bersih dan sebaran wilayah')

@section('content')
<div class="space-y-6">

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Air Tersalurkan -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Air Tersalurkan</span>
            <div class="text-2xl font-bold text-slate-900 mt-2">
                {{ number_format($volumeAirTersalurkan, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">Liter</span>
            </div>
            <p class="text-[11px] text-emerald-700 mt-1">Realisasi distribusi selesai</p>
        </div>

        <!-- Metric 2: Kegiatan Distribusi -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Kegiatan Air</span>
            <div class="text-2xl font-bold text-slate-900 mt-2">
                {{ $totalPenyaluranAir }} <span class="text-xs font-normal text-slate-500">Lokasi</span>
            </div>
            <p class="text-[11px] text-blue-700 mt-1">{{ $statusCounts['PROSES'] }} lokasi armada bergerak</p>
        </div>

        <!-- Metric 3: Penerima Manfaat -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Penerima Manfaat</span>
            <div class="text-2xl font-bold text-slate-900 mt-2">
                {{ number_format($totalJiwaAir, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">Jiwa</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-1">Mencakup {{ number_format($totalKkAir, 0, ',', '.') }} Kepala Keluarga</p>
        </div>

        <!-- Metric 4: Antrean Rencana -->
        <div class="bg-white rounded-lg p-5 border border-slate-200">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Jadwal Rencana</span>
            <div class="text-2xl font-bold text-slate-900 mt-2">
                {{ $statusCounts['RENCANA'] }} <span class="text-xs font-normal text-slate-500">Lokasi</span>
            </div>
            <p class="text-[11px] text-amber-700 mt-1">Menunggu alokasi pengiriman</p>
        </div>
    </div>

    <!-- Peta Sebaran Wilayah -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Peta Sebaran Titik Distribusi Bantuan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pemetaan koordinat kelurahan dan status penyaluran lapangan</p>
            </div>
            <div class="flex items-center space-x-4 text-xs text-slate-600">
                <span class="inline-flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 mr-1.5"></span> Tersalurkan
                </span>
                <span class="inline-flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600 mr-1.5"></span> Proses
                </span>
                <span class="inline-flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-1.5"></span> Rencana
                </span>
            </div>
        </div>

        <div id="sebaranMap" class="w-full h-80 z-10 bg-slate-100"></div>
    </div>

    <!-- Dua Kolom: Tabel Kegiatan Terkini & Program Bantuan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tabel Kegiatan Terkini (2 Kolom) -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 p-5">
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Kegiatan Penyaluran Terkini</h2>
                    <p class="text-xs text-slate-500">Lima riwayat penyaluran air terbaru</p>
                </div>
                <a href="{{ route('bantuan.air.index') }}" class="text-xs font-medium text-blue-700 hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-500 uppercase border-b border-slate-200">
                            <th class="py-2.5 pr-3">No. Transaksi</th>
                            <th class="py-2.5 px-3">Penerima & Lokasi</th>
                            <th class="py-2.5 px-3 text-right">Volume</th>
                            <th class="py-2.5 px-3 text-center">Status</th>
                            <th class="py-2.5 pl-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($penyaluranTerbaru as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 pr-3 font-mono font-medium text-slate-800">
                                    {{ $item->kode_transaksi }}
                                    <span class="block text-[11px] font-sans text-slate-400">
                                        {{ $item->tanggal_penyaluran ? $item->tanggal_penyaluran->format('d/m/Y') : $item->tanggal_rencana->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-medium text-slate-900 block">{{ $item->nama_penerima }}</span>
                                    <span class="text-[11px] text-slate-500 block">
                                        Kel. {{ $item->kelurahan?->nama }}, Kec. {{ $item->kecamatan?->nama }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right font-medium text-slate-900">
                                    {{ number_format($item->jumlah_bantuan, 0, ',', '.') }} {{ $item->satuan }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    @if($item->status === 'TERSALURKAN')
                                        <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            Tersalurkan
                                        </span>
                                    @elseif($item->status === 'PROSES')
                                        <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                            Proses
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                            Rencana
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 pl-3 text-right">
                                    <a href="{{ route('bantuan.air.show', $item->id) }}" class="text-blue-700 hover:underline font-medium">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400">Belum ada riwayat penyaluran data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Master Program Bantuan (1 Kolom) -->
        <div class="bg-white rounded-lg border border-slate-200 p-5 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900">Program Bantuan</h2>
                    <p class="text-xs text-slate-500">Status kategori bantuan terdaftar</p>
                </div>
                <a href="{{ route('bantuan.jenis.index') }}" class="text-xs font-medium text-blue-700 hover:underline">
                    Kelola
                </a>
            </div>

            <div class="space-y-2">
                @foreach($semuaJenisBantuan as $jb)
                    <div class="p-3 rounded border border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div>
                            <span class="font-semibold text-slate-900 text-xs block">{{ $jb->nama }}</span>
                            <span class="text-[11px] text-slate-500">Satuan: {{ $jb->satuan }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-medium text-slate-700 block">{{ $jb->penyaluran_count }} kegiatan</span>
                            @if($jb->slug === 'air')
                                <span class="text-[10px] text-blue-700 font-medium">Fokus saat ini</span>
                            @else
                                <span class="text-[10px] text-slate-400">Siap digunakan</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-2">
                <a href="{{ route('bantuan.jenis.index') }}"
                   class="w-full py-2 px-3 border border-slate-300 hover:border-slate-400 rounded text-xs font-medium text-slate-700 text-center block transition">
                    Tambah Jenis Program Bantuan
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const titikData = @json($mapTitik);

        let defaultCenter = [-0.789275, 113.921327];
        let defaultZoom = 5;

        if (titikData.length > 0) {
            defaultCenter = [titikData[0].lat, titikData[0].lng];
            defaultZoom = 8;
        }

        const map = L.map('sebaranMap').setView(defaultCenter, defaultZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const markersGroup = L.featureGroup();

        titikData.forEach(function(item) {
            let color = '#2563eb';
            if (item.status === 'TERSALURKAN') color = '#059669';
            if (item.status === 'RENCANA') color = '#d97706';

            const markerHtml = `
                <div style="background-color: ${color}; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.3);"></div>
            `;

            const customIcon = L.divIcon({
                html: markerHtml,
                className: '',
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });

            const marker = L.marker([item.lat, item.lng], { icon: customIcon }).addTo(map);

            const popupContent = `
                <div style="font-family: inherit; font-size: 12px; min-width: 170px;">
                    <div style="font-weight: 600; color: #0f172a; margin-bottom: 2px;">${item.nama_penerima}</div>
                    <div style="color: #64748b; font-size: 11px; margin-bottom: 4px;">${item.lokasi}</div>
                    <div style="font-size: 11px; margin-bottom: 4px;">
                        Volume: <b>${item.jumlah}</b> | Status: <b>${item.status}</b>
                    </div>
                    <a href="/bantuan/air/${item.id}" style="color: #1d4ed8; font-weight: 500; font-size: 11px;">Buka rincian</a>
                </div>
            `;

            marker.bindPopup(popupContent);
            markersGroup.addLayer(marker);
        });

        if (titikData.length > 0) {
            map.fitBounds(markersGroup.getBounds().pad(0.3));
        }
    });
</script>
@endpush
