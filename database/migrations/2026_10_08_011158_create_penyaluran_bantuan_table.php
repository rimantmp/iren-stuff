<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penyaluran_bantuan', function (Blueprint $table): void {
            $table->id();
            $table->string('kode_transaksi')->unique();
            $table->foreignId('jenis_bantuan_id')->constrained('jenis_bantuan')->cascadeOnDelete();

            // Wilayah Kemendagri / BPS
            $table->string('provinsi_id', 10);
            $table->string('kota_id', 10);
            $table->string('kecamatan_id', 10);
            $table->string('kelurahan_id', 10);
            $table->text('alamat_detail')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();

            // Penerima Manfaat
            $table->string('nama_penerima');
            $table->string('kontak_penerima')->nullable();
            $table->integer('jumlah_kk')->default(1);
            $table->integer('jumlah_jiwa')->default(1);

            // Bantuan
            $table->double('jumlah_bantuan');
            $table->string('satuan');
            $table->date('tanggal_rencana');
            $table->date('tanggal_penyaluran')->nullable();
            $table->enum('status', ['RENCANA', 'PROSES', 'TERSALURKAN'])->default('RENCANA');

            // Logistik & Operasional Air
            $table->string('metode_distribusi')->nullable(); // Truk Tangki, Toren Publik, Sumur Bor, dll.
            $table->string('nomor_armada')->nullable();     // Plat truk tangki
            $table->string('nama_petugas')->nullable();     // Driver / Penanggung jawab
            $table->string('sumber_air')->nullable();       // PDAM / Mata Air dll.
            $table->string('foto_dokumentasi')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyaluran_bantuan');
    }
};
