<?php

namespace App\Http\Controllers;

use App\Models\Dusun;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\Provinsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MasterWilayahController extends Controller
{
    /**
     * Master Data Provinsi
     */
    public function provinsi(Request $request): View
    {
        $query = Provinsi::query();

        if ($request->filled('status')) {
            if ($request->query('status') === 'aktif') {
                $query->where('status_aktif', true);
            } elseif ($request->query('status') === 'nonaktif') {
                $query->where('status_aktif', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search): void {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('id')->paginate(15)->withQueryString();

        $totalAktif = Provinsi::where('status_aktif', true)->count();
        $totalNonaktif = Provinsi::where('status_aktif', false)->count();

        return view('master.provinsi', compact('items', 'totalAktif', 'totalNonaktif'));
    }

    public function updateProvinsi(Request $request, string $id): RedirectResponse
    {
        $provinsi = Provinsi::findOrFail($id);
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $provinsi->update($validated);

        return back()->with('success', "Data provinsi {$provinsi->nama} berhasil diperbarui!");
    }

    public function toggleProvinsi(string $id): RedirectResponse
    {
        $provinsi = Provinsi::findOrFail($id);
        $provinsi->status_aktif = ! $provinsi->status_aktif;
        $provinsi->save();

        $statusText = $provinsi->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Provinsi {$provinsi->nama} berhasil {$statusText}!");
    }

    public function batchUpdateStatusProvinsi(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate_all,deactivate_all,preset_sulsel'],
        ]);

        if ($validated['action'] === 'preset_sulsel') {
            Provinsi::where('id', '!=', '73')->update(['status_aktif' => false]);
            Provinsi::where('id', '73')->update(['status_aktif' => true]);

            return back()->with('success', 'Preset Wilayah diterapkan: Hanya Provinsi Sulawesi Selatan yang aktif.');
        }

        if ($validated['action'] === 'activate_all') {
            Provinsi::query()->update(['status_aktif' => true]);

            return back()->with('success', 'Semua 38 provinsi berhasil diaktifkan.');
        }

        if ($validated['action'] === 'deactivate_all') {
            Provinsi::query()->update(['status_aktif' => false]);

            return back()->with('success', 'Semua provinsi dinonaktifkan.');
        }

        return back();
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

        if ($request->filled('status')) {
            if ($request->query('status') === 'aktif') {
                $query->where('status_aktif', true);
            } elseif ($request->query('status') === 'nonaktif') {
                $query->where('status_aktif', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search): void {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('id')->paginate(20)->withQueryString();
        $provinsiList = Provinsi::orderBy('nama')->get();

        $totalAktif = Kota::where('status_aktif', true)->count();
        $totalNonaktif = Kota::where('status_aktif', false)->count();

        return view('master.kota', compact('items', 'provinsiList', 'totalAktif', 'totalNonaktif'));
    }

    public function updateKota(Request $request, string $id): RedirectResponse
    {
        $kota = Kota::findOrFail($id);
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $kota->update($validated);

        return back()->with('success', "Data kota {$kota->nama} berhasil diperbarui!");
    }

    public function toggleKota(string $id): RedirectResponse
    {
        $kota = Kota::findOrFail($id);
        $kota->status_aktif = ! $kota->status_aktif;
        $kota->save();

        $statusText = $kota->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Kota/Kabupaten {$kota->nama} berhasil {$statusText}!");
    }

    public function batchUpdateStatusKota(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'provinsi_id' => ['nullable', 'string'],
            'action' => ['required', 'in:activate_all,deactivate_all,preset_toraja'],
        ]);

        if ($validated['action'] === 'preset_toraja') {
            Provinsi::where('id', '!=', '73')->update(['status_aktif' => false]);
            Provinsi::where('id', '73')->update(['status_aktif' => true]);

            Kota::query()->update(['status_aktif' => false]);
            Kota::whereIn('id', ['7318', '7326'])->update(['status_aktif' => true]);

            return back()->with('success', 'Preset Wilayah diterapkan: Provinsi Sulawesi Selatan aktif, Kabupaten aktif hanya Tana Toraja (7318) dan Toraja Utara (7326).');
        }

        if ($validated['action'] === 'activate_all') {
            $query = Kota::query();
            if ($request->filled('provinsi_id')) {
                $query->where('id', 'like', $request->query('provinsi_id').'%');
            }
            $query->update(['status_aktif' => true]);

            return back()->with('success', 'Semua kota/kabupaten pada filter yang dipilih berhasil diaktifkan.');
        }

        if ($validated['action'] === 'deactivate_all') {
            $query = Kota::query();
            if ($request->filled('provinsi_id')) {
                $query->where('id', 'like', $request->query('provinsi_id').'%');
            }
            $query->update(['status_aktif' => false]);

            return back()->with('success', 'Semua kota/kabupaten pada filter yang dipilih berhasil dinonaktifkan.');
        }

        return back();
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

        if ($request->filled('kecamatan_id')) {
            $query->where('kelurahan_id', 'like', $request->query('kecamatan_id').'%');
        } elseif ($request->filled('kota_id')) {
            $query->where('kelurahan_id', 'like', $request->query('kota_id').'%');
        }

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
        $activeKotaList = Kota::aktif()->orderBy('nama')->get();

        $selectedKotaId = $request->query('kota_id');
        $kecamatanList = $selectedKotaId ? Kecamatan::where('id', 'like', $selectedKotaId.'%')->orderBy('nama')->get() : collect();

        return view('master.dusun', compact('items', 'activeKotaList', 'kecamatanList'));
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

    public function storeDusunBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kelurahan_id' => ['required', 'string', 'exists:t_kelurahan,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.nama' => ['nullable', 'string', 'max:100'],
            'items.*.rw' => ['nullable', 'string', 'max:20'],
            'items.*.rt' => ['nullable', 'string', 'max:20'],
            'items.*.kepala_dusun' => ['nullable', 'string', 'max:100'],
            'items.*.latitude' => ['nullable', 'numeric'],
            'items.*.longitude' => ['nullable', 'numeric'],
            'items.*.keterangan' => ['nullable', 'string', 'max:500'],
        ]);

        $kelurahan = Kelurahan::findOrFail($validated['kelurahan_id']);

        $records = [];
        $now = now();

        foreach ($validated['items'] as $item) {
            $nama = trim($item['nama'] ?? '');
            if ($nama === '') {
                continue;
            }

            $records[] = [
                'kelurahan_id' => $kelurahan->id,
                'nama' => $nama,
                'rw' => ! empty($item['rw']) ? trim($item['rw']) : null,
                'rt' => ! empty($item['rt']) ? trim($item['rt']) : null,
                'kepala_dusun' => ! empty($item['kepala_dusun']) ? trim($item['kepala_dusun']) : null,
                'latitude' => (isset($item['latitude']) && $item['latitude'] !== '') ? (float) $item['latitude'] : $kelurahan->latitude,
                'longitude' => (isset($item['longitude']) && $item['longitude'] !== '') ? (float) $item['longitude'] : $kelurahan->longitude,
                'keterangan' => ! empty($item['keterangan']) ? trim($item['keterangan']) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (empty($records)) {
            return back()->withErrors(['items' => 'Minimal satu nama dusun harus diisi.'])->withInput();
        }

        DB::transaction(function () use ($records): void {
            Dusun::insert($records);
        });

        $count = count($records);

        return redirect()->route('master.dusun', [
            'kota_id' => substr($kelurahan->id, 0, 4),
            'kecamatan_id' => substr($kelurahan->id, 0, 6),
            'kelurahan_id' => $kelurahan->id,
        ])->with('success', "Berhasil menambahkan {$count} dusun sekaligus pada Kelurahan {$kelurahan->nama}!");
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
