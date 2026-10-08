<?php

namespace App\Http\Controllers;

use App\Models\JenisBantuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JenisBantuanController extends Controller
{
    /**
     * Display a listing of jenis bantuan.
     */
    public function index(): View
    {
        $jenisBantuanList = JenisBantuan::withCount('penyaluran')->latest()->get();

        return view('bantuan.jenis.index', compact('jenisBantuanList'));
    }

    /**
     * Store a newly created jenis bantuan.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $slug = Str::slug($validated['nama']);
        $count = JenisBantuan::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-'.($count + 1);
        }

        JenisBantuan::create([
            'nama' => $validated['nama'],
            'slug' => $slug,
            'satuan' => $validated['satuan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'icon' => $validated['icon'] ?? 'box',
            'status_aktif' => true,
        ]);

        return redirect()->route('bantuan.jenis.index')->with('success', 'Jenis bantuan baru berhasil ditambahkan!');
    }

    /**
     * Update the specified jenis bantuan.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $jenisBantuan = JenisBantuan::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'satuan' => ['required', 'string', 'max:50'],
            'deskripsi' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'status_aktif' => ['nullable', 'boolean'],
        ]);

        $jenisBantuan->update([
            'nama' => $validated['nama'],
            'satuan' => $validated['satuan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'icon' => $validated['icon'] ?? $jenisBantuan->icon,
            'status_aktif' => $request->has('status_aktif'),
        ]);

        return redirect()->route('bantuan.jenis.index')->with('success', 'Data jenis bantuan berhasil diperbarui!');
    }

    /**
     * Remove the specified jenis bantuan.
     */
    public function destroy(int $id): RedirectResponse
    {
        $jenisBantuan = JenisBantuan::withCount('penyaluran')->findOrFail($id);

        if ($jenisBantuan->penyaluran_count > 0) {
            return back()->with('error', 'Tidak dapat menghapus jenis bantuan yang sudah memiliki riwayat transaksi penyaluran.');
        }

        $jenisBantuan->delete();

        return redirect()->route('bantuan.jenis.index')->with('success', 'Jenis bantuan berhasil dihapus!');
    }
}
