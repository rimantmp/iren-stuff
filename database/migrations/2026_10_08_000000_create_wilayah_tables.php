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
        if (! Schema::hasTable('t_provinsi')) {
            Schema::create('t_provinsi', function (Blueprint $table): void {
                $table->string('id', 10)->primary();
                $table->string('nama', 100);
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
            });
        }

        if (! Schema::hasTable('t_kota')) {
            Schema::create('t_kota', function (Blueprint $table): void {
                $table->string('id', 10)->primary();
                $table->string('nama', 100);
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
            });
        }

        if (! Schema::hasTable('t_kecamatan')) {
            Schema::create('t_kecamatan', function (Blueprint $table): void {
                $table->string('id', 10)->primary();
                $table->string('nama', 100);
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
            });
        }

        if (! Schema::hasTable('t_kelurahan')) {
            Schema::create('t_kelurahan', function (Blueprint $table): void {
                $table->string('id', 10)->primary();
                $table->string('nama', 100);
                $table->double('latitude')->nullable();
                $table->double('longitude')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_kelurahan');
        Schema::dropIfExists('t_kecamatan');
        Schema::dropIfExists('t_kota');
        Schema::dropIfExists('t_provinsi');
    }
};
