<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Penyaluran Bantuan Sosial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { font-size: 11px; }
            .no-print { display: none !important; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body class="bg-white text-slate-900 p-8 max-w-5xl mx-auto">

    <!-- Floating Action Toolbar (Hidden on print) -->
    <div class="no-print mb-6 p-4 bg-slate-100 rounded-md flex items-center justify-between border border-slate-200">
        <span class="text-xs text-slate-700 font-medium">
            Pratinjau Cetak dan Ekspor PDF Dokumen Resmi
        </span>
        <div class="space-x-2">
            <button onclick="window.close()" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 rounded text-xs font-medium text-slate-700">
                Tutup
            </button>
            <button onclick="window.print()" class="px-4 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium shadow-sm">
                Cetak Dokumen
            </button>
        </div>
    </div>

    <!-- Official Header / Kop Surat -->
    <div class="text-center pb-4 mb-6 border-b-2 border-slate-900">
        <h1 class="text-base font-bold uppercase tracking-wider">Laporan Rekapitulasi Penyaluran Bantuan Sosial</h1>
        <p class="text-xs text-slate-600 mt-1">Sistem Manajemen Distribusi Bantuan dan Logistik Wilayah</p>
        <p class="text-[11px] text-slate-500 font-mono mt-0.5">Tanggal Cetak: {{ date('d/m/Y, H:i') }} WIB</p>
    </div>

    <!-- Info Filter Parameter -->
    <div class="mb-4 text-xs grid grid-cols-3 gap-2 bg-slate-50 p-3 rounded-lg border border-slate-200">
        <div>
            <span class="text-slate-500">Program:</span>
            <b class="ml-1 text-slate-800">{{ $filterInfo['jenis'] }}</b>
        </div>
        <div>
            <span class="text-slate-500">Status:</span>
            <b class="ml-1 text-slate-800">{{ $filterInfo['status'] }}</b>
        </div>
        <div>
            <span class="text-slate-500">Periode:</span>
            <b class="ml-1 text-slate-800">{{ $filterInfo['periode'] }}</b>
        </div>
    </div>

    <!-- Data Table -->
    <table class="w-full text-left text-xs border-collapse border border-slate-300">
        <thead>
            <tr class="bg-slate-100 text-slate-800">
                <th class="border border-slate-300 p-2 text-center w-8">No</th>
                <th class="border border-slate-300 p-2">No. Transaksi</th>
                <th class="border border-slate-300 p-2">Program</th>
                <th class="border border-slate-300 p-2">Penerima & Alamat</th>
                <th class="border border-slate-300 p-2">Wilayah Administratif</th>
                <th class="border border-slate-300 p-2 text-right">Volume</th>
                <th class="border border-slate-300 p-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporanList as $idx => $item)
                <tr>
                    <td class="border border-slate-300 p-2 text-center">{{ $idx + 1 }}</td>
                    <td class="border border-slate-300 p-2 font-mono font-bold">{{ $item->kode_transaksi }}</td>
                    <td class="border border-slate-300 p-2">{{ $item->jenisBantuan?->nama }}</td>
                    <td class="border border-slate-300 p-2">
                        <span class="font-bold block">{{ $item->nama_penerima }}</span>
                        <span class="text-[10px] text-slate-500">{{ $item->alamat_detail ?: '-' }}</span>
                    </td>
                    <td class="border border-slate-300 p-2 text-[11px]">
                        @if($item->dusun)
                            <span class="font-bold text-slate-900 block">Dusun {{ $item->dusun->nama }}</span>
                        @endif
                        Kel. {{ $item->kelurahan?->nama }}<br>
                        Kec. {{ $item->kecamatan?->nama }}, {{ $item->kota?->nama }}
                    </td>
                    <td class="border border-slate-300 p-2 text-right font-bold">
                        {{ number_format($item->jumlah_bantuan, 0, ',', '.') }} {{ $item->satuan }}
                    </td>
                    <td class="border border-slate-300 p-2 text-center text-[10px] font-bold">
                        {{ $item->status }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="border border-slate-300 p-4 text-center text-slate-400">Tidak ada data penyaluran pada parameter ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="mt-12 pt-6 grid grid-cols-2 text-xs text-center break-inside-avoid">
        <div>
            <p class="text-slate-500 mb-16">Petugas Pelaksana Lapangan,</p>
            <p class="font-bold underline text-slate-900">( .................................................... )</p>
        </div>
        <div>
            <p class="text-slate-500 mb-16">Mengetahui / Penanggung Jawab,</p>
            <p class="font-bold underline text-slate-900">( .................................................... )</p>
        </div>
    </div>

</body>
</html>
