@extends('layouts.admin')

@section('title', 'Edit Penyaluran ' . $penyaluran->kode_transaksi)
@section('page_title', 'Perbarui Data Penyaluran Air')
@section('page_subtitle', $penyaluran->kode_transaksi . ' - ' . $penyaluran->nama_penerima)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg border border-slate-200 p-6">

        @if($errors->any())
            <div class="mb-5 p-3.5 rounded border border-rose-200 bg-rose-50 text-rose-800 text-xs">
                <span class="font-semibold block mb-1">Harap periksa kembali input berikut:</span>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('bantuan.air.update', $penyaluran->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
            @csrf
            @method('PUT')

            <!-- Informasi Wilayah (Readonly) -->
            <div class="p-3 bg-slate-50 rounded border border-slate-200 text-xs space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium">No. Transaksi:</span>
                    <span class="font-mono font-bold text-slate-900">{{ $penyaluran->kode_transaksi }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Lokasi Terdaftar:</span>
                    <span class="text-slate-800 font-medium">
                        Kel. {{ $penyaluran->kelurahan?->nama }}, Kec. {{ $penyaluran->kecamatan?->nama }}, {{ $penyaluran->kota?->nama }}
                    </span>
                </div>
            </div>

            <!-- Section 1: Status & Tanggal -->
            <div class="space-y-3">
                <div class="pb-2 border-b border-slate-200">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">1. Status dan Jadwal Penyaluran</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="status">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 rounded border border-slate-300 bg-white font-medium outline-none focus:border-blue-600">
                            <option value="RENCANA" {{ old('status', $penyaluran->status) === 'RENCANA' ? 'selected' : '' }}>Rencana (Dijadwalkan)</option>
                            <option value="PROSES" {{ old('status', $penyaluran->status) === 'PROSES' ? 'selected' : '' }}>Proses (Dalam Pengiriman)</option>
                            <option value="TERSALURKAN" {{ old('status', $penyaluran->status) === 'TERSALURKAN' ? 'selected' : '' }}>Tersalurkan (Selesai Diterima)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="tanggal_rencana">
                            Tanggal Rencana <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" id="tanggal_rencana" name="tanggal_rencana"
                               value="{{ old('tanggal_rencana', $penyaluran->tanggal_rencana?->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 rounded border border-slate-300 outline-none focus:border-blue-600">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="tanggal_penyaluran">
                            Tanggal Realisasi (Jika Selesai)
                        </label>
                        <input type="date" id="tanggal_penyaluran" name="tanggal_penyaluran"
                               value="{{ old('tanggal_penyaluran', $penyaluran->tanggal_penyaluran?->format('Y-m-d')) }}"
                               class="w-full px-3 py-2 rounded border border-slate-300 outline-none focus:border-blue-600">
                    </div>
                </div>
            </div>

            <!-- Section 2: Data Penerima -->
            <div class="space-y-3 pt-2">
                <div class="pb-2 border-b border-slate-200">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">2. Penerima Manfaat</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="nama_penerima">
                            Nama PIC / Penerima <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="nama_penerima" name="nama_penerima"
                               value="{{ old('nama_penerima', $penyaluran->nama_penerima) }}" required
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="kontak_penerima">
                            Kontak / No Telepon
                        </label>
                        <input type="text" id="kontak_penerima" name="kontak_penerima"
                               value="{{ old('kontak_penerima', $penyaluran->kontak_penerima) }}"
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="jumlah_kk">
                            Jumlah KK Terbantu <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="jumlah_kk" name="jumlah_kk"
                               value="{{ old('jumlah_kk', $penyaluran->jumlah_kk) }}" min="1" required
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="jumlah_jiwa">
                            Jumlah Jiwa Terbantu <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="jumlah_jiwa" name="jumlah_jiwa"
                               value="{{ old('jumlah_jiwa', $penyaluran->jumlah_jiwa) }}" min="1" required
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-slate-700 mb-1" for="alamat_detail">
                        Alamat Spesifik
                    </label>
                    <textarea id="alamat_detail" name="alamat_detail" rows="2"
                              class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">{{ old('alamat_detail', $penyaluran->alamat_detail) }}</textarea>
                </div>
            </div>

            <!-- Section 3: Volume & Logistik -->
            <div class="space-y-3 pt-2">
                <div class="pb-2 border-b border-slate-200">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">3. Alokasi Volume & Armada</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="jumlah_bantuan">
                            Volume Air <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="jumlah_bantuan" name="jumlah_bantuan"
                               value="{{ old('jumlah_bantuan', $penyaluran->jumlah_bantuan) }}" step="any" min="1" required
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="satuan">
                            Satuan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="satuan" name="satuan"
                               value="{{ old('satuan', $penyaluran->satuan) }}" required
                               class="w-full px-3 py-2 rounded border border-slate-300 bg-slate-50 outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="nomor_armada">
                            Plat Truk Tangki
                        </label>
                        <input type="text" id="nomor_armada" name="nomor_armada"
                               value="{{ old('nomor_armada', $penyaluran->nomor_armada) }}"
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="nama_petugas">
                            Petugas / Pengemudi
                        </label>
                        <input type="text" id="nama_petugas" name="nama_petugas"
                               value="{{ old('nama_petugas', $penyaluran->nama_petugas) }}"
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="sumber_air">
                            Sumber Pengambilan Air
                        </label>
                        <input type="text" id="sumber_air" name="sumber_air"
                               value="{{ old('sumber_air', $penyaluran->sumber_air) }}"
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="metode_distribusi">
                            Metode Distribusi
                        </label>
                        <input type="text" id="metode_distribusi" name="metode_distribusi"
                               value="{{ old('metode_distribusi', $penyaluran->metode_distribusi) }}"
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="foto_dokumentasi">
                            Perbarui Foto Serah Terima (Opsional)
                        </label>
                        <input type="file" id="foto_dokumentasi" name="foto_dokumentasi" accept="image/*"
                               class="w-full px-2 py-1.5 border border-slate-300 rounded text-xs">
                        @if($penyaluran->foto_dokumentasi)
                            <span class="text-[11px] text-slate-500 mt-1 block">Foto telah ada tersimpan. Unggah hanya jika ingin menggantinya.</span>
                        @endif
                    </div>

                    <div>
                        <label class="block font-medium text-slate-700 mb-1" for="catatan">
                            Catatan Operasional
                        </label>
                        <input type="text" id="catatan" name="catatan"
                               value="{{ old('catatan', $penyaluran->catatan) }}"
                               class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 outline-none">
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('bantuan.air.show', $penyaluran->id) }}" class="text-slate-600 hover:underline">
                    Batal
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded font-medium transition focus:ring-2 focus:ring-offset-1 focus:ring-blue-600">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
