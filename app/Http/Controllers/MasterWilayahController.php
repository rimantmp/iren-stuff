<?php

namespace App\Http\Controllers;

use App\Models\Dusun;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\Provinsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterWilayahController extends Controller
{
    /**
     * Master Data Provinsi
     */
    public function provinsi(Request $request): View
    {
        $query = Provinsi::query();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
        }

        $items = $query->orderBy('id')->paginate(15)->withQueryString();

        return view('master.provinsi', compact('items'));
    }

    public function updateProvinsi(Request $request, string $id): RedirectResponse
    {
        $provinsi = Provinsi::findOrFail($id);
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $provinsi->update($validated);

        return back()->with('success', "Data provinsi {$provinsi->nama} berhasil diperbarui!");
    }

    /**
     * Master Data Kota / Kabupaten
     */
    public function kota(Request $request): View
    {
        $query = Kota::query();

        if ($request->filled('provinsi_id')) {
            $query->where('id', 'like', $request->query('provinsi_id').'%');
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
        }

        $items = $query->orderBy('id')->paginate(20)->withQueryString();
        $provinsiList = Provinsi::orderBy('nama')->get();

        return view('master.kota', compact('items', 'provinsiList'));
    }

    public function updateKota(Request $request, string $id): RedirectResponse
    {
        $kota = Kota::findOrFail($id);
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $kota->update($validated);

        return back()->with('success', "Data kota {$kota->nama} berhasil diperbarui!");
    }

    /**
     * Master Data Kecamatan
     */
    public function kecamatan(Request $request): View
    {
        $query = Kecamatan::query();

        if ($request->filled('kota_id')) {
            $query->where('id', 'like', $request->query('kota_id').'%');
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
        }

        $items = $query->orderBy('id')->paginate(25)->withQueryString();

        return view('master.kecamatan', compact('items'));
    }

    public function updateKecamatan(Request $request, string $id): RedirectResponse
    {
        $kecamatan = Kecamatan::findOrFail($id);
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $kecamatan->update($validated);

        return back()->with('success', "Data kecamatan {$kecamatan->nama} berhasil diperbarui!");
    }

    /**
     * Master Data Kelurahan / Desa
     */
    public function kelurahan(Request $request): View
    {
        $query = Kelurahan::query();

        if ($request->filled('kecamatan_id')) {
            $query->where('id', 'like', $request->query('kecamatan_id').'%');
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('id', 'like', "%{$search}%");
        }

        $items = $query->orderBy('id')->paginate(25)->withQueryString();

        return view('master.kelurahan', compact('items'));
    }

    public function updateKelurahan(Request $request, string $id): RedirectResponse
    {
        $kelurahan = Kelurahan::findOrFail($id);
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $kelurahan->update($validated);

        return back()->with('success', "Data kelurahan {$kelurahan->nama} berhasil diperbarui!");
    }

    /**
     * Master Data Dusun
     */
    public function dusun(Request $request): View
    {
        $query = Dusun::with('kelurahan');

        if ($request->filled('kelurahan_id')) {
            $query->where('kelurahan_id', $request->query('kelurahan_id'));
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search): void {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kepala_dusun', 'like', "%{$search}%")
                    ->orWhere('rw', 'like', "%{$search}%")
                    ->orWhere('rt', 'like', "%{$search}%")
                    ->orWhereHas('kelurahan', function ($kq) use ($search): void {
                        $kq->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $items = $query->latest('id')->paginate(20)->withQueryString();

        return view('master.dusun', compact('items'));
    }

    public function storeDusun(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'string', 'exists:t_kelurahan,id'],
            'nama' => ['required', 'string', 'max:100'],
            'rw' => ['nullable', 'string', 'max:20'],
            'rt' => ['nullable', 'string', 'max:20'],
            'kepala_dusun' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $dusun = Dusun::create($validated);

        return back()->with('success', "Dusun {$dusun->nama} berhasil ditambahkan!");
    }

    public function updateDusun(Request $request, int|string $id): RedirectResponse
    {
        $dusun = Dusun::findOrFail($id);

        $validated = $request->validate([
            'kelurahan_id' => ['required', 'string', 'exists:t_kelurahan,id'],
            'nama' => ['required', 'string', 'max:100'],
            'rw' => ['nullable', 'string', 'max:20'],
            'rt' => ['nullable', 'string', 'max:20'],
            'kepala_dusun' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $dusun->update($validated);

        return back()->with('success', "Data dusun {$dusun->nama} berhasil diperbarui!");
    }

    public function destroyDusun(int|string $id): RedirectResponse
    {
        $dusun = Dusun::findOrFail($id);
        $nama = $dusun->nama;
        $dusun->delete();

        return back()->with('success', "Dusun {$nama} berhasil dihapus!");
    }
}
