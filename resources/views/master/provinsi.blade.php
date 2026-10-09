@extends('layouts.admin')

@section('title', 'Master Data Provinsi')
@section('page_title', 'Master Data Provinsi')
@section('page_subtitle', 'Daftar 38 Provinsi administratif di Indonesia')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Quick Preset -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 rounded-lg border border-slate-200">
        <div class="flex items-center space-x-2 text-xs">
            <span class="font-medium text-slate-700">Ringkasan Wilayah:</span>
            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold rounded-full border border-slate-200">
                Total: {{ $items->total() }} Provinsi
            </span>
            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full border border-emerald-200">
                Aktif: {{ $totalAktif }}
            </span>
            <span class="px-2.5 py-1 bg-slate-50 text-slate-500 font-semibold rounded-full border border-slate-200">
                Nonaktif: {{ $totalNonaktif }}
            </span>
        </div>

        <!-- Batch & Preset Actions -->
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('master.provinsi.batch-status') }}" onsubmit="return confirm('Terapkan preset: Hanya Provinsi Sulawesi Selatan yang aktif dan 37 provinsi lainnya dinonaktifkan?');">
                @csrf
                <input type="hidden" name="action" value="preset_sulsel">
                <button type="submit" class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded text-xs font-semibold flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Preset: Khusus Sulsel
                </button>
            </form>

            <form method="POST" action="{{ route('master.provinsi.batch-status') }}" onsubmit="return confirm('Aktifkan seluruh 38 provinsi?');">
                @csrf
                <input type="hidden" name="action" value="activate_all">
                <button type="submit" class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 rounded text-xs font-medium transition" title="Aktifkan seluruh provinsi">
                    Aktifkan Semua
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <!-- Toolbar & Filter -->
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Filter Status Tab -->
            <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-md text-xs">
                <a href="{{ route('master.provinsi', array_merge(request()->except(['page', 'status']))) }}"
                   class="px-2.5 py-1 rounded font-medium transition {{ !request('status') ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua
                </a>
                <a href="{{ route('master.provinsi', array_merge(request()->except('page'), ['status' => 'aktif'])) }}"
                   class="px-2.5 py-1 rounded font-medium transition {{ request('status') === 'aktif' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Aktif ({{ $totalAktif }})
                </a>
                <a href="{{ route('master.provinsi', array_merge(request()->except('page'), ['status' => 'nonaktif'])) }}"
                   class="px-2.5 py-1 rounded font-medium transition {{ request('status') === 'nonaktif' ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Nonaktif ({{ $totalNonaktif }})
                </a>
            </div>

            <form method="GET" action="{{ route('master.provinsi') }}" class="flex items-center space-x-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari kode atau nama..."
                       class="px-3 py-1.5 border border-slate-300 rounded text-xs outline-none focus:border-blue-600 w-56">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded text-xs font-medium">
                    Cari
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('master.provinsi') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <th class="py-2.5 px-4">Kode Kemendagri</th>
                        <th class="py-2.5 px-4">Nama Provinsi</th>
                        <th class="py-2.5 px-4">Status</th>
                        <th class="py-2.5 px-4">Latitude</th>
                        <th class="py-2.5 px-4">Longitude</th>
                        <th class="py-2.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50 transition {{ !$item->status_aktif ? 'bg-slate-50/50 opacity-75' : '' }}">
                            <td class="py-3 px-4 font-mono font-medium text-blue-700">{{ $item->id }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">
                                {{ $item->nama }}
                            </td>
                            <td class="py-3 px-4">
                                <form method="POST" action="{{ route('master.provinsi.toggle', $item->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="Klik untuk {{ $item->status_aktif ? 'menonaktifkan' : 'mengaktifkan' }}"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium transition cursor-pointer {{ $item->status_aktif ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-300' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $item->status_aktif ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $item->latitude }}</td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $item->longitude }}</td>
                            <td class="py-3 px-4 text-right">
                                <button onclick="openEditModal('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->latitude }}', '{{ $item->longitude }}', {{ $item->status_aktif ? 'true' : 'false' }})"
                                        class="text-blue-700 hover:underline font-medium">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Tidak ada data provinsi yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-3 border-t border-slate-200">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Edit Provinsi -->
<div id="modalEdit" class="fixed inset-0 bg-slate-900/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-sm w-full p-5 shadow-lg border border-slate-200">
        <h3 class="text-xs font-semibold text-slate-900 mb-3 pb-2 border-b border-slate-200 uppercase tracking-wider">Perbarui Data Provinsi</h3>
        <form id="formEditProvinsi" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-medium text-slate-700 mb-1">Kode Provinsi</label>
                <input type="text" id="edit_id" disabled class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded font-mono">
            </div>
            <div>
                <label class="block font-medium text-slate-700 mb-1">Nama Provinsi</label>
                <input type="text" id="edit_nama" name="nama" required class="w-full px-3 py-1.5 border border-slate-300 rounded outline-none focus:border-blue-600">
            </div>
            <div>
                <label class="block font-medium text-slate-700 mb-1">Status Ketersediaan</label>
                <select id="edit_status_aktif" name="status_aktif" class="w-full px-3 py-1.5 border border-slate-300 rounded outline-none focus:border-blue-600 bg-white">
                    <option value="1">Aktif (Tampil di pencarian & form)</option>
                    <option value="0">Nonaktif (Disembunyikan)</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Latitude</label>
                    <input type="number" step="any" id="edit_lat" name="latitude" class="w-full px-3 py-1.5 border border-slate-300 rounded font-mono">
                </div>
                <div>
                    <label class="block font-medium text-slate-700 mb-1">Longitude</label>
                    <input type="number" step="any" id="edit_lng" name="longitude" class="w-full px-3 py-1.5 border border-slate-300 rounded font-mono">
                </div>
            </div>
            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-2">
                <button type="button" onclick="closeEditModal()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 rounded font-medium text-slate-700">Batal</button>
                <button type="submit" class="px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded font-medium">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(id, nama, lat, lng, statusAktif) {
        $('#formEditProvinsi').attr('action', `/master/provinsi/${id}`);
        $('#edit_id').val(id);
        $('#edit_nama').val(nama);
        $('#edit_lat').val(lat);
        $('#edit_lng').val(lng);
        $('#edit_status_aktif').val(statusAktif ? '1' : '0');
        $('#modalEdit').removeClass('hidden');
    }
    function closeEditModal() {
        $('#modalEdit').addClass('hidden');
    }
</script>
@endpush
@endsection
