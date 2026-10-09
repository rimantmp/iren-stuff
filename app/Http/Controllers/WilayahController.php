<?php

namespace App\Http\Controllers;

use App\Models\Dusun;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Kota;
use App\Models\Provinsi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WilayahController extends Controller
{
    /**
     * Display demo page with Select2 cascading dropdowns.
     */
    public function index(): View
    {
        return view('wilayah.demo');
    }

    /**
     * Get Provinsi list for Select2.
     */
    public function getProvinsi(Request $request): JsonResponse
    {
        $search = $request->query('q');

        $query = Provinsi::aktif();

        if ($search) {
            $query->where('nama', 'like', '%'.$search.'%');
        }

        $items = $query->orderBy('nama', 'asc')->get();

        $results = $items->map(function (Provinsi $item): array {
            return [
                'id' => $item->id,
                'text' => $item->nama,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Get Kota / Kabupaten list by Provinsi ID for Select2.
     */
    public function getKota(Request $request, string $provinsiId): JsonResponse
    {
        $search = $request->query('q');

        $query = Kota::aktif()->where('id', 'like', $provinsiId.'%');

        if ($search) {
            $query->where('nama', 'like', '%'.$search.'%');
        }

        $items = $query->orderBy('nama', 'asc')->get();

        $results = $items->map(function (Kota $item): array {
            return [
                'id' => $item->id,
                'text' => $item->nama,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Get Kecamatan list by Kota ID for Select2.
     */
    public function getKecamatan(Request $request, string $kotaId): JsonResponse
    {
        $search = $request->query('q');

        $query = Kecamatan::where('id', 'like', $kotaId.'%');

        if ($search) {
            $query->where('nama', 'like', '%'.$search.'%');
        }

        $items = $query->orderBy('nama', 'asc')->get();

        $results = $items->map(function (Kecamatan $item): array {
            return [
                'id' => $item->id,
                'text' => $item->nama,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Get Kelurahan / Desa list by Kecamatan ID for Select2.
     */
    public function getKelurahan(Request $request, string $kecamatanId): JsonResponse
    {
        $search = $request->query('q');

        $query = Kelurahan::where('id', 'like', $kecamatanId.'%');

        if ($search) {
            $query->where('nama', 'like', '%'.$search.'%');
        }

        $items = $query->orderBy('nama', 'asc')->get();

        $results = $items->map(function (Kelurahan $item): array {
            return [
                'id' => $item->id,
                'text' => $item->nama,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Search Kelurahan directly by name/code for global lookup.
     */
    public function searchKelurahan(Request $request): JsonResponse
    {
        $search = $request->query('q');

        if (! $search) {
            return response()->json(['results' => []]);
        }

        $filterKotaId = $request->query('kota_id');
        $activeKotaIds = Kota::aktif()->pluck('id');

        $query = Kelurahan::query();

        if ($filterKotaId) {
            $query->where('id', 'like', $filterKotaId.'%');
        } elseif ($activeKotaIds->isNotEmpty()) {
            $query->where(function ($q) use ($activeKotaIds): void {
                foreach ($activeKotaIds as $kotaId) {
                    $q->orWhere('id', 'like', $kotaId.'%');
                }
            });
        }

        $items = $query->where(function ($q) use ($search): void {
            $q->where('nama', 'like', '%'.$search.'%')
                ->orWhere('id', 'like', '%'.$search.'%');
        })
            ->limit(30)
            ->get();

        $results = $items->map(function (Kelurahan $item): array {
            return [
                'id' => $item->id,
                'text' => "{$item->nama} (Kode: {$item->id})",
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Get Dusun list by Kelurahan ID for Select2.
     */
    public function getDusun(Request $request, string $kelurahanId): JsonResponse
    {
        $search = $request->query('q');

        $query = Dusun::where('kelurahan_id', $kelurahanId);

        if ($search) {
            $query->where('nama', 'like', '%'.$search.'%');
        }

        $items = $query->orderBy('nama', 'asc')->get();

        $results = $items->map(function (Dusun $item): array {
            return [
                'id' => $item->id,
                'text' => $item->nama,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
            ];
        });

        return response()->json(['results' => $results]);
    }
}
