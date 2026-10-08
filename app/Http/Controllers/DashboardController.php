<?php

namespace App\Http\Controllers;

use App\Models\JenisBantuan;
use App\Models\PenyaluranBantuan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $airBantuan = JenisBantuan::where('slug', 'air')->first();
        $airBantuanId = $airBantuan?->id;

        // Metrik Khusus Air
        $totalPenyaluranAir = PenyaluranBantuan::where('jenis_bantuan_id', $airBantuanId)->count();
        $volumeAirTersalurkan = PenyaluranBantuan::where('jenis_bantuan_id', $airBantuanId)
            ->where('status', 'TERSALURKAN')
            ->sum('jumlah_bantuan');
        $totalJiwaAir = PenyaluranBantuan::where('jenis_bantuan_id', $airBantuanId)->sum('jumlah_jiwa');
        $totalKkAir = PenyaluranBantuan::where('jenis_bantuan_id', $airBantuanId)->sum('jumlah_kk');

        // Status counts
        $statusCounts = [
            'RENCANA' => PenyaluranBantuan::where('jenis_bantuan_id', $airBantuanId)->where('status', 'RENCANA')->count(),
            'PROSES' => PenyaluranBantuan::where('jenis_bantuan_id', $airBantuanId)->where('status', 'PROSES')->count(),
            'TERSALURKAN' => PenyaluranBantuan::where('jenis_bantuan_id', $airBantuanId)->where('status', 'TERSALURKAN')->count(),
        ];

        // Sebaran data titik koordinat untuk Leaflet Map
        $mapTitik = PenyaluranBantuan::with(['kelurahan', 'kecamatan', 'kota', 'provinsi'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0)
            ->where('longitude', '!=', 0)
            ->get()
            ->map(function (PenyaluranBantuan $item): array {
                return [
                    'id' => $item->id,
                    'kode' => $item->kode_transaksi,
                    'nama_penerima' => $item->nama_penerima,
                    'status' => $item->status,
                    'jumlah' => number_format($item->jumlah_bantuan, 0, ',', '.').' '.$item->satuan,
                    'lokasi' => ($item->kelurahan?->nama ?? '').', '.($item->kecamatan?->nama ?? '').', '.($item->kota?->nama ?? ''),
                    'lat' => $item->latitude,
                    'lng' => $item->longitude,
                    'tanggal' => $item->tanggal_penyaluran ? $item->tanggal_penyaluran->format('d/m/Y') : $item->tanggal_rencana->format('d/m/Y'),
                ];
            });

        // 5 Penyaluran Air Terbaru
        $penyaluranTerbaru = PenyaluranBantuan::with(['kelurahan', 'kecamatan', 'kota', 'provinsi'])
            ->where('jenis_bantuan_id', $airBantuanId)
            ->latest()
            ->take(5)
            ->get();

        // List semua jenis bantuan
        $semuaJenisBantuan = JenisBantuan::withCount('penyaluran')->get();

        return view('dashboard.index', compact(
            'totalPenyaluranAir',
            'volumeAirTersalurkan',
            'totalJiwaAir',
            'totalKkAir',
            'statusCounts',
            'mapTitik',
            'penyaluranTerbaru',
            'semuaJenisBantuan'
        ));
    }
}
