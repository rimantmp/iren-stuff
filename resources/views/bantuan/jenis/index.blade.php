@extends('layouts.admin')

@section('title', 'Master Jenis Bantuan')
@section('page_title', 'Master Jenis Program Bantuan')
@section('page_subtitle', 'Pengelolaan kategori dan jenis program bantuan sosial')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Form Tambah -->
        <div class="bg-white rounded-lg border border-slate-200 p-5">
            <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider pb-2 mb-3 border-b border-slate-200">
                Tambah Jenis Bantuan
            </h2>

            <form action="{{ route('bantuan.jenis.store') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                <div>
                    <label class="block font-medium text-slate-700 mb-1" for="nama">
                        Nama Program Bantuan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama" name="nama" required
                           placeholder="Contoh: Bantuan Beasiswa"
                           class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1" for="satuan">
                        Satuan Ukur <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="satuan" name="satuan" required
                           placeholder="Contoh: Paket, Liter, Jiwa, Rupiah"
                           class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1" for="deskripsi">
                        Keterangan Sasaran
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                              placeholder="Deskripsi ringkas program..."
                              class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none"></textarea>
                </div>

                <button type="submit"
                        class="w-full py-2 bg-blue-700 hover:bg-blue-800 text-white font-medium rounded transition">
                    Simpan Jenis Bantuan
                </button>
            </form>
        </div>

        <!-- Kolom Kanan: Tabel -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 p-5">
            <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider pb-2 mb-3 border-b border-slate-200">
                Daftar Jenis Bantuan Terdaftar
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                            <th class="py-2.5 px-3">Nama Program</th>
                            <th class="py-2.5 px-3">Satuan</th>
                            <th class="py-2.5 px-3">Keterangan</th>
                            <th class="py-2.5 px-3 text-center">Data Kegiatan</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($jenisBantuanList as $jb)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3 font-medium text-slate-900">
                                    {{ $jb->nama }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="text-slate-700 font-mono text-[11px] bg-slate-100 px-1.5 py-0.5 rounded">{{ $jb->satuan }}</span>
                                </td>
                                <td class="py-3 px-3 text-slate-500 max-w-xs truncate">
                                    {{ $jb->deskripsi ?: '-' }}
                                </td>
                                <td class="py-3 px-3 text-center font-medium text-slate-800">
                                    {{ $jb->penyaluran_count }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    @if($jb->penyaluran_count === 0 && !in_array($jb->slug, ['air', 'sembako', 'tunai-gereja', 'pengadaan']))
                                        <form action="{{ route('bantuan.jenis.destroy', $jb->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Hapus jenis bantuan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-700 hover:underline font-medium">
                                                Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-slate-400">Bawaan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
