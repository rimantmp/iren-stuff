@extends('layouts.admin')

@section('title', 'Catat Bantuan Air')
@section('page_title', 'Catat Penyaluran Bantuan Air')
@section('page_subtitle', 'Formulir pencatatan distribusi bantuan air bersih dan pemetaan titik sasaran')

@section('header_actions')
<a href="{{ route('bantuan.air.index') }}"
   class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition">
    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    <span>Kembali ke Daftar</span>
</a>
@endsection

@section('content')
<form id="formBantuanAir" action="{{ route('bantuan.air.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @if($errors->any())
        <div class="mb-5 p-4 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-xs">
            <div class="flex items-center space-x-2 font-semibold text-rose-900 mb-1.5">
                <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Mohon periksa data yang belum sesuai berikut:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Form Sections (8 Cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Section 1: Wilayah & Lokasi Distribusi -->
            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-6 h-6 rounded bg-blue-600 text-white font-semibold text-xs flex items-center justify-center">1</span>
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Wilayah Distribusi & Lokasi Sasaran</h2>
                            <p class="text-[11px] text-slate-500">Pilih hierarki wilayah administratif dari data resmi Kemendagri</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-medium text-slate-500 bg-slate-200 px-2 py-0.5 rounded">Wajib Diisi</span>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="provinsi_id">
                                Provinsi <span class="text-rose-600">*</span>
                            </label>
                            <select id="provinsi_id" name="provinsi_id" class="w-full" required>
                                <option value="{{ $defaultProvinsi?->id ?? '73' }}" selected>
                                    {{ $defaultProvinsi?->nama ?? 'Sulawesi Selatan' }}
                                </option>
                            </select>
                            <span class="text-[10px] text-slate-500 mt-1 block">Default: Sulawesi Selatan (dapat diubah jika diperlukan)</span>
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="kota_id">
                                Kabupaten / Kota <span class="text-rose-600">*</span>
                            </label>
                            <select id="kota_id" name="kota_id" class="w-full" required>
                                <option value="{{ $defaultKota?->id ?? '7326' }}" selected>
                                    {{ $defaultKota?->nama ?? 'Kabupaten Toraja Utara' }}
                                </option>
                            </select>
                            <span class="text-[10px] text-slate-500 mt-1 block">Default: Toraja Utara</span>
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="kecamatan_id">
                                Kecamatan <span class="text-rose-600">*</span>
                            </label>
                            <select id="kecamatan_id" name="kecamatan_id" class="w-full" required>
                                <option value="">Pilih Kecamatan di Toraja Utara...</option>
                            </select>
                            <span class="text-[10px] text-slate-500 mt-1 block">21 Kecamatan di Kab. Toraja Utara</span>
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="kelurahan_id">
                                Kelurahan / Desa <span class="text-rose-600">*</span>
                            </label>
                            <select id="kelurahan_id" name="kelurahan_id" class="w-full" disabled required>
                                <option value="">Pilih Kelurahan/Desa...</option>
                            </select>
                            <span class="text-[10px] text-slate-500 mt-1 block">Aktif setelah memilih kecamatan</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-800 mb-1" for="alamat_detail">
                            Alamat Detail / Patokan Titik Toren Warga
                        </label>
                        <textarea id="alamat_detail" name="alamat_detail" rows="2"
                                  placeholder="Contoh: RT 03/RW 02, Lapangan voli samping Posyandu Melati, titik toren penampungan warga"
                                  class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition">{{ old('alamat_detail') }}</textarea>
                    </div>

                    <!-- Peta Interaktif & Koordinat -->
                    <div class="pt-2 border-t border-slate-100">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                            <div>
                                <span class="font-medium text-slate-800 block">Titik Koordinat Distribusi (GIS)</span>
                                <span class="text-[11px] text-slate-500">Koordinat terisi otomatis saat kelurahan dipilih. Geser pin atau klik peta untuk titik presisi.</span>
                            </div>
                            <button type="button" id="btnGps"
                                    class="inline-flex items-center px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-medium transition self-start sm:self-auto">
                                <svg class="w-3.5 h-3.5 mr-1 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Gunakan GPS Perangkat</span>
                            </button>
                        </div>

                        <!-- Leaflet Container -->
                        <div id="mapCreate" class="w-full h-56 rounded border border-slate-300 bg-slate-100 relative z-10"></div>

                        <div class="mt-2 flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-600 bg-slate-50 p-2 rounded border border-slate-200">
                            <div class="flex items-center space-x-3">
                                <div>
                                    <span class="text-slate-500">Latitude:</span>
                                    <span id="latDisplay" class="font-mono font-medium text-slate-800">0.000000</span>
                                </div>
                                <div>
                                    <span class="text-slate-500">Longitude:</span>
                                    <span id="lngDisplay" class="font-mono font-medium text-slate-800">0.000000</span>
                                </div>
                            </div>
                            <span id="coordStatus" class="text-slate-500 italic">Menunggu pemilihan kelurahan</span>
                        </div>

                        <!-- Hidden Inputs for Lat / Lng -->
                        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', '0') }}">
                        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', '0') }}">
                    </div>
                </div>
            </div>

            <!-- Section 2: Volume Bantuan & Jadwal -->
            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-6 h-6 rounded bg-blue-600 text-white font-semibold text-xs flex items-center justify-center">2</span>
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Alokasi Volume Air & Jadwal Pengiriman</h2>
                            <p class="text-[11px] text-slate-500">Tentukan volume air yang disalurkan dan estimasi jadwal kedatangan armada</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    <!-- Volume Presets & Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block font-medium text-slate-800" for="jumlah_bantuan">
                                Volume Air Bersih <span class="text-rose-600">*</span>
                            </label>
                            <span class="text-[11px] text-slate-500">Pilihan Cepat Kapasitas Tangki:</span>
                        </div>

                        <!-- Presets -->
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 mb-2">
                            <button type="button" onclick="setVolume(1000)" class="btn-preset px-2 py-1.5 border border-slate-300 hover:border-blue-600 hover:bg-blue-50 text-slate-700 rounded text-center transition font-medium">
                                1.000 L <span class="block text-[9px] text-slate-500 font-normal">Toren Umum</span>
                            </button>
                            <button type="button" onclick="setVolume(2500)" class="btn-preset px-2 py-1.5 border border-slate-300 hover:border-blue-600 hover:bg-blue-50 text-slate-700 rounded text-center transition font-medium">
                                2.500 L <span class="block text-[9px] text-slate-500 font-normal">Tangki Kecil</span>
                            </button>
                            <button type="button" onclick="setVolume(5000)" class="btn-preset px-2 py-1.5 border border-blue-600 bg-blue-50 text-blue-900 rounded text-center transition font-medium ring-1 ring-blue-600">
                                5.000 L <span class="block text-[9px] text-blue-700 font-normal">1 Tangki Standar</span>
                            </button>
                            <button type="button" onclick="setVolume(8000)" class="btn-preset px-2 py-1.5 border border-slate-300 hover:border-blue-600 hover:bg-blue-50 text-slate-700 rounded text-center transition font-medium">
                                8.000 L <span class="block text-[9px] text-slate-500 font-normal">Tangki Sedang</span>
                            </button>
                            <button type="button" onclick="setVolume(10000)" class="btn-preset px-2 py-1.5 border border-slate-300 hover:border-blue-600 hover:bg-blue-50 text-slate-700 rounded text-center transition font-medium">
                                10.000 L <span class="block text-[9px] text-slate-500 font-normal">2 Tangki Standar</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <div class="relative">
                                    <input type="number" id="jumlah_bantuan" name="jumlah_bantuan" value="{{ old('jumlah_bantuan', 5000) }}" step="any" min="1" required
                                           class="w-full px-3 py-2 pr-16 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition font-semibold text-sm text-slate-900">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 font-medium">Liter</span>
                                </div>
                            </div>
                            <div>
                                <input type="text" id="satuan" name="satuan" value="{{ old('satuan', 'Liter') }}" required readonly
                                       class="w-full px-3 py-2 rounded border border-slate-200 bg-slate-50 text-slate-600 outline-none cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Status Segmented Card Picker -->
                    <div>
                        <label class="block font-medium text-slate-800 mb-1.5">
                            Status Operasional Penyaluran <span class="text-rose-600">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="status-option cursor-pointer p-3 rounded-lg border border-slate-200 hover:border-slate-300 transition flex items-start space-x-2.5">
                                <input type="radio" name="status" value="RENCANA" {{ old('status', 'RENCANA') === 'RENCANA' ? 'checked' : '' }} class="mt-0.5 text-blue-600 focus:ring-blue-600">
                                <div>
                                    <span class="font-semibold text-slate-900 block">Rencana</span>
                                    <span class="text-[11px] text-slate-500 block leading-tight">Terjadwal dalam antrean logistik armada</span>
                                </div>
                            </label>

                            <label class="status-option cursor-pointer p-3 rounded-lg border border-slate-200 hover:border-slate-300 transition flex items-start space-x-2.5">
                                <input type="radio" name="status" value="PROSES" {{ old('status') === 'PROSES' ? 'checked' : '' }} class="mt-0.5 text-blue-600 focus:ring-blue-600">
                                <div>
                                    <span class="font-semibold text-slate-900 block">Proses</span>
                                    <span class="text-[11px] text-slate-500 block leading-tight">Armada bergerak menuju lokasi sasaran</span>
                                </div>
                            </label>

                            <label class="status-option cursor-pointer p-3 rounded-lg border border-slate-200 hover:border-slate-300 transition flex items-start space-x-2.5">
                                <input type="radio" name="status" value="TERSALURKAN" {{ old('status') === 'TERSALURKAN' ? 'checked' : '' }} class="mt-0.5 text-blue-600 focus:ring-blue-600">
                                <div>
                                    <span class="font-semibold text-slate-900 block">Tersalurkan</span>
                                    <span class="text-[11px] text-slate-500 block leading-tight">Air berhasil diterima di penampungan warga</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Tanggal Rencana & Realisasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-medium text-slate-800" for="tanggal_rencana">
                                    Tanggal Rencana <span class="text-rose-600">*</span>
                                </label>
                                <div class="flex items-center space-x-1">
                                    <button type="button" onclick="setTanggalRencana('today')" class="text-[10px] text-blue-700 hover:underline">Hari Ini</button>
                                    <span class="text-slate-300">|</span>
                                    <button type="button" onclick="setTanggalRencana('tomorrow')" class="text-[10px] text-blue-700 hover:underline">Besok</button>
                                </div>
                            </div>
                            <input type="date" id="tanggal_rencana" name="tanggal_rencana" value="{{ old('tanggal_rencana', date('Y-m-d')) }}" required
                                   class="w-full px-3 py-2 rounded border border-slate-300 outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="tanggal_penyaluran">
                                Tanggal Realisasi
                            </label>
                            <input type="date" id="tanggal_penyaluran" name="tanggal_penyaluran" value="{{ old('tanggal_penyaluran') }}"
                                   class="w-full px-3 py-2 rounded border border-slate-300 outline-none focus:border-blue-600">
                            <span class="text-[10px] text-slate-500 mt-1 block">Wajib jika status Tersalurkan</span>
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="metode_distribusi">
                                Metode Distribusi
                            </label>
                            <input type="text" id="metode_distribusi" name="metode_distribusi" value="{{ old('metode_distribusi', 'Truk Tangki') }}" list="metodeOptions"
                                   class="w-full px-3 py-2 rounded border border-slate-300 outline-none focus:border-blue-600">
                            <datalist id="metodeOptions">
                                <option value="Truk Tangki">
                                <option value="Toren Publik">
                                <option value="Pipa Darurat">
                                <option value="Jeriken Warga">
                            </datalist>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Penerima Manfaat & Dampak Lingkungan -->
            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-6 h-6 rounded bg-blue-600 text-white font-semibold text-xs flex items-center justify-center">3</span>
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Penerima Manfaat & Penanggung Jawab</h2>
                            <p class="text-[11px] text-slate-500">Data penanggung jawab lapangan dan estimasi jumlah warga yang menerima manfaat</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="nama_penerima">
                                Nama Penanggung Jawab / Kontak <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" id="nama_penerima" name="nama_penerima" value="{{ old('nama_penerima') }}" required
                                   placeholder="Contoh: Bpk. Bambang (Ketua RW 04)"
                                   class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="kontak_penerima">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input type="text" id="kontak_penerima" name="kontak_penerima" value="{{ old('kontak_penerima') }}"
                                   placeholder="0812xxxxxxxx"
                                   class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="jumlah_kk">
                                Estimasi Jumlah KK Terbantu <span class="text-rose-600">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="number" id="jumlah_kk" name="jumlah_kk" value="{{ old('jumlah_kk', 25) }}" min="1" required
                                       class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition">
                                <span class="text-slate-500 font-medium">KK</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-medium text-slate-800" for="jumlah_jiwa">
                                    Estimasi Total Jiwa Terbantu <span class="text-rose-600">*</span>
                                </label>
                                <span id="ratioDisplay" class="text-[11px] font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                    ~50 L / Jiwa
                                </span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="number" id="jumlah_jiwa" name="jumlah_jiwa" value="{{ old('jumlah_jiwa', 100) }}" min="1" required
                                       class="w-full px-3 py-2 rounded border border-slate-300 focus:border-blue-600 focus:ring-1 focus:ring-blue-600 outline-none transition">
                                <span class="text-slate-500 font-medium">Jiwa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Armada, Petugas & Dokumentasi -->
            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <span class="w-6 h-6 rounded bg-blue-600 text-white font-semibold text-xs flex items-center justify-center">4</span>
                        <div>
                            <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Armada Logistik, Petugas & Dokumentasi</h2>
                            <p class="text-[11px] text-slate-500">Rincian kendaraan pengangkut air, petugas yang bertugas, dan bukti serah terima</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="nomor_armada">
                                Nomor Plat Truk Tangki
                            </label>
                            <input type="text" id="nomor_armada" name="nomor_armada" value="{{ old('nomor_armada') }}"
                                   placeholder="Contoh: B 9123 TDA"
                                   class="w-full px-3 py-2 rounded border border-slate-300 outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="nama_petugas">
                                Nama Pengemudi / Petugas
                            </label>
                            <input type="text" id="nama_petugas" name="nama_petugas" value="{{ old('nama_petugas') }}"
                                   placeholder="Contoh: Asep Saepudin"
                                   class="w-full px-3 py-2 rounded border border-slate-300 outline-none focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block font-medium text-slate-800 mb-1" for="sumber_air">
                                Sumber Pengambilan Air
                            </label>
                            <input type="text" id="sumber_air" name="sumber_air" value="{{ old('sumber_air') }}"
                                   placeholder="Contoh: PDAM Tirta Kahuripan"
                                   class="w-full px-3 py-2 rounded border border-slate-300 outline-none focus:border-blue-600">
                        </div>
                    </div>

                    <!-- Upload Foto Dokumentasi Dropzone UI -->
                    <div class="pt-2 border-t border-slate-100">
                        <label class="block font-medium text-slate-800 mb-1">
                            Foto Dokumentasi Distribusi / Serah Terima (Opsional)
                        </label>
                        <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-lg p-4 transition bg-slate-50 text-center relative" id="dropzoneBox">
                            <input type="file" id="foto_dokumentasi" name="foto_dokumentasi" accept="image/jpeg,image/png,image/webp"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">

                            <div id="dropzoneEmpty" class="space-y-1.5 pointer-events-none">
                                <svg class="w-8 h-8 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-xs font-medium text-slate-700">Tarik foto ke sini atau <span class="text-blue-700 underline">pilih file</span></p>
                                <p class="text-[10px] text-slate-500">Mendukung format JPG, PNG, atau WEBP (Maksimal 4 MB)</p>
                            </div>

                            <div id="dropzonePreview" class="hidden flex items-center justify-between p-2 bg-white rounded border border-slate-200">
                                <div class="flex items-center space-x-3">
                                    <img id="previewImg" src="#" alt="Preview" class="w-14 h-14 object-cover rounded border border-slate-200">
                                    <div class="text-left">
                                        <span id="previewName" class="font-medium text-slate-800 block text-xs truncate max-w-xs">nama-file.jpg</span>
                                        <span id="previewSize" class="text-[10px] text-slate-500 block">0 KB</span>
                                    </div>
                                </div>
                                <button type="button" onclick="clearFile()" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Hapus foto">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-slate-800 mb-1" for="catatan">
                            Catatan Operasional Lapangan
                        </label>
                        <textarea id="catatan" name="catatan" rows="2"
                                  placeholder="Catatan tambahan mengenai akses jalan, kondisi toren, atau koordinasi aparat desa"
                                  class="w-full px-3 py-2 rounded border border-slate-300 outline-none focus:border-blue-600">{{ old('catatan') }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column: Sticky Summary & Confirmation (4 Cols) -->
        <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-20">

            <!-- Summary Card -->
            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-sm">
                <div class="px-4 py-3.5 border-b border-slate-200 bg-slate-900 text-white flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <h3 class="text-xs font-bold uppercase tracking-wider">Ringkasan Distribusi</h3>
                    </div>
                    <span id="cardStatusBadge" class="text-[10px] px-2 py-0.5 rounded font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        Rencana
                    </span>
                </div>

                <div class="p-4 space-y-3.5 text-xs">
                    <!-- Volume Callout -->
                    <div class="bg-blue-50 border border-blue-200 rounded p-3 text-center">
                        <span class="text-[11px] text-blue-700 font-medium block">Total Alokasi Volume Air</span>
                        <div class="flex items-baseline justify-center space-x-1 mt-0.5">
                            <span id="summaryVolume" class="text-2xl font-black text-blue-900 font-mono">5.000</span>
                            <span class="text-xs font-semibold text-blue-700">Liter</span>
                        </div>
                        <span id="summaryRatio" class="text-[10px] text-blue-600 block mt-0.5">Kebutuhan ~50 L per jiwa</span>
                    </div>

                    <!-- Target Location -->
                    <div>
                        <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block mb-1">Lokasi Distribusi:</span>
                        <div class="bg-slate-50 rounded p-2.5 border border-slate-200 space-y-1">
                            <div class="flex items-start space-x-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                <div>
                                    <span id="summaryWilayah" class="font-medium text-slate-800 block leading-tight">Belum memilih wilayah</span>
                                    <span id="summaryAlamat" class="text-[11px] text-slate-500 block leading-tight mt-0.5 italic">Alamat spesifik belum diisi</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Target Recipients -->
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="bg-slate-50 rounded p-2 border border-slate-200">
                            <span class="text-[10px] text-slate-500 block">Penerima KK</span>
                            <span id="summaryKk" class="font-bold text-slate-900 font-mono text-sm">25 KK</span>
                        </div>
                        <div class="bg-slate-50 rounded p-2 border border-slate-200">
                            <span class="text-[10px] text-slate-500 block">Total Jiwa</span>
                            <span id="summaryJiwa" class="font-bold text-slate-900 font-mono text-sm">100 Jiwa</span>
                        </div>
                    </div>

                    <!-- Logistics Preview -->
                    <div class="pt-2 border-t border-slate-100 space-y-1.5 text-[11px]">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Penanggung Jawab:</span>
                            <span id="summaryPenerima" class="font-medium text-slate-800 truncate max-w-[140px]">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Truk Tangki:</span>
                            <span id="summaryArmada" class="font-medium text-slate-800">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Petugas / Driver:</span>
                            <span id="summaryPetugas" class="font-medium text-slate-800">-</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Tgl. Rencana:</span>
                            <span id="summaryTanggal" class="font-medium text-slate-800">{{ date('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-3 border-t border-slate-200 space-y-2">
                        <button type="submit" id="btnSubmit"
                                class="w-full py-2.5 px-4 bg-blue-700 hover:bg-blue-800 text-white rounded font-medium text-xs transition flex items-center justify-center space-x-2 shadow-sm focus:ring-2 focus:ring-offset-1 focus:ring-blue-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Penyaluran Air</span>
                        </button>

                        <a href="{{ route('bantuan.air.index') }}"
                           class="w-full py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-medium text-xs transition text-center block">
                            Batal dan Kembali
                        </a>
                    </div>
                </div>
            </div>

            <!-- Standard SOP Guidance Card -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 text-xs space-y-2 shadow-sm">
                <div class="flex items-center space-x-2 text-slate-800 font-semibold">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Standar Distribusi Air Darurat</span>
                </div>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Berdasarkan acuan BNPB dan SPHERE, kebutuhan air bersih minimum di lokasi darurat kekeringan adalah 15 hingga 20 liter per jiwa per hari.
                </p>
                <div class="pt-1 border-t border-slate-100 text-[10px] text-slate-500">
                    Pastikan armada tangki dalam kondisi higienis dan koordinasi dengan pengurus RT setempat telah dilakukan.
                </div>
            </div>

        </div>

    </div>
</form>
@endsection

@push('scripts')
<script>
    let map = null;
    let marker = null;

    // Default Center: Toraja Utara (Rantepao)
    const defaultLat = -2.975660;
    const defaultLng = 119.898410;

    $(document).ready(function() {
        initLeafletMap();
        initCascadingSelect2();
        initLiveSummaryListeners();
        updateRatioCalculation();
        updateSummaryWilayah();
    });

    function initLeafletMap() {
        const initialLat = parseFloat($('#latitude').val()) || defaultLat;
        const initialLng = parseFloat($('#longitude').val()) || defaultLng;
        const initialZoom = (Math.abs(initialLat - defaultLat) < 0.01 && Math.abs(initialLng - defaultLng) < 0.01) ? 11 : 6;

        map = L.map('mapCreate', {
            zoomControl: true,
            attributionControl: false
        }).setView([initialLat, initialLng], initialZoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18
        }).addTo(map);

        $('#coordStatus').text('Pusat Wilayah Kab. Toraja Utara (Pilih kelurahan untuk titik presisi)');

        // Map Click to pin
        map.on('click', function(e) {
            setCoordinates(e.latlng.lat, e.latlng.lng, 'Dipilih manual dari peta');
        });

        // GPS Button
        $('#btnGps').on('click', function() {
            if (navigator.geolocation) {
                $('#coordStatus').text('Mencari posisi GPS...');
                navigator.geolocation.getCurrentPosition(
                    function(pos) {
                        setCoordinates(pos.coords.latitude, pos.coords.longitude, 'Posisi GPS terdeteksi');
                        map.setView([pos.coords.latitude, pos.coords.longitude], 15);
                    },
                    function(err) {
                        alert('Tidak dapat mendeteksi lokasi GPS: ' + err.message);
                        $('#coordStatus').text('Gagal membaca GPS');
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            } else {
                alert('Browser Anda tidak mendukung geolokasi GPS.');
            }
        });
    }

    function setCoordinates(lat, lng, statusText = '') {
        const roundedLat = Number(lat).toFixed(6);
        const roundedLng = Number(lng).toFixed(6);

        $('#latitude').val(roundedLat);
        $('#longitude').val(roundedLng);
        $('#latDisplay').text(roundedLat);
        $('#lngDisplay').text(roundedLng);

        if (statusText) {
            $('#coordStatus').text(statusText);
        }

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                setCoordinates(pos.lat, pos.lng, 'Pin digeser manual');
            });
        }
    }

    function initCascadingSelect2() {
        const urlProvinsi  = "{{ route('wilayah.provinsi') }}";
        const urlKota      = "{{ url('wilayah/kota') }}";
        const urlKecamatan = "{{ url('wilayah/kecamatan') }}";
        const urlKelurahan = "{{ url('wilayah/kelurahan') }}";

        const defaultProvId = "{{ $defaultProvinsi?->id ?? '73' }}";
        const defaultKotaId = "{{ $defaultKota?->id ?? '7326' }}";

        $('#provinsi_id').select2({
            placeholder: 'Pilih Provinsi...',
            allowClear: true,
            ajax: {
                url: urlProvinsi,
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data.results })
            }
        });

        // Initialize Kota with default Sulawesi Selatan
        $('#kota_id').select2({
            placeholder: 'Pilih Kota/Kabupaten...',
            ajax: {
                url: `${urlKota}/${defaultProvId}`,
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data.results })
            }
        });

        // Initialize Kecamatan with default Toraja Utara
        $('#kecamatan_id').select2({
            placeholder: 'Pilih Kecamatan di Toraja Utara...',
            ajax: {
                url: `${urlKecamatan}/${defaultKotaId}`,
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data.results })
            }
        });

        $('#provinsi_id').on('select2:select', function(e) {
            const id = e.params.data.id;
            updateSummaryWilayah();
            resetSelect('#kota_id', 'Pilih Kota/Kabupaten...');
            resetSelect('#kecamatan_id', 'Pilih Kecamatan...');
            resetSelect('#kelurahan_id', 'Pilih Kelurahan/Desa...');

            $('#kota_id').prop('disabled', false).select2({
                placeholder: 'Pilih Kota/Kabupaten...',
                ajax: {
                    url: `${urlKota}/${id}`,
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                }
            });
        });

        $('#kota_id').on('select2:select', function(e) {
            const id = e.params.data.id;
            updateSummaryWilayah();
            resetSelect('#kecamatan_id', 'Pilih Kecamatan...');
            resetSelect('#kelurahan_id', 'Pilih Kelurahan/Desa...');

            $('#kecamatan_id').prop('disabled', false).select2({
                placeholder: 'Pilih Kecamatan...',
                ajax: {
                    url: `${urlKecamatan}/${id}`,
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                }
            });
        });

        $('#kecamatan_id').on('select2:select', function(e) {
            const id = e.params.data.id;
            updateSummaryWilayah();
            resetSelect('#kelurahan_id', 'Pilih Kelurahan/Desa...');

            $('#kelurahan_id').prop('disabled', false).select2({
                placeholder: 'Pilih Kelurahan/Desa...',
                ajax: {
                    url: `${urlKelurahan}/${id}`,
                    dataType: 'json',
                    delay: 250,
                    data: params => ({ q: params.term }),
                    processResults: data => ({ results: data.results })
                }
            });
        });

        $('#kelurahan_id').on('select2:select', function(e) {
            const data = e.params.data;
            updateSummaryWilayah();

            if (data.latitude && data.longitude && (parseFloat(data.latitude) !== 0 || parseFloat(data.longitude) !== 0)) {
                const lat = parseFloat(data.latitude);
                const lng = parseFloat(data.longitude);
                setCoordinates(lat, lng, 'Koordinat resmi Kel. ' + data.text);
                map.setView([lat, lng], 14);
            } else {
                $('#coordStatus').text('Kel. ' + data.text + ' (Silakan tentukan titik di peta)');
            }
        });

        function resetSelect(selector, placeholderText) {
            if ($(selector).data('select2')) {
                $(selector).val(null).trigger('change');
                $(selector).select2('destroy');
            }
            $(selector).empty().append(new Option(placeholderText, '', true, true));
            $(selector).prop('disabled', true);
        }
    }

    function setVolume(val) {
        $('#jumlah_bantuan').val(val).trigger('input');

        $('.btn-preset').removeClass('border-blue-600 bg-blue-50 text-blue-900 ring-1 ring-blue-600')
                         .addClass('border-slate-300 text-slate-700');

        $(event.currentTarget).removeClass('border-slate-300 text-slate-700')
                              .addClass('border-blue-600 bg-blue-50 text-blue-900 ring-1 ring-blue-600');
    }

    function setTanggalRencana(type) {
        const date = new Date();
        if (type === 'tomorrow') {
            date.setDate(date.getDate() + 1);
        }
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        const formatted = `${yyyy}-${mm}-${dd}`;

        const inputEl = document.getElementById('tanggal_rencana');
        if (inputEl && inputEl._flatpickr) {
            inputEl._flatpickr.setDate(formatted, true);
        } else {
            $('#tanggal_rencana').val(formatted).trigger('change');
        }
    }

    function updateRatioCalculation() {
        const vol = parseFloat($('#jumlah_bantuan').val()) || 0;
        const jiwa = parseInt($('#jumlah_jiwa').val()) || 1;
        const ratio = Math.max(1, Math.round(vol / jiwa));

        $('#ratioDisplay').text(`~${ratio} L / Jiwa`);
        $('#summaryRatio').text(`Kebutuhan ~${ratio} L per jiwa`);
        $('#summaryVolume').text(vol.toLocaleString('id-ID'));
    }

    function updateSummaryWilayah() {
        const prov = $('#provinsi_id option:selected').text();
        const kota = $('#kota_id option:selected').text();
        const kec = $('#kecamatan_id option:selected').text();
        const kel = $('#kelurahan_id option:selected').text();

        const parts = [];
        if (kel && !kel.includes('Pilih')) parts.push('Kel. ' + kel);
        if (kec && !kec.includes('Pilih')) parts.push('Kec. ' + kec);
        if (kota && !kota.includes('Pilih')) parts.push(kota);
        if (prov && !prov.includes('Pilih')) parts.push(prov);

        if (parts.length > 0) {
            $('#summaryWilayah').text(parts.join(', '));
        } else {
            $('#summaryWilayah').text('Belum memilih wilayah');
        }
    }

    function initLiveSummaryListeners() {
        $('#jumlah_bantuan, #jumlah_jiwa').on('input change', updateRatioCalculation);

        $('#jumlah_kk').on('input change', function() {
            const val = $(this).val() || '0';
            $('#summaryKk').text(val + ' KK');
        });

        $('#jumlah_jiwa').on('input change', function() {
            const val = $(this).val() || '0';
            $('#summaryJiwa').text(val + ' Jiwa');
        });

        $('#alamat_detail').on('input change', function() {
            const val = $(this).val().trim();
            $('#summaryAlamat').text(val || 'Alamat spesifik belum diisi');
        });

        $('#nama_penerima').on('input change', function() {
            $('#summaryPenerima').text($(this).val().trim() || '-');
        });

        $('#nomor_armada').on('input change', function() {
            $('#summaryArmada').text($(this).val().trim() || '-');
        });

        $('#nama_petugas').on('input change', function() {
            $('#summaryPetugas').text($(this).val().trim() || '-');
        });

        $('#tanggal_rencana').on('change', function() {
            const val = $(this).val();
            if (val) {
                if (val.includes('-')) {
                    const parts = val.split('-');
                    if (parts.length === 3) {
                        $('#summaryTanggal').text(`${parts[2]}/${parts[1]}/${parts[0]}`);
                    }
                } else if (val.includes('/')) {
                    $('#summaryTanggal').text(val);
                }
            }
        });

        $('input[name="status"]').on('change', function() {
            const val = $(this).val();
            const badge = $('#cardStatusBadge');

            if (val === 'TERSALURKAN') {
                badge.attr('class', 'text-[10px] px-2 py-0.5 rounded font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40').text('Tersalurkan');
            } else if (val === 'PROSES') {
                badge.attr('class', 'text-[10px] px-2 py-0.5 rounded font-semibold bg-blue-500/20 text-blue-300 border border-blue-500/40').text('Proses');
            } else {
                badge.attr('class', 'text-[10px] px-2 py-0.5 rounded font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/40').text('Rencana');
            }
        });
    }

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#previewName').text(file.name);
                $('#previewSize').text(Math.round(file.size / 1024) + ' KB');

                $('#dropzoneEmpty').addClass('hidden');
                $('#dropzonePreview').removeClass('hidden');
            };

            reader.readAsDataURL(file);
        }
    }

    function clearFile() {
        $('#foto_dokumentasi').val('');
        $('#dropzonePreview').addClass('hidden');
        $('#dropzoneEmpty').removeClass('hidden');
    }
</script>
@endpush
