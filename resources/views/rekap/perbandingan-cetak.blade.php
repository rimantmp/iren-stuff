<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perbandingan Wilayah - {{ date('d-m-Y') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #0f172a;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .page-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
            @page {
                size: A4 landscape;
                margin: 10mm 12mm 10mm 12mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-6 px-4">

    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="no-print max-w-6xl mx-auto mb-6 bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <a href="{{ route('rekap.perbandingan') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Web
            </a>
            <div>
                <span class="text-xs font-bold text-slate-900 block">Pratinjau Dokumen Cetak / PDF Laporan Perbandingan Wilayah</span>
                <span class="text-[11px] text-slate-500">Format cetak dioptimalkan untuk kertas A4 Landscape</span>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <button onclick="window.close()"
                    class="px-3.5 py-2 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 bg-slate-50 hover:bg-slate-100 transition">
                Tutup
            </button>
            <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold shadow transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Dokumen / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Main Printable Sheet (A4 Landscape) -->
    <div class="page-container max-w-6xl mx-auto bg-white p-8 sm:p-10 rounded-xl shadow-md border border-slate-200 text-slate-900">

        <!-- Kop Surat Resmi -->
        <div class="border-b-4 border-double border-slate-900 pb-3 mb-5">
            <div class="flex items-center justify-between gap-4">
                <div class="w-14 h-14 rounded-xl bg-blue-700 text-white flex flex-col items-center justify-center flex-shrink-0 shadow-sm border border-blue-800">
                    <span class="font-black text-lg tracking-tighter leading-none">SB</span>
                    <span class="text-[7px] font-bold tracking-widest uppercase mt-0.5">Aspirasi</span>
                </div>
                <div class="text-center flex-1 px-2">
                    <h2 class="text-[10px] font-bold text-slate-600 tracking-widest uppercase mb-0.5">SISTEM PENYALURAN BANTUAN SOSIAL & ASPIRASI MASYARAKAT</h2>
                    <h1 class="text-base sm:text-lg font-black text-slate-950 uppercase tracking-tight">LAPORAN PERBANDINGAN TARGET DAN REALISASI PENYALURAN</h1>
                    <p class="text-[11px] text-slate-600 font-medium">Daerah Pemilihan Sulawesi Selatan III - Sekretariat Wilayah Kabupaten Toraja Utara & Sekitarnya</p>
                </div>
                <div class="w-20 text-right flex-shrink-0 text-[10px] text-slate-500 font-mono">
                    <div class="border border-slate-300 p-1.5 rounded bg-slate-50 text-center">
                        <span class="block text-[8px] font-bold text-slate-600 uppercase">Tanggal Cetak</span>
                        <span class="block font-bold text-slate-900">{{ date('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Info Bar -->
        <div class="mb-4 text-xs grid grid-cols-4 gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-300">
            <div>
                <span class="text-slate-500">Program:</span>
                <b class="ml-1 text-slate-900">{{ $filterInfo['jenis'] }}</b>
            </div>
            <div>
                <span class="text-slate-500">Kabupaten:</span>
                <b class="ml-1 text-slate-900">{{ $filterInfo['kota'] }}</b>
            </div>
            <div>
                <span class="text-slate-500">Kecamatan:</span>
                <b class="ml-1 text-slate-900">{{ $filterInfo['kecamatan'] }}</b>
            </div>
            <div>
                <span class="text-slate-500">Periode:</span>
                <b class="ml-1 text-slate-900">{{ $filterInfo['periode'] }}</b>
            </div>
        </div>

        <!-- Main Comparison Table -->
        <table class="w-full text-xs text-left border-collapse border border-slate-300 mb-6">
            <thead>
                <tr class="bg-slate-100 text-slate-800 text-[10px] uppercase font-bold tracking-wider">
                    <th class="border border-slate-300 p-2 text-center w-8">No</th>
                    <th class="border border-slate-300 p-2">Kabupaten / Kota</th>
                    <th class="border border-slate-300 p-2">Kecamatan</th>
                    <th class="border border-slate-300 p-2">Kelurahan / Lembang</th>
                    <th class="border border-slate-300 p-2 text-center">Target Rencana (Titik)</th>
                    <th class="border border-slate-300 p-2 text-right">Target Volume</th>
                    <th class="border border-slate-300 p-2 text-center">Realisasi (Titik)</th>
                    <th class="border border-slate-300 p-2 text-right">Realisasi Volume</th>
                    <th class="border border-slate-300 p-2 text-center">Sisa (Titik)</th>
                    <th class="border border-slate-300 p-2 text-right">Sisa Volume</th>
                    <th class="border border-slate-300 p-2 text-center w-20">Capaian</th>
                    <th class="border border-slate-300 p-2 text-center w-20">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $idx => $row)
                    <tr>
                        <td class="border border-slate-300 p-2 text-center font-mono text-[11px]">{{ $idx + 1 }}</td>
                        <td class="border border-slate-300 p-2 font-semibold text-slate-900">
                            {{ $row['kota'] }}
                        </td>
                        <td class="border border-slate-300 p-2 text-slate-800">
                            {{ $row['kecamatan'] }}
                        </td>
                        <td class="border border-slate-300 p-2 text-slate-800">
                            <span class="font-bold block text-slate-900">{{ $row['kelurahan'] }}</span>
                            @if(!empty($row['dusun_names']))
                                <span class="text-[9px] text-slate-600 block mt-0.5">
                                    Dusun: {{ implode(', ', $row['dusun_names']) }}
                                </span>
                            @endif
                        </td>
                        <td class="border border-slate-300 p-2 text-center">
                            {{ $row['target_titik'] }} Titik
                        </td>
                        <td class="border border-slate-300 p-2 text-right font-bold text-slate-900">
                            {{ number_format($row['target_volume'], 0, ',', '.') }} {{ $row['satuan'] }}
                        </td>
                        <td class="border border-slate-300 p-2 text-center text-emerald-800 font-semibold">
                            {{ $row['realisasi_titik'] }} Titik
                        </td>
                        <td class="border border-slate-300 p-2 text-right font-bold text-emerald-800">
                            {{ number_format($row['realisasi_volume'], 0, ',', '.') }} {{ $row['satuan'] }}
                        </td>
                        <td class="border border-slate-300 p-2 text-center text-slate-600">
                            {{ $row['sisa_titik'] }} Titik
                        </td>
                        <td class="border border-slate-300 p-2 text-right text-slate-700">
                            {{ number_format($row['sisa_volume'], 0, ',', '.') }} {{ $row['satuan'] }}
                        </td>
                        <td class="border border-slate-300 p-2 text-center font-mono font-bold">
                            {{ $row['persentase'] }}%
                        </td>
                        <td class="border border-slate-300 p-2 text-center font-semibold text-[10px]">
                            {{ $row['status_badge'] }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="border border-slate-300 p-6 text-center text-slate-400">
                            Tidak ada data penyaluran pada parameter filter ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($items) > 0)
                <tfoot class="bg-slate-100 font-bold text-slate-900 text-xs border-t-2 border-slate-400">
                    <tr>
                        <td colspan="4" class="border border-slate-300 p-2 text-left uppercase">
                            TOTAL KESELURUHAN ({{ $summary['total_kota'] }} Kab. / {{ $summary['total_kecamatan'] }} Kec. / {{ $summary['total_kelurahan'] }} Desa)
                        </td>
                        <td class="border border-slate-300 p-2 text-center font-bold">
                            {{ $summary['total_target_titik'] }} Titik
                        </td>
                        <td class="border border-slate-300 p-2 text-right font-black">
                            {{ number_format($summary['total_target_volume'], 0, ',', '.') }} Liter
                        </td>
                        <td class="border border-slate-300 p-2 text-center font-bold text-emerald-900">
                            {{ $summary['total_realisasi_titik'] }} Titik
                        </td>
                        <td class="border border-slate-300 p-2 text-right font-black text-emerald-900">
                            {{ number_format($summary['total_realisasi_volume'], 0, ',', '.') }} Liter
                        </td>
                        <td class="border border-slate-300 p-2 text-center font-bold">
                            {{ $summary['total_sisa_titik'] }} Titik
                        </td>
                        <td class="border border-slate-300 p-2 text-right font-black">
                            {{ number_format($summary['total_sisa_volume'], 0, ',', '.') }} Liter
                        </td>
                        <td class="border border-slate-300 p-2 text-center font-mono font-black text-sm">
                            {{ $summary['total_persentase'] }}%
                        </td>
                        <td class="border border-slate-300 p-2 text-center text-[10px] font-black">
                            {{ $summary['total_persentase'] >= 100 ? 'TUNTAS' : 'PROSES' }}
                        </td>
                    </tr>
                </tfoot>
            @endif
        </table>

        <!-- Signature Block -->
        <div class="break-inside-avoid pt-2 text-xs">
            <div class="text-right text-slate-700 mb-4">
                Kabupaten Toraja Utara, {{ date('d F Y') }}
            </div>
            <div class="grid grid-cols-3 gap-6 text-center">
                <div>
                    <p class="font-medium text-slate-600 mb-1">Dibuat Oleh,</p>
                    <p class="text-[11px] text-slate-500 mb-16">Petugas Verifikasi Lapangan</p>
                    <p class="font-bold underline text-slate-950 uppercase">( ........................................ )</p>
                    <span class="text-[10px] text-slate-500 block">Staf Administrasi & Rekap</span>
                </div>
                <div>
                    <p class="font-medium text-slate-600 mb-1">Diperiksa Oleh,</p>
                    <p class="text-[11px] text-slate-500 mb-16">Koordinator Logistik Wilayah</p>
                    <p class="font-bold underline text-slate-950 uppercase">( ........................................ )</p>
                    <span class="text-[10px] text-slate-500 block">Satgas Penyaluran Bantuan</span>
                </div>
                <div>
                    <p class="font-medium text-slate-600 mb-1">Mengetahui & Menyetujui,</p>
                    <p class="text-[11px] text-slate-500 mb-16">Penanggung Jawab Program</p>
                    <p class="font-bold underline text-slate-950 uppercase">( ........................................ )</p>
                    <span class="text-[10px] text-slate-500 block">Aspirasi Dapil Sulawesi Selatan III</span>
                </div>
            </div>
        </div>

        <!-- Print Footer -->
        <div class="mt-8 pt-3 border-t border-slate-200 flex items-center justify-between text-[9px] text-slate-400">
            <span>Sistem Administrasi Penyaluran Bantuan Sosial - Dokumen Rekapitulasi Perbandingan Wilayah</span>
            <span class="font-mono">Waktu Ekspor: {{ date('d/m/Y H:i:s') }} WIB</span>
        </div>

    </div>

</body>
</html>
