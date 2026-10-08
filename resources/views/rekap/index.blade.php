@extends('layouts.admin')

@section('title', 'Rekapitulasi Penyaluran')
@section('page_title', 'Rekapitulasi Penyaluran Bantuan')
@section('page_subtitle', 'Laporan agregasi kegiatan distribusi bantuan sosial')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="bg-white rounded-lg border border-slate-200 p-5">
        <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider pb-2.5 mb-3 border-b border-slate-200">
            Parameter Laporan
        </h2>

        <form method="GET" action="{{ route('rekap.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-medium text-slate-700 mb-1">Jenis Program</label>
                <select name="jenis_bantuan_id" class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none bg-white">
                    <option value="">Semua Jenis Program</option>
                    @foreach($semuaJenisBantuan as $jb)
                        <option value="{{ $jb->id }}" {{ request('jenis_bantuan_id') == $jb->id ? 'selected' : '' }}>
                            {{ $jb->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="RENCANA" {{ request('status') === 'RENCANA' ? 'selected' : '' }}>Rencana</option>
                    <option value="PROSES" {{ request('status') === 'PROSES' ? 'selected' : '' }}>Proses</option>
                    <option value="TERSALURKAN" {{ request('status') === 'TERSALURKAN' ? 'selected' : '' }}>Tersalurkan</option>
                </select>
            </div>

            <div>
                <label class="block font-medium text-slate-700 mb-1">Provinsi</label>
                <select name="provinsi_id" class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none bg-white">
                    <option value="">Semua Provinsi</option>
                    @foreach($semuaProvinsi as $prov)
                        <option value="{{ $prov->id }}" {{ request('provinsi_id') == $prov->id ? 'selected' : '' }}>
                            {{ $prov->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                       class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none">
            </div>

            <div>
                <label class="block font-medium text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                       class="w-full px-2.5 py-1.5 border border-slate-300 rounded outline-none">
            </div>

            <div class="sm:col-span-2 lg:col-span-5 flex items-center justify-between pt-2 border-t border-slate-100">
                <a href="{{ route('rekap.index') }}" class="text-xs text-slate-500 hover:underline">
                    Reset Filter
                </a>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('rekap.cetak', request()->all()) }}" target="_blank"
                       class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium border border-slate-300 transition">
                        Cetak Laporan
                    </a>
                    <button type="submit" class="px-4 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Ringkasan Akumulasi -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <span class="text-xs font-medium text-slate-500 block">Total Kegiatan</span>
            <span class="text-xl font-bold text-slate-900 mt-1 block">{{ number_format($totalTransaksi, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <span class="text-xs font-medium text-slate-500 block">Total Jiwa Terbantu</span>
            <span class="text-xl font-bold text-slate-900 mt-1 block">{{ number_format($totalJiwa, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <span class="text-xs font-medium text-slate-500 block">Total KK Terbantu</span>
            <span class="text-xl font-bold text-slate-900 mt-1 block">{{ number_format($totalKk, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-4 rounded-lg border border-slate-200">
            <span class="text-xs font-medium text-slate-500 block">Air Tersalurkan</span>
            <span class="text-xl font-bold text-slate-900 mt-1 block">{{ number_format($totalVolumeAir, 0, ',', '.') }} Liter</span>
        </div>
    </div>

    <!-- Tabel Rekapitulasi -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <th class="py-2.5 px-4">No. Transaksi</th>
                        <th class="py-2.5 px-4">Program</th>
                        <th class="py-2.5 px-4">Penerima & Lokasi</th>
                        <th class="py-2.5 px-4">Jadwal Rencana / Realisasi</th>
                        <th class="py-2.5 px-4 text-right">Volume</th>
                        <th class="py-2.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($laporanList as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-medium text-blue-700">
                                {{ $item->kode_transaksi }}
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-900">
                                {{ $item->jenisBantuan?->nama }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-slate-900 block">{{ $item->nama_penerima }}</span>
                                <span class="text-[11px] text-slate-500 block">
                                    Kel. {{ $item->kelurahan?->nama }}, Kec. {{ $item->kecamatan?->nama }}, {{ $item->kota?->nama }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                <span class="block">Rencana: {{ $item->tanggal_rencana->format('d/m/Y') }}</span>
                                @if($item->tanggal_penyaluran)
                                    <span class="text-slate-900 font-medium block text-[11px]">Selesai: {{ $item->tanggal_penyaluran->format('d/m/Y') }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-slate-900">
                                {{ number_format($item->jumlah_bantuan, 0, ',', '.') }} {{ $item->satuan }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->status === 'TERSALURKAN')
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Tersalurkan
                                    </span>
                                @elseif($item->status === 'PROSES')
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-800 border border-blue-200">
                                        Proses
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                        Rencana
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Tidak ada data yang sesuai dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($laporanList->hasPages())
            <div class="p-3 border-t border-slate-200">
                {{ $laporanList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
