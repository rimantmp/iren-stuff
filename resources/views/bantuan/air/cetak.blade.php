<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAST - {{ $penyaluran->kode_transaksi }} - {{ $penyaluran->nama_penerima }}</title>
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
                size: A4 portrait;
                margin: 12mm 15mm 12mm 15mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4">

    <!-- Top Action Toolbar (Hidden during print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <a href="{{ route('bantuan.air.show', $penyaluran->id) }}"
               class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50 transition shadow-sm">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Rincian
            </a>
            <div>
                <span class="text-xs font-bold text-slate-900 block">Pratinjau Lembar Berita Acara Serah Terima (BAST)</span>
                <span class="text-[11px] text-slate-500 font-mono">No. Transaksi: {{ $penyaluran->kode_transaksi }}</span>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <button onclick="window.close()"
                    class="px-3.5 py-2 rounded-lg border border-slate-300 text-xs font-medium text-slate-700 bg-slate-50 hover:bg-slate-100 transition">
                Tutup Jendela
            </button>
            <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold shadow transition focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Dokumen / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Main Printable Sheet (A4 Dimensions) -->
    <div class="page-container max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-xl shadow-md border border-slate-200 text-slate-900">

        <!-- Kop Surat Resmi Berita Acara -->
        <div class="border-b-4 border-double border-slate-900 pb-3 mb-6">
            <div class="flex items-center justify-between gap-4">
                <!-- Lambang / Badge Kiri -->
                <div class="w-16 h-16 rounded-xl bg-blue-700 text-white flex flex-col items-center justify-center flex-shrink-0 shadow-sm border border-blue-800">
                    <span class="font-black text-xl tracking-tighter leading-none">SB</span>
                    <span class="text-[8px] font-bold tracking-widest uppercase mt-0.5">Aspirasi</span>
                </div>

                <!-- Teks Kop Surat -->
                <div class="text-center flex-1 px-2">
                    <h2 class="text-xs font-bold text-slate-600 tracking-widest uppercase mb-0.5">PROGRAM ASPIRASI MASYARAKAT SULAWESI SELATAN III</h2>
                    <h1 class="text-base sm:text-lg font-black text-slate-950 uppercase tracking-tight">TIM LOGISTIK & SATUAN TUGAS PENYALURAN AIR BERSIH</h1>
                    <p class="text-[11px] text-slate-600 font-medium">Sekretariat Penyaluran Lapangan: Wilayah Kabupaten Toraja Utara & Sekitarnya</p>
                    <p class="text-[10px] text-slate-500">Layanan Distribusi Logistik Air Bersih Bebas Biaya (Gratis) untuk Masyarakat</p>
                </div>

                <!-- Kode QR / ID Pojok Kanan -->
                <div class="w-16 flex-shrink-0 text-right">
                    <div class="border border-slate-300 p-1 rounded inline-block bg-slate-50 text-center">
                        <span class="block text-[8px] font-bold text-slate-500 uppercase">Dokumen</span>
                        <span class="block font-mono text-[9px] font-bold text-blue-700">RESMI</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Judul Berita Acara -->
        <div class="text-center mb-6">
            <h2 class="text-base sm:text-lg font-black uppercase tracking-wider text-slate-950 underline decoration-2 underline-offset-4">
                BERITA ACARA SERAH TERIMA (BAST)
            </h2>
            <p class="text-xs font-semibold text-slate-700 mt-1 uppercase tracking-wide">
                DISTRIBUSI BANTUAN LOGISTIK AIR BERSIH
            </p>
            <div class="inline-block mt-1 px-3 py-0.5 bg-slate-100 rounded border border-slate-300 font-mono text-xs font-bold text-slate-900">
                NOMOR: BAST/AIR/{{ $penyaluran->kode_transaksi }}
            </div>
        </div>

        <!-- Narasi Pembuka -->
        <div class="text-xs leading-relaxed text-slate-800 mb-4 text-justify">
            <p>
                Pada hari ini, <b>{{ \Carbon\Carbon::parse($penyaluran->tanggal_penyaluran ?: $penyaluran->tanggal_rencana)->locale('id')->isoFormat('dddd') }}</b>, tanggal <b>{{ \Carbon\Carbon::parse($penyaluran->tanggal_penyaluran ?: $penyaluran->tanggal_rencana)->locale('id')->isoFormat('D MMMM Y') }}</b>, bertempat di titik lokasi penampungan/distribusi masyarakat, kami yang bertanda tangan di bawah ini telah melaksanakan serah terima bantuan pasokan air bersih:
            </p>
        </div>

        <!-- Identitas Para Pihak (Grid 2 Kolom) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5 text-xs">
            <!-- PIHAK PERTAMA -->
            <div class="border border-slate-300 rounded-lg p-3.5 bg-slate-50/60">
                <div class="flex items-center justify-between pb-1.5 mb-2 border-b border-slate-300">
                    <span class="font-bold text-blue-900 uppercase tracking-wide text-[11px]">PIHAK PERTAMA (Yang Menyerahkan)</span>
                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 font-semibold">Pelaksana</span>
                </div>
                <table class="w-full text-xs">
                    <tr class="align-top">
                        <td class="w-28 text-slate-500 py-0.5">Nama Petugas</td>
                        <td class="w-2 py-0.5">:</td>
                        <td class="font-bold text-slate-900 py-0.5">{{ $penyaluran->nama_petugas ?: 'Tim Satgas Distribusi Air' }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="text-slate-500 py-0.5">No. Armada Tangki</td>
                        <td class="py-0.5">:</td>
                        <td class="font-semibold text-slate-800 py-0.5">{{ $penyaluran->nomor_armada ?: '-' }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="text-slate-500 py-0.5">Sumber Pasokan</td>
                        <td class="py-0.5">:</td>
                        <td class="text-slate-800 py-0.5">{{ $penyaluran->sumber_air ?: 'Depot Penampungan Resmi' }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="text-slate-500 py-0.5">Status Instansi</td>
                        <td class="py-0.5">:</td>
                        <td class="text-slate-800 py-0.5">Satuan Tugas Logistik Aspirasi Dapil Sulsel III</td>
                    </tr>
                </table>
            </div>

            <!-- PIHAK KEDUA -->
            <div class="border border-slate-300 rounded-lg p-3.5 bg-slate-50/60">
                <div class="flex items-center justify-between pb-1.5 mb-2 border-b border-slate-300">
                    <span class="font-bold text-emerald-900 uppercase tracking-wide text-[11px]">PIHAK KEDUA (Yang Menerima)</span>
                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">Penerima</span>
                </div>
                <table class="w-full text-xs">
                    <tr class="align-top">
                        <td class="w-28 text-slate-500 py-0.5">Nama Penerima</td>
                        <td class="w-2 py-0.5">:</td>
                        <td class="font-bold text-slate-900 py-0.5">{{ $penyaluran->nama_penerima }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="text-slate-500 py-0.5">No. Kontak / HP</td>
                        <td class="py-0.5">:</td>
                        <td class="text-slate-800 py-0.5">{{ $penyaluran->kontak_penerima ?: '-' }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="text-slate-500 py-0.5">Alamat / Lokasi</td>
                        <td class="py-0.5">:</td>
                        <td class="text-slate-800 py-0.5">{{ $penyaluran->alamat_detail ?: '-' }}</td>
                    </tr>
                    <tr class="align-top">
                        <td class="text-slate-500 py-0.5">Kelurahan / Lembang</td>
                        <td class="py-0.5">:</td>
                        <td class="text-slate-800 py-0.5">
                            Kel. {{ $penyaluran->kelurahan?->nama ?? '-' }}, Kec. {{ $penyaluran->kecamatan?->nama ?? '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Tabel Rincian Distribusi Bantuan -->
        <div class="mb-5">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wide mb-2 flex items-center">
                <span class="w-2 h-2 rounded-full bg-blue-700 mr-1.5"></span>
                Rincian Bantuan yang Diserahterimakan:
            </h3>

            <table class="w-full text-xs border-collapse border border-slate-300">
                <thead>
                    <tr class="bg-slate-100 text-slate-800">
                        <th class="border border-slate-300 p-2 text-center w-10">No</th>
                        <th class="border border-slate-300 p-2 text-left">Deskripsi Program Bantuan</th>
                        <th class="border border-slate-300 p-2 text-center w-28">Metode Penyaluran</th>
                        <th class="border border-slate-300 p-2 text-center w-36">Sasaran Penerima</th>
                        <th class="border border-slate-300 p-2 text-right w-36">Volume / Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-slate-300 p-2 text-center font-bold">1</td>
                        <td class="border border-slate-300 p-2">
                            <span class="font-bold text-slate-950 block">Pasokan Air Bersih Siap Pakai & Higienis</span>
                            <span class="text-[11px] text-slate-600 block mt-0.5">
                                Wilayah: Kel./Lembang {{ $penyaluran->kelurahan?->nama }}, Kec. {{ $penyaluran->kecamatan?->nama }}, {{ $penyaluran->kota?->nama }}
                            </span>
                            @if($penyaluran->catatan)
                                <span class="text-[10px] text-slate-500 italic block mt-1">Catatan: {{ $penyaluran->catatan }}</span>
                            @endif
                        </td>
                        <td class="border border-slate-300 p-2 text-center font-medium">
                            {{ $penyaluran->metode_distribusi }}
                        </td>
                        <td class="border border-slate-300 p-2 text-center font-medium">
                            @if($penyaluran->jumlah_kk || $penyaluran->jumlah_jiwa)
                                {{ $penyaluran->jumlah_kk ? $penyaluran->jumlah_kk . ' KK' : '' }}{{ ($penyaluran->jumlah_kk && $penyaluran->jumlah_jiwa) ? ' / ' : '' }}{{ $penyaluran->jumlah_jiwa ? $penyaluran->jumlah_jiwa . ' Jiwa' : '' }}
                            @else
                                Warga Setempat
                            @endif
                        </td>
                        <td class="border border-slate-300 p-2 text-right font-black text-sm text-blue-950">
                            {{ number_format($penyaluran->jumlah_bantuan, 0, ',', '.') }} {{ $penyaluran->satuan }}
                        </td>
                    </tr>
                    <tr class="bg-slate-50 font-semibold">
                        <td colspan="4" class="border border-slate-300 p-2 text-right text-slate-700">
                            STATUS REALISASI PENYERAHAN:
                        </td>
                        <td class="border border-slate-300 p-2 text-right font-mono font-bold text-emerald-800">
                            {{ $penyaluran->status == 'TERSALURKAN' ? 'TERSALURKAN / DITERIMA' : $penyaluran->status }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Klausul & Pernyataan Serah Terima -->
        <div class="p-3.5 rounded-lg border border-slate-300 bg-slate-50 text-[11px] leading-relaxed text-slate-800 mb-6">
            <p class="font-semibold mb-1 text-slate-900">Ketentuan & Pernyataan Keabsahan:</p>
            <ol class="list-decimal pl-4 space-y-0.5">
                <li><b>PIHAK PERTAMA</b> telah menyerahkan seluruh volume bantuan air bersih sesuai dengan spesifikasi dan jumlah di atas dalam kondisi jernih, higienis, dan layak pakai.</li>
                <li><b>PIHAK KEDUA</b> telah memeriksa dan menerima bantuan tersebut secara penuh atas nama warga masyarakat penerima manfaat di lokasi yang disepakati.</li>
                <li>Penyaluran bantuan ini disalurkan secara <b>CUMA-CUMA / GRATIS</b> tanpa dipungut biaya apapun dari masyarakat penerima.</li>
            </ol>
        </div>

        @if($penyaluran->foto_dokumentasi)
            <!-- Lampiran Foto Dokumentasi Lapangan -->
            <div class="mb-6 p-3 border border-slate-200 rounded-lg bg-slate-50/50 break-inside-avoid">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Foto Dokumentasi Serah Terima Lapangan:</span>
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('storage/' . $penyaluran->foto_dokumentasi) }}"
                         alt="Bukti Serah Terima"
                         class="h-28 max-w-xs object-cover rounded border border-slate-300 shadow-sm">
                    <div class="text-[11px] text-slate-600">
                        <p class="font-semibold text-slate-900">Armada / Lokasi Distribusi Terverifikasi</p>
                        <p>Titik: {{ $penyaluran->alamat_detail ?: 'Lokasi Warga' }}</p>
                        <p class="font-mono text-[10px] text-slate-500 mt-1">
                            Koordinat: {{ $penyaluran->latitude ?: '-' }}, {{ $penyaluran->longitude ?: '-' }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Lembar Tanda Tangan Tiga Pihak (Bersebelahan) -->
        <div class="break-inside-avoid pt-2">
            <div class="text-right text-xs text-slate-700 mb-4">
                {{ $penyaluran->kota?->nama ?? 'Toraja Utara' }},
                {{ \Carbon\Carbon::parse($penyaluran->tanggal_penyaluran ?: $penyaluran->tanggal_rencana)->locale('id')->isoFormat('D MMMM Y') }}
            </div>

            <div class="grid grid-cols-3 gap-6 text-center text-xs">
                <!-- Pihak Pertama -->
                <div>
                    <p class="font-medium text-slate-600 mb-1">PIHAK PERTAMA</p>
                    <p class="text-[11px] text-slate-500 mb-16">Petugas Satgas Penyalur,</p>
                    <p class="font-bold underline text-slate-950 uppercase">{{ $penyaluran->nama_petugas ?: '( ................................... )' }}</p>
                    <span class="text-[10px] text-slate-500 block">Pengemudi / Petugas Tangki</span>
                </div>

                <!-- Pihak Kedua -->
                <div>
                    <p class="font-medium text-slate-600 mb-1">PIHAK KEDUA</p>
                    <p class="text-[11px] text-slate-500 mb-16">Penerima Manfaat / Warga,</p>
                    <p class="font-bold underline text-slate-950 uppercase">{{ $penyaluran->nama_penerima }}</p>
                    <span class="text-[10px] text-slate-500 block">Penanggung Jawab Lokasi</span>
                </div>

                <!-- Mengetahui -->
                <div>
                    <p class="font-medium text-slate-600 mb-1">MENGETAHUI</p>
                    <p class="text-[11px] text-slate-500 mb-16">Pemerintah Desa / Lembang / RT,</p>
                    <p class="font-bold underline text-slate-950">( ................................... )</p>
                    <span class="text-[10px] text-slate-500 block">Kepala Lembang / Tokoh Setempat</span>
                </div>
            </div>
        </div>

        <!-- Footer Dokumen -->
        <div class="mt-8 pt-3 border-t border-slate-200 flex items-center justify-between text-[9px] text-slate-400">
            <span>Dokumen Administrasi Bantuan Air Bersih - Sistem Penyaluran Bantuan Sosial</span>
            <span class="font-mono">Dicetak pada: {{ now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }}</span>
        </div>

    </div>

</body>
</html>
