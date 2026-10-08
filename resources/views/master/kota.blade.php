@extends('layouts.admin')

@section('title', 'Master Data Kota / Kabupaten')
@section('page_title', 'Master Data Kota dan Kabupaten')
@section('page_subtitle', 'Daftar 514 Kota dan Kabupaten administratif di Indonesia')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <!-- Toolbar & Filter -->
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <span class="text-xs font-medium text-slate-500">
                Total data: <b>{{ $items->total() }}</b> Kota / Kabupaten
            </span>

            <form method="GET" action="{{ route('master.kota') }}" class="flex flex-wrap items-center gap-2">
                <select name="provinsi_id" onchange="this.form.submit()" class="px-2.5 py-1.5 border border-slate-300 rounded text-xs outline-none bg-white">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinsiList as $prov)
                        <option value="{{ $prov->id }}" {{ request('provinsi_id') == $prov->id ? 'selected' : '' }}>
                            {{ $prov->nama }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama kota..."
                       class="px-3 py-1.5 border border-slate-300 rounded text-xs outline-none focus:border-blue-600 w-48">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded text-xs font-medium">
                    Cari
                </button>
                @if(request('search') || request('provinsi_id'))
                    <a href="{{ route('master.kota') }}" class="text-xs text-slate-500 hover:underline">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200">
                        <th class="py-2.5 px-4">Kode Kemendagri</th>
                        <th class="py-2.5 px-4">Nama Kota / Kabupaten</th>
                        <th class="py-2.5 px-4">Latitude</th>
                        <th class="py-2.5 px-4">Longitude</th>
                        <th class="py-2.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($items as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3 px-4 font-mono font-medium text-blue-700">{{ $item->id }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">{{ $item->nama }}</td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $item->latitude }}</td>
                            <td class="py-3 px-4 font-mono text-slate-500">{{ $item->longitude }}</td>
                            <td class="py-3 px-4 text-right">
                                <button onclick="openEditModal('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->latitude }}', '{{ $item->longitude }}')"
                                        class="text-blue-700 hover:underline font-medium">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
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

<!-- Modal Edit Kota -->
<div id="modalEdit" class="fixed inset-0 bg-slate-900/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-sm w-full p-5 shadow-lg border border-slate-200">
        <h3 class="text-xs font-semibold text-slate-900 mb-3 pb-2 border-b border-slate-200 uppercase tracking-wider">Perbarui Data Kota / Kabupaten</h3>
        <form id="formEditKota" method="POST" class="space-y-3 text-xs">
            @csrf
            @method('PUT')
            <div>
                <label class="block font-medium text-slate-700 mb-1">Kode Kota</label>
                <input type="text" id="edit_id" disabled class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded font-mono">
            </div>
            <div>
                <label class="block font-medium text-slate-700 mb-1">Nama Kota / Kabupaten</label>
                <input type="text" id="edit_nama" name="nama" required class="w-full px-3 py-1.5 border border-slate-300 rounded outline-none focus:border-blue-600">
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
    function openEditModal(id, nama, lat, lng) {
        $('#formEditKota').attr('action', `/master/kota/${id}`);
        $('#edit_id').val(id);
        $('#edit_nama').val(nama);
        $('#edit_lat').val(lat);
        $('#edit_lng').val(lng);
        $('#modalEdit').removeClass('hidden');
    }
    function closeEditModal() {
        $('#modalEdit').addClass('hidden');
    }
</script>
@endpush
@endsection
