<?php

namespace Database\Seeders;

use App\Models\JenisBantuan;
use App\Models\Kelurahan;
use App\Models\PenyaluranBantuan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::firstOrCreate(
            ['email' => 'admin@bantuan.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. Master Jenis Bantuan
        $air = JenisBantuan::firstOrCreate(
            ['slug' => 'air'],
            [
                'nama' => 'Air Bersih',
                'satuan' => 'Liter',
                'deskripsi' => 'Penyaluran air bersih untuk warga terdampak kekeringan dan krisis air.',
                'icon' => 'droplet',
                'status_aktif' => true,
            ]
        );

        JenisBantuan::firstOrCreate(
            ['slug' => 'sembako'],
            [
                'nama' => 'Sembako',
                'satuan' => 'Paket',
                'deskripsi' => 'Paket sembako dan kebutuhan pokok masyarakat.',
                'icon' => 'shopping-bag',
                'status_aktif' => true,
            ]
        );

        JenisBantuan::firstOrCreate(
            ['slug' => 'tunai-gereja'],
            [
                'nama' => 'Tunai Gereja',
                'satuan' => 'Rupiah',
                'deskripsi' => 'Bantuan dana tunai pembinaan dan operasional gereja / jemaat.',
                'icon' => 'church',
                'status_aktif' => true,
            ]
        );

        JenisBantuan::firstOrCreate(
            ['slug' => 'pengadaan'],
            [
                'nama' => 'Pengadaan',
                'satuan' => 'Unit',
                'deskripsi' => 'Pengadaan sarana prasarana fisik, toren penampungan, dan fasilitas umum.',
                'icon' => 'wrench',
                'status_aktif' => true,
            ]
        );

        // 3. Sample Data Bantuan Air (Mengambil contoh kelurahan yang ada koordinatnya)
        $sampleKelurahan = Kelurahan::where('latitude', '!=', 0)->take(3)->get();

        if ($sampleKelurahan->count() >= 3) {
            $kel1 = $sampleKelurahan[0];
            $kel2 = $sampleKelurahan[1];
            $kel3 = $sampleKelurahan[2];

            PenyaluranBantuan::firstOrCreate(
                ['kode_transaksi' => 'BA-202610-0001'],
                [
                    'jenis_bantuan_id' => $air->id,
                    'provinsi_id' => substr($kel1->id, 0, 2),
                    'kota_id' => substr($kel1->id, 0, 4),
                    'kecamatan_id' => substr($kel1->id, 0, 6),
                    'kelurahan_id' => $kel1->id,
                    'alamat_detail' => 'Dusun Krajan RT 02 / RW 01, Dekat Balai Warga',
                    'latitude' => $kel1->latitude,
                    'longitude' => $kel1->longitude,
                    'nama_penerima' => 'Bpk. Ahmad Sujana (Ketua RT 02)',
                    'kontak_penerima' => '081234567890',
                    'jumlah_kk' => 45,
                    'jumlah_jiwa' => 180,
                    'jumlah_bantuan' => 10000,
                    'satuan' => 'Liter',
                    'tanggal_rencana' => now()->subDays(2)->toDateString(),
                    'tanggal_penyaluran' => now()->subDays(2)->toDateString(),
                    'status' => 'TERSALURKAN',
                    'metode_distribusi' => '2 Truk Tangki 5000L',
                    'nomor_armada' => 'B 9123 TAA & B 9124 TAA',
                    'nama_petugas' => 'Rahmat Hidayat (Relawan Air Bersih)',
                    'sumber_air' => 'PDAM Depot Sumber Air Bersih Utama',
                    'catatan' => 'Penyaluran lancar langsung diisikan ke toren penampung warga dan jerigen penduduk.',
                ]
            );

            PenyaluranBantuan::firstOrCreate(
                ['kode_transaksi' => 'BA-202610-0002'],
                [
                    'jenis_bantuan_id' => $air->id,
                    'provinsi_id' => substr($kel2->id, 0, 2),
                    'kota_id' => substr($kel2->id, 0, 4),
                    'kecamatan_id' => substr($kel2->id, 0, 6),
                    'kelurahan_id' => $kel2->id,
                    'alamat_detail' => 'Komplek Permukiman Warga RW 04, Belakang Posyandu',
                    'latitude' => $kel2->latitude,
                    'longitude' => $kel2->longitude,
                    'nama_penerima' => 'Ibu Siti Aminah (Kader Posyandu)',
                    'kontak_penerima' => '085678901234',
                    'jumlah_kk' => 60,
                    'jumlah_jiwa' => 240,
                    'jumlah_bantuan' => 15000,
                    'satuan' => 'Liter',
                    'tanggal_rencana' => now()->toDateString(),
                    'tanggal_penyaluran' => null,
                    'status' => 'PROSES',
                    'metode_distribusi' => '3 Truk Tangki 5000L',
                    'nomor_armada' => 'B 9445 KLO',
                    'nama_petugas' => 'Suryanto & Tim Armada 2',
                    'sumber_air' => 'Mata Air Ciburial',
                    'catatan' => 'Armada sedang perjalanan menuju titik lokasi pengisian toren.',
                ]
            );

            PenyaluranBantuan::firstOrCreate(
                ['kode_transaksi' => 'BA-202610-0003'],
                [
                    'jenis_bantuan_id' => $air->id,
                    'provinsi_id' => substr($kel3->id, 0, 2),
                    'kota_id' => substr($kel3->id, 0, 4),
                    'kecamatan_id' => substr($kel3->id, 0, 6),
                    'kelurahan_id' => $kel3->id,
                    'alamat_detail' => 'Desa Babakan Hilir, Area Sekitar Gereja GKJ & Warga Sekitar',
                    'latitude' => $kel3->latitude,
                    'longitude' => $kel3->longitude,
                    'nama_penerima' => 'Pdt. Samuel & Pengurus Lingkungan',
                    'kontak_penerima' => '082198765432',
                    'jumlah_kk' => 35,
                    'jumlah_jiwa' => 130,
                    'jumlah_bantuan' => 5000,
                    'satuan' => 'Liter',
                    'tanggal_rencana' => now()->addDays(2)->toDateString(),
                    'tanggal_penyaluran' => null,
                    'status' => 'RENCANA',
                    'metode_distribusi' => '1 Truk Tangki 5000L',
                    'nomor_armada' => 'B 9011 ZYX',
                    'nama_petugas' => 'Dedi Setiawan',
                    'sumber_air' => 'PDAM Cabang Timur',
                    'catatan' => 'Jadwal hari Sabtu pagi sebelum ibadah akhir pekan.',
                ]
            );
        }
    }
}
