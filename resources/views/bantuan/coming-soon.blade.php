@extends('layouts.admin')

@section('title', $title)
@section('page_title', $title)
@section('page_subtitle', $subtitle)

@section('header_actions')
<a href="{{ route('admin.dashboard') }}"
   class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition">
    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    <span>Kembali ke Dashboard</span>
</a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Coming Soon Card -->
    <div class="bg-white rounded-lg border border-slate-200 p-8 shadow-sm text-center">
        <!-- Module Icon -->
        <div class="w-16 h-16 rounded-full bg-slate-100 border border-slate-200 mx-auto flex items-center justify-center text-slate-600 mb-4">
            @if($icon === 'shopping-bag')
                <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            @elseif($icon === 'cash')
                <svg class="w-8 h-8 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            @else
                <svg class="w-8 h-8 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            @endif
        </div>

        <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200 mb-2">
            {{ $badge }}
        </span>

        <h2 class="text-base font-bold text-slate-900 mb-2">
            Modul {{ $moduleName }} Segera Hadir
        </h2>

        <p class="text-xs text-slate-600 max-w-lg mx-auto leading-relaxed mb-6">
            {{ $description }}
        </p>

        <!-- Readiness Checklist Card -->
        <div class="bg-slate-50 rounded-lg border border-slate-200 p-5 text-left max-w-lg mx-auto mb-6">
            <span class="text-[11px] font-bold text-slate-800 uppercase tracking-wider block mb-2.5">
                Kebutuhan Data Sebelum Implementasi:
            </span>
            <ul class="space-y-2 text-xs text-slate-600">
                @foreach($requirements as $req)
                    <li class="flex items-start space-x-2">
                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $req }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Action Links -->
        <div class="flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('bantuan.air.index') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-medium transition focus:ring-2 focus:ring-offset-1 focus:ring-blue-600 shadow-sm">
                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span>Kelola Penyaluran Bantuan Air</span>
            </a>

            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-medium transition">
                <span>Ke Beranda Dashboard</span>
            </a>
        </div>
    </div>

</div>
@endsection
