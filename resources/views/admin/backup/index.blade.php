@extends('layouts.admin')

@section('title', 'Backup Database - Sistem Informasi Penyaluran Bantuan')
@section('page_title', 'Backup Basis Data')
@section('page_subtitle', 'Pencadangan dan pengamanan berkala seluruh struktur tabel dan riwayat penyaluran bantuan')

@section('header_actions')
    <form action="{{ route('admin.backup.store') }}" method="POST" onsubmit="handleBackupSubmit(event, this)">
        @csrf
        <button type="submit" id="btn-backup-top" class="inline-flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition active:scale-95">
            <svg id="icon-backup-top" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
            </svg>
            <span id="label-backup-top">Buat Cadangan Baru</span>
        </button>
    </form>
@endsection

@section('content')
<div class="space-y-6">

    <!-- Flash Alert Sukses / Error -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start space-x-3 text-emerald-800 animate-fadeIn">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1 text-xs sm:text-sm font-medium leading-relaxed">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-start space-x-3 text-rose-800 animate-fadeIn">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1 text-xs sm:text-sm font-medium leading-relaxed">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Kartu Statistik Basis Data -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Basis Data Aktif -->
        <div class="bg-white border border-slate-200/80 rounded-xl p-4 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Basis Data</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                </span>
            </div>
            <p class="text-lg font-bold text-slate-900 mt-2 truncate">{{ $stats['database'] }}</p>
            <div class="flex items-center space-x-1.5 mt-1">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                <span class="text-[11px] text-slate-500 capitalize">Driver: {{ $stats['driver'] }}</span>
            </div>
        </div>

        <!-- Jumlah Tabel -->
        <div class="bg-white border border-slate-200/80 rounded-xl p-4 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tabel Terdaftar</span>
                <span class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </span>
            </div>
            <p class="text-lg font-bold text-slate-900 mt-2">{{ number_format($stats['tables_count']) }} Tabel</p>
            <p class="text-[11px] text-slate-500 mt-1">Struktur tabel & indeks aktif</p>
        </div>

        <!-- Total File Backup -->
        <div class="bg-white border border-slate-200/80 rounded-xl p-4 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">File Cadangan</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </span>
            </div>
            <p class="text-lg font-bold text-slate-900 mt-2">{{ count($backups) }} Berkas</p>
            <p class="text-[11px] text-slate-500 mt-1">Tersimpan di sistem lokal</p>
        </div>

        <!-- Terakhir Dicadangkan -->
        <div class="bg-white border border-slate-200/80 rounded-xl p-4 shadow-sm hover:shadow transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Backup Terakhir</span>
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="text-sm font-bold text-slate-900 mt-2 truncate">
                {{ !empty($backups) ? $backups[0]['created_at_human'] : 'Belum pernah' }}
            </p>
            <p class="text-[11px] text-slate-500 mt-1">
                {{ !empty($backups) ? $backups[0]['created_at']->format('d/m/Y, H:i') : 'Silakan buat backup pertama' }}
            </p>
        </div>
    </div>

    <!-- Banner Aksi Pencadangan Cepat -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 border border-slate-800 rounded-2xl p-6 text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <svg class="w-64 h-64 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
            </svg>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="max-w-2xl space-y-1.5">
                <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[11px] font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                    <span>Snapshot Basis Data Terpadu</span>
                </div>
                <h2 class="text-base sm:text-lg font-bold text-white">Amankan Seluruh Data Penyaluran Bantuan</h2>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Fitur ini akan mengekspor seluruh skema tabel (<code class="bg-slate-800 px-1 py-0.5 rounded text-blue-300 text-[11px]">DDL</code>) beserta jutaan baris data (<code class="bg-slate-800 px-1 py-0.5 rounded text-blue-300 text-[11px]">DML</code>) menjadi file standar <code class="bg-slate-800 px-1 py-0.5 rounded text-blue-300 text-[11px]">.sql</code> yang siap diimpor ke phpMyAdmin, HeidiSQL, atau MySQL Server kapan saja.
                </p>
            </div>
            <div class="flex-shrink-0">
                <form action="{{ route('admin.backup.store') }}" method="POST" onsubmit="handleBackupSubmit(event, this)">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2.5 px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-blue-900/40 transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <span>Mulai Pencadangan Sekarang</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar File Cadangan -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Riwayat File Cadangan</h3>
                <p class="text-xs text-slate-500">Daftar file cadangan basis data (.sql) yang tersimpan di sistem penyimpanan lokal</p>
            </div>
            <div class="text-xs font-medium text-slate-500">
                Total: <span class="font-semibold text-slate-800">{{ count($backups) }} berkas cadangan</span>
            </div>
        </div>

        @if(count($backups) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Nama Berkas Cadangan</th>
                            <th class="py-3 px-4">Ukuran</th>
                            <th class="py-3 px-4">Waktu Pembuatan</th>
                            <th class="py-3 px-4 text-center w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($backups as $index => $backup)
                            <tr class="hover:bg-blue-50/40 transition">
                                <td class="py-3.5 px-4 text-center font-medium text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-medium text-slate-900">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="truncate max-w-xs sm:max-w-md">{{ $backup['filename'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $backup['size_formatted'] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    <div class="font-medium text-slate-800">{{ $backup['created_at']->format('d/m/Y, H:i:s') }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $backup['created_at_human'] }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="inline-flex items-center space-x-2">
                                        <!-- Tombol Unduh -->
                                        <a href="{{ route('admin.backup.download', $backup['filename']) }}"
                                           title="Unduh Berkas .SQL"
                                           class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white rounded-lg font-semibold text-xs transition border border-blue-200 hover:border-transparent">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                            <span>Unduh</span>
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('admin.backup.destroy', $backup['filename']) }}"
                                              method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus file cadangan \'{{ $backup['filename'] }}\'? File yang dihapus tidak dapat dipulihkan.')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus Berkas Cadangan"
                                                    class="inline-flex items-center space-x-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white rounded-lg font-semibold text-xs transition border border-rose-200 hover:border-transparent">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="py-12 px-4 text-center">
                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum Ada File Cadangan</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Basis data belum pernah dicadangkan ke penyimpanan. Klik tombol di bawah untuk membuat berkas cadangan pertama Anda.
                </p>
                <div class="mt-4">
                    <form action="{{ route('admin.backup.store') }}" method="POST" onsubmit="handleBackupSubmit(event, this)">
                        @csrf
                        <button type="submit" class="inline-flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                            <span>Buat Cadangan Pertama</span>
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Panduan & Tips Pemulihan (Restore) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Cara Restore via phpMyAdmin -->
        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm">
            <div class="flex items-center space-x-2.5 mb-3">
                <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </span>
                <h4 class="text-xs sm:text-sm font-bold text-slate-900">Cara Restore via phpMyAdmin</h4>
            </div>
            <ol class="list-decimal list-inside text-xs text-slate-600 space-y-1.5 leading-relaxed">
                <li>Unduh berkas cadangan <code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded text-[11px]">.sql</code> ke komputer Anda.</li>
                <li>Buka dashboard phpMyAdmin dan pilih database target (<code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded text-[11px]">{{ $stats['database'] }}</code>).</li>
                <li>Klik tab <strong>Import</strong> pada navigasi atas.</li>
                <li>Pilih file <code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded text-[11px]">.sql</code> yang telah diunduh, lalu klik tombol <strong>Kirim (Go)</strong>.</li>
            </ol>
        </div>

        <!-- Cara Restore via Terminal / CLI -->
        <div class="bg-white border border-slate-200/80 rounded-xl p-5 shadow-sm">
            <div class="flex items-center space-x-2.5 mb-3">
                <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </span>
                <h4 class="text-xs sm:text-sm font-bold text-slate-900">Cara Restore via Terminal / CLI</h4>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed mb-2">
                Jalankan perintah SQL client berikut di terminal / command prompt server:
            </p>
            <div class="bg-slate-900 text-emerald-400 p-3 rounded-lg font-mono text-[11px] overflow-x-auto select-all">
                mysql -u root -p {{ $stats['database'] }} &lt; nama_file_backup.sql
            </div>
            <p class="text-[10px] text-slate-400 mt-2">
                *File cadangan tersimpan secara aman di folder <code class="text-slate-600">storage/app/private/backups/</code> dan tidak dapat diakses secara publik.
            </p>
        </div>
    </div>

</div>

<!-- Loading overlay script -->
<script>
    function handleBackupSubmit(event, form) {
        const btn = form.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Sedang Mencadangkan...</span>
            `;
        }
    }
</script>
@endsection
