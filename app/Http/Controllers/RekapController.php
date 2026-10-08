<?php

namespace App\Http\Controllers;

use App\Models\JenisBantuan;
use App\Models\PenyaluranBantuan;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RekapController extends Controller
{
    /**
     * Display report and summary of aid distributions.
     */
    public function index(Request $request): View
    {
        $query = PenyaluranBantuan::with(['jenisBantuan', 'provinsi', 'kota', 'kecamatan', 'kelurahan']);

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
            $query->whereDate('tanggal_rencana', '>=', $request->query('tanggal_mulai'));
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal_rencana', '<=', $request->query('tanggal_akhir'));
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
        $query = PenyaluranBantuan::with(['jenisBantuan', 'provinsi', 'kota', 'kecamatan', 'kelurahan']);

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
            $query->whereDate('tanggal_rencana', '>=', $request->query('tanggal_mulai'));
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal_rencana', '<=', $request->query('tanggal_akhir'));
        }

        $laporanList = $query->orderBy('tanggal_rencana', 'asc')->get();

        $filterInfo = [
            'jenis' => $request->filled('jenis_bantuan_id') ? JenisBantuan::find($request->query('jenis_bantuan_id'))?->nama : 'Semua Jenis Bantuan',
            'status' => $request->query('status', 'Semua Status'),
            'periode' => ($request->query('tanggal_mulai') ? date('d/m/Y', strtotime($request->query('tanggal_mulai'))) : 'Awal').' s/d '.($request->query('tanggal_akhir') ? date('d/m/Y', strtotime($request->query('tanggal_akhir'))) : 'Sekarang'),
        ];

        return view('rekap.cetak', compact('laporanList', 'filterInfo'));
    }
}
