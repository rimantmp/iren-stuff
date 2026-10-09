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
        Schema::create('t_dusun', function (Blueprint $table): void {
            $table->id();
            $table->string('kelurahan_id', 10)->index();
            $table->string('nama', 100);
            $table->string('rw', 20)->nullable();
            $table->string('rt', 20)->nullable();
            $table->string('kepala_dusun', 100)->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_dusun');
    }
};
