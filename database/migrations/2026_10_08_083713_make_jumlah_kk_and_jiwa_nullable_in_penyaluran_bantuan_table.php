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
        Schema::table('penyaluran_bantuan', function (Blueprint $table): void {
            $table->integer('jumlah_kk')->nullable()->default(null)->change();
            $table->integer('jumlah_jiwa')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyaluran_bantuan', function (Blueprint $table): void {
            $table->integer('jumlah_kk')->default(1)->nullable(false)->change();
            $table->integer('jumlah_jiwa')->default(1)->nullable(false)->change();
        });
    }
};
