<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProgramBantuanController extends Controller
{
    /**
     * Tampilan modul Sembako (Coming Soon)
     */
    public function sembako(): View
    {
        return view('bantuan.coming-soon', [
            'title' => 'Penyaluran Paket Sembako',
            'subtitle' => 'Distribusi paket kebutuhan pokok dan bahan pangan',
            'moduleName' => 'Paket Sembako',
            'badge' => 'Menunggu Brief Data',
            'icon' => 'shopping-bag',
            'description' => 'Modul penyaluran paket sembako sedang dipersiapkan dan menunggu kelengkapan brief data operasional serta skema verifikasi penerima manfaat.',
            'requirements' => [
                'Penyusunan format data penerima (nama kepala keluarga, NIK, jumlah tanggungan)',
                'Daftar rincian isi paket pangan (beras, minyak goreng, gula, dan kebutuhan pokok)',
                'Penentuan titik posko distribusi logistik di tiap kecamatan dan desa',
            ],
        ]);
    }

    /**
     * Tampilan modul Bantuan Tunai Gereja (Coming Soon)
     */
    public function tunaiGereja(): View
    {
        return view('bantuan.coming-soon', [
            'title' => 'Penyaluran Tunai Gereja',
            'subtitle' => 'Bantuan dana operasional peribadatan dan jemaat gereja',
            'moduleName' => 'Bantuan Tunai Gereja',
            'badge' => 'Menunggu Brief Data',
            'icon' => 'cash',
            'description' => 'Modul pencatatan bantuan tunai gereja sedang dalam tahap finalisasi skema administrasi rekening penyaluran dan formulir verifikasi gereja penerima.',
            'requirements' => [
                'Format data gereja sasaran (nama gereja, denominasi, alamat, penanggung jawab)',
                'Mekanisme transfer perbankan atau serah terima tanda terima fisik bertanda tangan',
                'Dokumentasi bukti kuitansi resmi dan laporan pertanggungjawaban dana',
            ],
        ]);
    }

    /**
     * Tampilan modul Pengadaan (Coming Soon)
     */
    public function pengadaan(): View
    {
        return view('bantuan.coming-soon', [
            'title' => 'Pengadaan Sarana & Prasarana',
            'subtitle' => 'Pengadaan fasilitas fisik, inventaris, dan sarana umum',
            'moduleName' => 'Pengadaan Sarana',
            'badge' => 'Menunggu Brief Data',
            'icon' => 'archive',
            'description' => 'Modul pengadaan barang dan logistik sarana umum sedang menunggu spesifikasi teknis barang, RAB, serta alur persetujuan vendor pengadaan.',
            'requirements' => [
                'Katalog jenis barang dan spesifikasi teknis pengadaan sarana',
                'Alur persetujuan anggaran (approval workflow) dan pagu dana',
                'Pencatatan status serah terima fisik barang dan nomor inventaris',
            ],
        ]);
    }
}
