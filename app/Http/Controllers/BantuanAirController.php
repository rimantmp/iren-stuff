<?php

namespace App\Http\Controllers;

use App\Models\JenisBantuan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\PenyaluranBantuan;
use App\Models\Provinsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BantuanAirController extends Controller
{
    /**
     * Display a listing of water aid distribution.
     */
    public function index(Request $request): View
    {
        $air = JenisBantuan::where('slug', 'air')->firstOrFail();

        $query = PenyaluranBantuan::with(['kelurahan', 'kecamatan', 'kota', 'provinsi'])
            ->where('jenis_bantuan_id', $air->id);

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search): void {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                    ->orWhere('nama_penerima', 'like', "%{$search}%")
                    ->orWhere('nomor_armada', 'like', "%{$search}%")
                    ->orWhere('alamat_detail', 'like', "%{$search}%");
            });
        }

        $penyaluranList = $query->latest('tanggal_rencana')->paginate(10)->withQueryString();

        // Metrics for summary header
        $totalTersalurkan = PenyaluranBantuan::where('jenis_bantuan_id', $air->id)
            ->where('status', 'TERSALURKAN')
            ->sum('jumlah_bantuan');

        $totalRencana = PenyaluranBantuan::where('jenis_bantuan_id', $air->id)
            ->where('status', 'RENCANA')
            ->count();

        $totalProses = PenyaluranBantuan::where('jenis_bantuan_id', $air->id)
            ->where('status', 'PROSES')
            ->count();

        return view('bantuan.air.index', compact('penyaluranList', 'totalTersalurkan', 'totalRencana', 'totalProses', 'air'));
    }

    /**
     * Show the form for creating a new water aid distribution.
     */
    public function create(): View
    {
        $air = JenisBantuan::where('slug', 'air')->firstOrFail();

        $defaultProvinsi = Provinsi::find('73');
        $defaultKota = Kota::find('7326');

        return view('bantuan.air.create', compact('air', 'defaultProvinsi', 'defaultKota'));
    }

    /**
     * Store a newly created water aid distribution.
     */
    public function store(Request $request): RedirectResponse
    {
        $air = JenisBantuan::where('slug', 'air')->firstOrFail();

        $validated = $request->validate([
            'nama_penerima' => ['required', 'string', 'max:255'],
            'kontak_penerima' => ['nullable', 'string', 'max:50'],
            'jumlah_kk' => ['required', 'integer', 'min:1'],
            'jumlah_jiwa' => ['required', 'integer', 'min:1'],
            'provinsi_id' => ['required', 'string'],
            'kota_id' => ['required', 'string'],
            'kecamatan_id' => ['required', 'string'],
            'kelurahan_id' => ['required', 'string'],
            'alamat_detail' => ['nullable', 'string'],
            'jumlah_bantuan' => ['required', 'numeric', 'min:1'],
            'satuan' => ['required', 'string'],
            'tanggal_rencana' => ['required', 'date'],
            'tanggal_penyaluran' => ['nullable', 'date'],
            'status' => ['required', 'in:RENCANA,PROSES,TERSALURKAN'],
            'metode_distribusi' => ['nullable', 'string', 'max:100'],
            'nomor_armada' => ['nullable', 'string', 'max:100'],
            'nama_petugas' => ['nullable', 'string', 'max:100'],
            'sumber_air' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'foto_dokumentasi' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        // Auto coordinate from kelurahan if not explicitly passed
        $kelurahan = Kelurahan::find($validated['kelurahan_id']);
        $latitude = ($request->filled('latitude') && (float) $request->input('latitude') != 0)
            ? (float) $request->input('latitude')
            : ($kelurahan?->latitude ?? 0);

        $longitude = ($request->filled('longitude') && (float) $request->input('longitude') != 0)
            ? (float) $request->input('longitude')
            : ($kelurahan?->longitude ?? 0);

        // Auto generate kode transaksi (e.g. BA-202610-0004)
        $datePrefix = 'BA-'.date('Ym');
        $lastRecord = PenyaluranBantuan::where('kode_transaksi', 'like', "{$datePrefix}-%")
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastRecord && preg_match('/-(\d+)$/', $lastRecord->kode_transaksi, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }
        $kodeTransaksi = $datePrefix.'-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        // Upload foto jika ada
        $fotoPath = null;
        if ($request->hasFile('foto_dokumentasi')) {
            $fotoPath = $request->file('foto_dokumentasi')->store('dokumentasi', 'public');
        }

        PenyaluranBantuan::create([
            'kode_transaksi' => $kodeTransaksi,
            'jenis_bantuan_id' => $air->id,
            'provinsi_id' => $validated['provinsi_id'],
            'kota_id' => $validated['kota_id'],
            'kecamatan_id' => $validated['kecamatan_id'],
            'kelurahan_id' => $validated['kelurahan_id'],
            'alamat_detail' => $validated['alamat_detail'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'nama_penerima' => $validated['nama_penerima'],
            'kontak_penerima' => $validated['kontak_penerima'],
            'jumlah_kk' => $validated['jumlah_kk'],
            'jumlah_jiwa' => $validated['jumlah_jiwa'],
            'jumlah_bantuan' => $validated['jumlah_bantuan'],
            'satuan' => $validated['satuan'],
            'tanggal_rencana' => $validated['tanggal_rencana'],
            'tanggal_penyaluran' => $validated['tanggal_penyaluran'],
            'status' => $validated['status'],
            'metode_distribusi' => $validated['metode_distribusi'],
            'nomor_armada' => $validated['nomor_armada'],
            'nama_petugas' => $validated['nama_petugas'],
            'sumber_air' => $validated['sumber_air'],
            'foto_dokumentasi' => $fotoPath,
            'catatan' => $validated['catatan'],
        ]);

        return redirect()->route('bantuan.air.index')->with('success', "Data penyaluran air dengan kode {$kodeTransaksi} berhasil ditambahkan!");
    }

    /**
     * Display the specified water aid distribution.
     */
    public function show(int $id): View
    {
        $penyaluran = PenyaluranBantuan::with(['kelurahan', 'kecamatan', 'kota', 'provinsi', 'jenisBantuan'])
            ->findOrFail($id);

        return view('bantuan.air.show', compact('penyaluran'));
    }

    /**
     * Display the official handover receipt (BAST) for printing.
     */
    public function cetak(int $id): View
    {
        $penyaluran = PenyaluranBantuan::with(['kelurahan', 'kecamatan', 'kota', 'provinsi', 'jenisBantuan'])
            ->findOrFail($id);

        return view('bantuan.air.cetak', compact('penyaluran'));
    }

    /**
     * Show the form for editing the specified water aid distribution.
     */
    public function edit(int $id): View
    {
        $penyaluran = PenyaluranBantuan::with(['kelurahan', 'kecamatan', 'kota', 'provinsi'])
            ->findOrFail($id);

        return view('bantuan.air.edit', compact('penyaluran'));
    }

    /**
     * Update the specified water aid distribution.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $penyaluran = PenyaluranBantuan::findOrFail($id);

        $validated = $request->validate([
            'nama_penerima' => ['required', 'string', 'max:255'],
            'kontak_penerima' => ['nullable', 'string', 'max:50'],
            'jumlah_kk' => ['required', 'integer', 'min:1'],
            'jumlah_jiwa' => ['required', 'integer', 'min:1'],
            'alamat_detail' => ['nullable', 'string'],
            'jumlah_bantuan' => ['required', 'numeric', 'min:1'],
            'satuan' => ['required', 'string'],
            'tanggal_rencana' => ['required', 'date'],
            'tanggal_penyaluran' => ['nullable', 'date'],
            'status' => ['required', 'in:RENCANA,PROSES,TERSALURKAN'],
            'metode_distribusi' => ['nullable', 'string', 'max:100'],
            'nomor_armada' => ['nullable', 'string', 'max:100'],
            'nama_petugas' => ['nullable', 'string', 'max:100'],
            'sumber_air' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string'],
            'foto_dokumentasi' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($request->hasFile('foto_dokumentasi')) {
            if ($penyaluran->foto_dokumentasi) {
                Storage::disk('public')->delete($penyaluran->foto_dokumentasi);
            }
            $validated['foto_dokumentasi'] = $request->file('foto_dokumentasi')->store('dokumentasi', 'public');
        }

        $penyaluran->update($validated);

        return redirect()->route('bantuan.air.show', $penyaluran->id)->with('success', 'Data penyaluran berhasil diperbarui!');
    }

    /**
     * Remove the specified water aid distribution.
     */
    public function destroy(int $id): RedirectResponse
    {
        $penyaluran = PenyaluranBantuan::findOrFail($id);

        if ($penyaluran->foto_dokumentasi) {
            Storage::disk('public')->delete($penyaluran->foto_dokumentasi);
        }

        $penyaluran->delete();

        return redirect()->route('bantuan.air.index')->with('success', 'Data penyaluran berhasil dihapus!');
    }

    /**
     * Quick update status from show/detail page.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $penyaluran = PenyaluranBantuan::findOrFail($id);

        $validated = $request->validate([
            'status' => ['required', 'in:RENCANA,PROSES,TERSALURKAN'],
        ]);

        $updates = ['status' => $validated['status']];
        if ($validated['status'] === 'TERSALURKAN' && ! $penyaluran->tanggal_penyaluran) {
            $updates['tanggal_penyaluran'] = now();
        }

        $penyaluran->update($updates);

        return back()->with('success', "Status penyaluran {$penyaluran->kode_transaksi} berhasil diperbarui menjadi {$validated['status']}.");
    }
}
