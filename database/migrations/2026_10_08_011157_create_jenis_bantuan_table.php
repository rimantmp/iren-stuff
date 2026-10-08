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
        Schema::create('jenis_bantuan', function (Blueprint $table): void {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('satuan')->default('Unit');
            $table->text('deskripsi')->nullable();
            $table->string('icon')->default('box');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_bantuan');
    }
};
