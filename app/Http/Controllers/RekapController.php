<?php

namespace App\Http\Controllers;

use App\Models\JenisBantuan;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\PenyaluranBantuan;
use App\Models\Provinsi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\View\View;
use Shuchkin\SimpleXLSXGen;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekapController extends Controller
{
    /**
     * Display report and summary of aid distributions.
     */
    public function index(Request $request): View
    {
        $query = PenyaluranBantuan::with(['jenisBantuan', 'provinsi', 'kota', 'kecamatan', 'kelurahan', 'dusun']);

        // Filters
        if ($request->filled('jenis_bantuan_id')) {
            $query->where('jenis_bantuan_id', $request->query('jenis_bantuan_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('provinsi_id')) {
            $query->where('provinsi_id', $request->query('provinsi_id'));
        }

        if ($request->filled('tanggal_mulai')) {
            $tglMulai = $this->normalizeDate($request->query('tanggal_mulai'));
            if ($tglMulai) {
                $query->whereDate('tanggal_rencana', '>=', $tglMulai);
            }
        }

        if ($request->filled('tanggal_akhir')) {
            $tglAkhir = $this->normalizeDate($request->query('tanggal_akhir'));
            if ($tglAkhir) {
                $query->whereDate('tanggal_rencana', '<=', $tglAkhir);
            }
        }

        // Clone query for aggregation
        $totalTransaksi = (clone $query)->count();
        $totalKk = (clone $query)->sum('jumlah_kk');
        $totalJiwa = (clone $query)->sum('jumlah_jiwa');
        $totalVolumeAir = (clone $query)->whereHas('jenisBantuan', function ($q): void {
            $q->where('slug', 'air');
        })->where('status', 'TERSALURKAN')->sum('jumlah_bantuan');

        $laporanList = $query->latest('tanggal_rencana')->paginate(20)->withQueryString();

        $semuaJenisBantuan = JenisBantuan::where('status_aktif', true)->get();
        $semuaProvinsi = Provinsi::orderBy('nama')->get();

        return view('rekap.index', compact(
            'laporanList',
            'totalTransaksi',
            'totalKk',
            'totalJiwa',
            'totalVolumeAir',
            'semuaJenisBantuan',
            'semuaProvinsi'
        ));
    }

    /**
     * Print-ready printable report view.
     */
    public function cetak(Request $request): View
    {
        $query = PenyaluranBantuan::with(['jenisBantuan', 'provinsi', 'kota', 'kecamatan', 'kelurahan', 'dusun']);

        if ($request->filled('jenis_bantuan_id')) {
            $query->where('jenis_bantuan_id', $request->query('jenis_bantuan_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('provinsi_id')) {
            $query->where('provinsi_id', $request->query('provinsi_id'));
        }

        if ($request->filled('tanggal_mulai')) {
            $tglMulai = $this->normalizeDate($request->query('tanggal_mulai'));
            if ($tglMulai) {
                $query->whereDate('tanggal_rencana', '>=', $tglMulai);
            }
        }

        if ($request->filled('tanggal_akhir')) {
            $tglAkhir = $this->normalizeDate($request->query('tanggal_akhir'));
            if ($tglAkhir) {
                $query->whereDate('tanggal_rencana', '<=', $tglAkhir);
            }
        }

        $laporanList = $query->orderBy('tanggal_rencana', 'asc')->get();

        $tglMulaiText = $request->query('tanggal_mulai') ? date('d/m/Y', strtotime($this->normalizeDate($request->query('tanggal_mulai')))) : 'Awal';
        $tglAkhirText = $request->query('tanggal_akhir') ? date('d/m/Y', strtotime($this->normalizeDate($request->query('tanggal_akhir')))) : 'Sekarang';

        $filterInfo = [
            'jenis' => $request->filled('jenis_bantuan_id') ? JenisBantuan::find($request->query('jenis_bantuan_id'))?->nama : 'Semua Jenis Bantuan',
            'status' => $request->query('status', 'Semua Status'),
            'periode' => "{$tglMulaiText} s/d {$tglAkhirText}",
        ];

        return view('rekap.cetak', compact('laporanList', 'filterInfo'));
    }

    /**
     * Display comparison report (Target Rencana vs Realisasi Tersalurkan per Wilayah).
     */
    public function perbandingan(Request $request): View
    {
        $data = $this->getPerbandinganData($request);
        $items = $data['items'];
        $summary = $data['summary'];
        $selectedKotaId = $data['selected_kota_id'];

        // Pagination setup
        $perPage = (int) $request->query('per_page', 25);
        if ($perPage <= 0) {
            $perPage = 25;
        }

        $currentPage = Paginator::resolveCurrentPage('page');
        $currentItems = array_slice($items, ($currentPage - 1) * $perPage, $perPage);

        $paginatedItems = new LengthAwarePaginator(
            $currentItems,
            count($items),
            $perPage,
            $currentPage,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
                'query' => $request->query(),
            ]
        );

        $semuaJenisBantuan = JenisBantuan::where('status_aktif', true)->get();
        // Daftar Kabupaten/Kota di Sulawesi Selatan (Dapil Sulsel III / Wilayah Kerja)
        $semuaKota = Kota::where('id', 'like', '73%')
            ->orderBy('nama')
            ->get();

        $kecamatanQuery = Kecamatan::query();
        if ($selectedKotaId !== 'SEMUA') {
            $kecamatanQuery->where('id', 'like', $selectedKotaId.'%');
        } else {
            $kecamatanQuery->where('id', 'like', '73%');
        }
        $semuaKecamatan = $kecamatanQuery->orderBy('nama')->get();

        return view('rekap.perbandingan', compact(
            'paginatedItems',
            'summary',
            'semuaJenisBantuan',
            'semuaKota',
            'semuaKecamatan',
            'selectedKotaId',
            'perPage'
        ));
    }

    /**
     * Display printable view for comparison report.
     */
    public function cetakPerbandingan(Request $request): View
    {
        $data = $this->getPerbandinganData($request);
        $items = $data['items'];
        $summary = $data['summary'];
        $selectedKotaId = $data['selected_kota_id'];

        $statusLabel = match ($request->query('filter_status')) {
            'sudah' => 'Hanya yang Sudah Tersalurkan',
            'belum' => 'Hanya yang Belum Tersalurkan',
            'belum_tersentuh' => 'Hanya yang Belum Ada Alokasi',
            default => 'Semua Wilayah (Sudah & Belum)',
        };

        $tglMulaiText = $request->query('tanggal_mulai') ? date('d/m/Y', strtotime($this->normalizeDate($request->query('tanggal_mulai')))) : 'Awal';
        $tglAkhirText = $request->query('tanggal_akhir') ? date('d/m/Y', strtotime($this->normalizeDate($request->query('tanggal_akhir')))) : 'Sekarang';

        $filterInfo = [
            'jenis' => $request->filled('jenis_bantuan_id') ? JenisBantuan::find($request->query('jenis_bantuan_id'))?->nama : 'Semua Program Bantuan',
            'kota' => $selectedKotaId !== 'SEMUA' ? Kota::find($selectedKotaId)?->nama : 'Semua Kabupaten/Kota',
            'kecamatan' => $request->filled('kecamatan_id') ? Kecamatan::find($request->query('kecamatan_id'))?->nama : 'Semua Kecamatan',
            'status' => $statusLabel,
            'periode' => "{$tglMulaiText} s/d {$tglAkhirText}",
        ];

        return view('rekap.perbandingan-cetak', compact('items', 'summary', 'filterInfo'));
    }

    /**
     * Export comparison data as genuine Microsoft Excel (.xlsx) workbook.
     */
    public function exportPerbandinganExcel(Request $request): StreamedResponse
    {
        $data = $this->getPerbandinganData($request);
        $items = $data['items'];
        $summary = $data['summary'];

        $filename = 'Laporan-Perbandingan-Penyaluran-'.date('Ymd-His').'.xlsx';

        $rows = [
            ['<style font-size="14"><b>LAPORAN PERBANDINGAN TARGET DAN REALISASI PENYALURAN BANTUAN SOSIAL</b></style>'],
            ['<b>Sistem Penyaluran Bantuan Sosial - Aspirasi Sulawesi Selatan III</b>'],
            ['Tanggal Unduh: '.date('d/m/Y H:i').' WITA'],
            [],
            [
                '<b>No</b>',
                '<b>Kabupaten / Kota</b>',
                '<b>Kecamatan</b>',
                '<b>Kelurahan / Lembang</b>',
                '<b>Dusun / Lembang Sasaran</b>',
                '<center><b>Target Rencana (Titik)</b></center>',
                '<right><b>Target Volume (Liter)</b></right>',
                '<center><b>Realisasi Tersalurkan (Titik)</b></center>',
                '<right><b>Realisasi Tersalurkan (Volume Liter)</b></right>',
                '<center><b>Sisa Belum Salur (Titik)</b></center>',
                '<right><b>Sisa Belum Salur (Volume Liter)</b></right>',
                '<center><b>Progres Capaian (%)</b></center>',
                '<center><b>Status Pelaksanaan</b></center>',
            ],
        ];

        foreach ($items as $idx => $row) {
            $rows[] = [
                $idx + 1,
                $row['kota'],
                $row['kecamatan'],
                $row['kelurahan'],
                ! empty($row['dusun_names']) ? implode('; ', $row['dusun_names']) : '-',
                $row['target_titik'],
                $row['target_volume'],
                $row['realisasi_titik'],
                $row['realisasi_volume'],
                $row['sisa_titik'],
                $row['sisa_volume'],
                $row['persentase'].'%',
                $row['status_badge'],
            ];
        }

        $rows[] = [];
        $rows[] = [
            '<b>TOTAL</b>',
            '<b>'.$summary['total_kota'].' Kabupaten/Kota</b>',
            '<b>'.$summary['total_kecamatan'].' Kecamatan</b>',
            '<b>'.$summary['total_kelurahan'].' Kelurahan/Lembang</b>',
            '<b>'.($summary['total_dusun_terbantu'] ?? 0).' Dusun</b>',
            '<b>'.$summary['total_target_titik'].'</b>',
            '<b>'.$summary['total_target_volume'].'</b>',
            '<b>'.$summary['total_realisasi_titik'].'</b>',
            '<b>'.$summary['total_realisasi_volume'].'</b>',
            '<b>'.$summary['total_sisa_titik'].'</b>',
            '<b>'.$summary['total_sisa_volume'].'</b>',
            '<b>'.$summary['total_persentase'].'%</b>',
            '<b>'.($summary['total_persentase'] >= 100 ? 'Selesai 100%' : 'Dalam Proses').'</b>',
        ];

        $xlsx = SimpleXLSXGen::fromArray($rows, 'Komparasi Wilayah');

        return response()->streamDownload(function () use ($xlsx): void {
            echo (string) $xlsx;
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Helper to compute comparison dataset grouped by Kota, Kecamatan, and Kelurahan.
     * Includes all administrative regions (both serviced and unserviced/blank spots).
     *
     * @return array{items: array<int, array<string, mixed>>, summary: array<string, mixed>, selected_kota_id: string}
     */
    private function getPerbandinganData(Request $request): array
    {
        // Default ke Kabupaten Toraja Utara (7326) jika tidak dipilih atau jika baru pertama kali buka
        $kotaId = $request->query('kota_id', '7326');
        $kotaObj = Kota::find($kotaId);
        $defaultKotaNama = $kotaObj?->nama ?? 'Kabupaten Toraja Utara';

        // Ambil data transaksi yang ada di database
        $queryTx = PenyaluranBantuan::with(['jenisBantuan', 'provinsi', 'kota', 'kecamatan', 'kelurahan', 'dusun']);

        if ($request->filled('jenis_bantuan_id')) {
            $queryTx->where('jenis_bantuan_id', $request->query('jenis_bantuan_id'));
        }

        if ($request->filled('tanggal_mulai')) {
            $tglMulai = $this->normalizeDate($request->query('tanggal_mulai'));
            if ($tglMulai) {
                $queryTx->whereDate('tanggal_rencana', '>=', $tglMulai);
            }
        }

        if ($request->filled('tanggal_akhir')) {
            $tglAkhir = $this->normalizeDate($request->query('tanggal_akhir'));
            if ($tglAkhir) {
                $queryTx->whereDate('tanggal_rencana', '<=', $tglAkhir);
            }
        }

        if ($kotaId !== 'SEMUA') {
            $queryTx->where('kota_id', $kotaId);
        }

        if ($request->filled('kecamatan_id')) {
            $queryTx->where('kecamatan_id', $request->query('kecamatan_id'));
        }

        $allTxRecords = $queryTx->get();
        $txByKelurahan = $allTxRecords->groupBy('kelurahan_id');

        // Master Wilayah Kelurahan
        $kelurahanQuery = Kelurahan::query();
        if ($kotaId !== 'SEMUA') {
            $kelurahanQuery->where('id', 'like', $kotaId.'%');
        } else {
            // Jika SEMUA dipilih, ambil kelurahan yang memiliki transaksi
            $kelurahanQuery->whereIn('id', $txByKelurahan->keys());
        }

        if ($request->filled('kecamatan_id')) {
            $kelurahanQuery->where('id', 'like', $request->query('kecamatan_id').'%');
        }

        $allMasterKelurahan = $kelurahanQuery->orderBy('id')->get();

        // Peta nama kecamatan untuk lookup
        $prefix = $kotaId !== 'SEMUA' ? $kotaId : '73';
        $kecamatansMap = Kecamatan::where('id', 'like', $prefix.'%')
            ->get()
            ->keyBy('id');

        // Peta nama kota untuk lookup
        $kotaMap = Kota::where('id', 'like', '73%')->get()->keyBy('id');

        $items = [];
        $totalTargetTitik = 0;
        $totalTargetVolume = 0;
        $totalRealisasiTitik = 0;
        $totalRealisasiVolume = 0;
        $totalSisaTitik = 0;
        $totalSisaVolume = 0;
        $countDesaSelesai = 0;
        $countDesaSebagian = 0;
        $countDesaRencana = 0;
        $countDesaBelum = 0;

        foreach ($allMasterKelurahan as $kel) {
            $kId = (string) $kel->id;
            $kecId = substr($kId, 0, 6);
            $regencyId = substr($kId, 0, 4);

            $thisKotaNama = $kotaMap->get($regencyId)?->nama ?? $defaultKotaNama;
            $thisKecNama = $kecamatansMap->get($kecId)?->nama ?? ('Kecamatan '.$kecId);

            $records = $txByKelurahan->get($kId, collect());

            $targetTitik = $records->count();
            $targetVolume = (float) $records->sum('jumlah_bantuan');

            $tersalurkan = $records->where('status', 'TERSALURKAN');
            $realisasiTitik = $tersalurkan->count();
            $realisasiVolume = (float) $tersalurkan->sum('jumlah_bantuan');

            $sisaTitik = max(0, $targetTitik - $realisasiTitik);
            $sisaVolume = max(0.0, $targetVolume - $realisasiVolume);

            $persentase = $targetVolume > 0 ? round(($realisasiVolume / $targetVolume) * 100, 1) : 0.0;

            if ($targetTitik === 0) {
                $statusBadge = 'Belum Tersentuh';
                $statusColor = 'slate';
                $countDesaBelum++;
            } elseif ($persentase >= 100) {
                $statusBadge = 'Selesai';
                $statusColor = 'emerald';
                $countDesaSelesai++;
            } elseif ($realisasiTitik > 0) {
                $statusBadge = 'Sebagian';
                $statusColor = 'blue';
                $countDesaSebagian++;
            } else {
                $statusBadge = 'Rencana';
                $statusColor = 'amber';
                $countDesaRencana++;
            }

            $satuan = $records->first()?->satuan ?: 'Liter';

            $dusunNames = $records->filter(fn ($r) => ! empty($r->dusun_id) && $r->dusun)
                ->map(fn ($r) => $r->dusun->nama)
                ->unique()
                ->values()
                ->all();

            $item = [
                'kota_id' => $regencyId,
                'kota' => $thisKotaNama,
                'kecamatan_id' => $kecId,
                'kecamatan' => $thisKecNama,
                'kelurahan_id' => $kId,
                'kelurahan' => $kel->nama,
                'dusun_names' => $dusunNames,
                'target_titik' => $targetTitik,
                'target_volume' => $targetVolume,
                'realisasi_titik' => $realisasiTitik,
                'realisasi_volume' => $realisasiVolume,
                'sisa_titik' => $sisaTitik,
                'sisa_volume' => $sisaVolume,
                'persentase' => $persentase,
                'status_badge' => $statusBadge,
                'status_color' => $statusColor,
                'satuan' => $satuan,
            ];

            // Filter Ketercakupan Status
            $filterStatus = $request->query('filter_status');
            if ($filterStatus === 'sudah' && $realisasiTitik === 0) {
                continue; // Hanya yang sudah tersalurkan (realisasi > 0)
            }
            if ($filterStatus === 'belum' && $realisasiTitik > 0) {
                continue; // Hanya yang belum tersalurkan (realisasi == 0)
            }
            if ($filterStatus === 'belum_tersentuh' && $statusBadge !== 'Belum Tersentuh') {
                continue; // Hanya yang blank spot / belum ada alokasi sama sekali
            }

            $items[] = $item;

            $totalTargetTitik += $targetTitik;
            $totalTargetVolume += $targetVolume;
            $totalRealisasiTitik += $realisasiTitik;
            $totalRealisasiVolume += $realisasiVolume;
            $totalSisaTitik += $sisaTitik;
            $totalSisaVolume += $sisaVolume;
        }

        // Urutkan berdasarkan Nama Kabupaten/Kota, lalu Nama Kecamatan, lalu Nama Kelurahan
        usort($items, function (array $a, array $b): int {
            $cmpKota = strcmp($a['kota'], $b['kota']);
            if ($cmpKota !== 0) {
                return $cmpKota;
            }

            $cmpKec = strcmp($a['kecamatan'], $b['kecamatan']);
            if ($cmpKec !== 0) {
                return $cmpKec;
            }

            return strcmp($a['kelurahan'], $b['kelurahan']);
        });

        $totalPersentase = $totalTargetVolume > 0 ? round(($totalRealisasiVolume / $totalTargetVolume) * 100, 1) : 0.0;

        $uniqueKota = count(array_unique(array_column($items, 'kota')));
        $uniqueKecamatan = count(array_unique(array_column($items, 'kecamatan')));
        $uniqueKelurahan = count($items);
        $totalDusunTerbantu = $allTxRecords->filter(fn ($r) => ! empty($r->dusun_id))->pluck('dusun_id')->unique()->count();

        $totalDesaTersalur = $countDesaSelesai + $countDesaSebagian;

        return [
            'items' => $items,
            'summary' => [
                'total_kota' => $uniqueKota,
                'total_kecamatan' => $uniqueKecamatan,
                'total_kelurahan' => $uniqueKelurahan,
                'total_dusun_terbantu' => $totalDusunTerbantu,
                'total_target_titik' => $totalTargetTitik,
                'total_target_volume' => $totalTargetVolume,
                'total_realisasi_titik' => $totalRealisasiTitik,
                'total_realisasi_volume' => $totalRealisasiVolume,
                'total_sisa_titik' => $totalSisaTitik,
                'total_sisa_volume' => $totalSisaVolume,
                'total_persentase' => $totalPersentase,
                'desa_tersalur' => $totalDesaTersalur,
                'desa_belum_tersalur' => $countDesaRencana + $countDesaBelum,
                'desa_selesai' => $countDesaSelesai,
                'desa_sebagian' => $countDesaSebagian,
                'desa_rencana' => $countDesaRencana,
                'desa_blank' => $countDesaBelum,
            ],
            'selected_kota_id' => $kotaId,
        ];
    }

    /**
     * Konversi input tanggal dd/mm/yyyy atau yyyy-mm-dd menjadi format standar yyyy-mm-dd.
     */
    protected function normalizeDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        $date = trim($date);

        if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $date, $matches)) {
            return sprintf('%04d-%02d-%02d', (int) $matches[3], (int) $matches[2], (int) $matches[1]);
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable) {
            return $date;
        }
    }
}
