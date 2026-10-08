@extends('layouts.admin')

@section('title', 'Daftar Bantuan Air')
@section('page_title', 'Penyaluran Bantuan Air Bersih')
@section('page_subtitle', 'Operasional distribusi air bersih ke lokasi sasaran')

@section('header_actions')
<a href="{{ route('bantuan.air.create') }}"
   class="inline-flex items-center px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition focus:ring-2 focus:ring-offset-1 focus:ring-blue-600">
    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    <span>Catat Bantuan Air</span>
</a>
@endsection

@section('content')
<div class="space-y-6">

    <!-- KPI Mini Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4 border border-slate-200">
            <span class="text-xs text-slate-500 font-medium block">Total Air Tersalurkan</span>
            <span class="text-xl font-bold text-slate-900 mt-1 block">{{ number_format($totalTersalurkan, 0, ',', '.') }} Liter</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200">
            <span class="text-xs text-slate-500 font-medium block">Armada Sedang Proses</span>
            <span class="text-xl font-bold text-blue-700 mt-1 block">{{ $totalProses }} Lokasi</span>
        </div>

        <div class="bg-white rounded-lg p-4 border border-slate-200">
            <span class="text-xs text-slate-500 font-medium block">Jadwal Rencana</span>
            <span class="text-xl font-bold text-amber-700 mt-1 block">{{ $totalRencana }} Lokasi</span>
        </div>
    </div>

    <!-- Main Card & Data Table -->
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <!-- Filter Toolbar -->
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Filter Status Tab Buttons -->
            <div class="flex items-center space-x-1 border border-slate-200 p-1 rounded-md text-xs font-medium">
                <a href="{{ route('bantuan.air.index') }}"
                   class="px-3 py-1 rounded transition {{ !request('status') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua
                </a>
                <a href="{{ route('bantuan.air.index', ['status' => 'RENCANA']) }}"
                   class="px-3 py-1 rounded transition {{ request('status') === 'RENCANA' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:text-slate-900' }}">
                    Rencana
                </a>
                <a href="{{ route('bantuan.air.index', ['status' => 'PROSES']) }}"
                   class="px-3 py-1 rounded transition {{ request('status') === 'PROSES' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:text-slate-900' }}">
                    Proses
                </a>
                <a href="{{ route('bantuan.air.index', ['status' => 'TERSALURKAN']) }}"
                   class="px-3 py-1 rounded transition {{ request('status') === 'TERSALURKAN' ? 'bg-slate-900 text-white' : 'text-slate-600 hover:text-slate-900' }}">
                    Tersalurkan
                </a>
            </div>

            <!-- Search Form and Action Button -->
            <div class="flex items-center space-x-2">
                <form method="GET" action="{{ route('bantuan.air.index') }}" class="flex items-center space-x-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari kode, penerima, armada..."
                           class="px-3 py-1.5 border border-slate-300 rounded text-xs outline-none focus:border-blue-600 w-56">
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded text-xs font-medium transition">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('bantuan.air.index', ['status' => request('status')]) }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                    @endif
                </form>

                <a href="{{ route('bantuan.air.create') }}"
                   class="inline-flex items-center px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Data</span>
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">No. Transaksi</th>
                        <th class="py-3 px-4">Penerima & Kontak</th>
                        <th class="py-3 px-4">Wilayah Distribusi</th>
                        <th class="py-3 px-4">Armada & Petugas</th>
                        <th class="py-3 px-4 text-right">Volume</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penyaluranList as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4">
                                <span class="font-mono font-medium text-blue-700 block">{{ $item->kode_transaksi }}</span>
                                <span class="text-[11px] text-slate-400">
                                    {{ $item->tanggal_penyaluran ? $item->tanggal_penyaluran->format('d/m/Y') : $item->tanggal_rencana->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-slate-900 block">{{ $item->nama_penerima }}</span>
                                <span class="text-[11px] text-slate-500">{{ $item->kontak_penerima ?: 'Tanpa kontak' }}</span>
                                <span class="text-[10px] text-slate-400 block">({{ $item->jumlah_kk }} KK / {{ $item->jumlah_jiwa }} Jiwa)</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-medium text-slate-800 block">Kel. {{ $item->kelurahan?->nama }}</span>
                                <span class="text-[11px] text-slate-500 block">Kec. {{ $item->kecamatan?->nama }}, {{ $item->kota?->nama }}</span>
                                <span class="text-[10px] text-slate-400 truncate max-w-xs block">{{ $item->alamat_detail }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-slate-800 block">{{ $item->nomor_armada ?: '-' }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $item->nama_petugas ?: '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <span class="font-mono font-semibold text-slate-900 block">
                                    {{ number_format($item->jumlah_bantuan, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $item->satuan }}</span>
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
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('bantuan.air.show', $item->id) }}" class="text-blue-700 hover:underline font-medium">
                                        Detail
                                    </a>
                                    <a href="{{ route('bantuan.air.edit', $item->id) }}" class="text-slate-600 hover:text-slate-900 font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('bantuan.air.destroy', $item->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Hapus data penyaluran {{ $item->kode_transaksi }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-700 hover:underline font-medium">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Tidak ada data bantuan air yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penyaluranList->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $penyaluranList->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
