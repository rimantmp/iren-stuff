@extends('layouts.admin')

@section('title', 'Master Data Kota / Kabupaten')
@section('page_title', 'Master Data Kota dan Kabupaten')
@section('page_subtitle', 'Daftar 514 Kota dan Kabupaten administratif di Indonesia')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Quick Preset -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 rounded-lg border border-slate-200">
        <div class="flex items-center space-x-2 text-xs">
            <span class="font-medium text-slate-700">Ringkasan Wilayah:</span>
            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-semibold rounded-full border border-slate-200">
                Total: {{ $items->total() }} Kota/Kab
            </span>
            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 font-semibold rounded-full border border-emerald-200">
                Aktif: {{ $totalAktif }}
            </span>
            <span class="px-2.5 py-1 bg-slate-50 text-slate-500 font-semibold rounded-full border border-slate-200">
                Nonaktif: {{ $totalNonaktif }}
            </span>
        </div>

        <!-- Batch & Preset Actions -->
        <div class="flex flex-wrap items-center gap-2">
            <form id="formPresetToraja" method="POST" action="{{ route('master.kota.batch-status') }}">
                @csrf
                <input type="hidden" name="action" value="preset_toraja">
                <button type="button"
                        onclick="confirmAction({
                            title: 'Terapkan Preset: Dapil Toraja',
                            message: 'Hanya <b>Kab. Tana Toraja & Toraja Utara</b> (Sulawesi Selatan) yang akan diaktifkan. Seluruh kabupaten/kota lainnya di Indonesia akan dinonaktifkan dari form dan pencarian wilayah.',
                            confirmText: 'Ya, Terapkan Preset',
                            theme: 'blue',
                            targetForm: document.getElementById('formPresetToraja')
                        })"
                        class="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Preset: Dapil Toraja (Tana Toraja & Torut)
                </button>
            </form>

            <form id="formActivateAllKota" method="POST" action="{{ route('master.kota.batch-status') }}">
                @csrf
                <input type="hidden" name="action" value="activate_all">
                @if(request('provinsi_id'))
                    <input type="hidden" name="provinsi_id" value="{{ request('provinsi_id') }}">
                @endif
                <button type="button"
                        onclick="confirmAction({
                            title: 'Aktifkan Semua Kota/Kabupaten',
                            message: 'Apakah Anda yakin ingin mengaktifkan seluruh kota/kabupaten{{ request('provinsi_id') ? ' pada provinsi ini' : '' }}?',
                            confirmText: 'Ya, Aktifkan',
                            theme: 'emerald',
                            targetForm: document.getElementById('formActivateAllKota')
                        })"
                        class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 rounded text-xs font-medium transition cursor-pointer">
                    Aktifkan Semua
                </button>
            </form>

            <form id="formDeactivateAllKota" method="POST" action="{{ route('master.kota.batch-status') }}">
                @csrf
                <input type="hidden" name="action" value="deactivate_all">
                @if(request('provinsi_id'))
                    <input type="hidden" name="provinsi_id" value="{{ request('provinsi_id') }}">
                @endif
                <button type="button"
                        onclick="confirmAction({
                            title: 'Nonaktifkan Semua Kota/Kabupaten',
                            message: 'Apakah Anda yakin ingin menonaktifkan seluruh kota/kabupaten{{ request('provinsi_id') ? ' pada provinsi ini' : '' }}? Kota yang dinonaktifkan tidak akan muncul pada pilihan pencarian form bantuan.',
                            confirmText: 'Ya, Nonaktifkan',
                            theme: 'amber',
                            targetForm: document.getElementById('formDeactivateAllKota')
                        })"
                        class="px-2.5 py-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200 rounded text-xs font-medium transition cursor-pointer">
                    Nonaktifkan Semua
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
        <!-- Toolbar & Filter -->
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Filter Status Tab -->
            <div class="flex items-center space-x-1 bg-slate-100 p-1 rounded-md text-xs">
                <a href="{{ route('master.kota', array_merge(request()->except(['page', 'status']))) }}"
                   class="px-2.5 py-1 rounded font-medium transition {{ !request('status') ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Semua
                </a>
                <a href="{{ route('master.kota', array_merge(request()->except('page'), ['status' => 'aktif'])) }}"
                   class="px-2.5 py-1 rounded font-medium transition {{ request('status') === 'aktif' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Aktif ({{ $totalAktif }})
                </a>
                <a href="{{ route('master.kota', array_merge(request()->except('page'), ['status' => 'nonaktif'])) }}"
                   class="px-2.5 py-1 rounded font-medium transition {{ request('status') === 'nonaktif' ? 'bg-white text-slate-800 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Nonaktif ({{ $totalNonaktif }})
                </a>
            </div>

            <form method="GET" action="{{ route('master.kota') }}" class="flex flex-wrap items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <select name="provinsi_id" onchange="this.form.submit()" class="px-2.5 py-1.5 border border-slate-300 rounded text-xs outline-none bg-white">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinsiList as $prov)
                        <option value="{{ $prov->id }}" {{ request('provinsi_id') == $prov->id ? 'selected' : '' }}>
                            {{ $prov->nama }} {{ !$prov->status_aktif ? '(Nonaktif)' : '' }}
                        </option>
                    @endforeach
                </select>

                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama kota..."
                       class="px-3 py-1.5 border border-slate-300 rounded text-xs outline-none focus:border-blue-600 w-44">
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white rounded text-xs font-medium">
                    Cari
                </button>
                @if(request('search') || request('provinsi_id') || request('status'))
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
                                <form method="POST" action="{{ route('master.kota.toggle', $item->id) }}" class="inline">
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
                                Tidak ada data kota/kabupaten yang cocok dengan filter.
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

<!-- Modal Konfirmasi Aksi Elegan -->
<div id="modalConfirmAction" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-start gap-3.5">
            <div id="confirmIconWrapper" class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 id="confirmTitle" class="text-sm font-bold text-slate-900 leading-tight"></h3>
                <div id="confirmMessage" class="mt-2 text-xs text-slate-600 leading-relaxed"></div>
                <div class="mt-3.5 p-2.5 bg-slate-50 border border-slate-200/80 rounded-lg text-[11px] text-slate-600 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span><b>Data Aman:</b> Tidak ada data transaksi penyaluran yang terhapus.</span>
                </div>
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-end gap-2.5">
            <button type="button" onclick="closeConfirmModal()" class="px-3.5 py-2 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition cursor-pointer">
                Batal
            </button>
            <button type="button" id="confirmSubmitBtn" class="px-4 py-2 text-xs font-semibold text-white rounded-lg shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>Ya, Lanjutkan</span>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentConfirmForm = null;

    function confirmAction({ title, message, confirmText, theme, targetForm }) {
        currentConfirmForm = targetForm;
        $('#confirmTitle').text(title);
        $('#confirmMessage').html(message);
        $('#confirmSubmitBtn span').text(confirmText || 'Ya, Lanjutkan');

        const $btn = $('#confirmSubmitBtn');
        const $iconWrapper = $('#confirmIconWrapper');

        $btn.removeClass('bg-blue-600 hover:bg-blue-700 bg-emerald-600 hover:bg-emerald-700 bg-amber-600 hover:bg-amber-700');
        $iconWrapper.removeClass('bg-blue-50 text-blue-600 border border-blue-200 bg-emerald-50 text-emerald-600 border border-emerald-200 bg-amber-50 text-amber-600 border border-amber-200');

        if (theme === 'emerald') {
            $btn.addClass('bg-emerald-600 hover:bg-emerald-700');
            $iconWrapper.addClass('bg-emerald-50 text-emerald-600 border border-emerald-200');
        } else if (theme === 'amber') {
            $btn.addClass('bg-amber-600 hover:bg-amber-700');
            $iconWrapper.addClass('bg-amber-50 text-amber-600 border border-amber-200');
        } else {
            $btn.addClass('bg-blue-600 hover:bg-blue-700');
            $iconWrapper.addClass('bg-blue-50 text-blue-600 border border-blue-200');
        }

        $('#modalConfirmAction').removeClass('hidden');
    }

    function closeConfirmModal() {
        $('#modalConfirmAction').addClass('hidden');
        currentConfirmForm = null;
    }

    $('#confirmSubmitBtn').on('click', function() {
        if (currentConfirmForm) {
            currentConfirmForm.submit();
        }
    });

    function openEditModal(id, nama, lat, lng, statusAktif) {
        $('#formEditKota').attr('action', `/master/kota/${id}`);
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
