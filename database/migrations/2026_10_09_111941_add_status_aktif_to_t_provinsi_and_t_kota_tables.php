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
        Schema::table('t_provinsi', function (Blueprint $table): void {
            $table->boolean('status_aktif')->default(true)->index();
        });

        Schema::table('t_kota', function (Blueprint $table): void {
            $table->boolean('status_aktif')->default(true)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_provinsi', function (Blueprint $table): void {
            $table->dropColumn('status_aktif');
        });

        Schema::table('t_kota', function (Blueprint $table): void {
            $table->dropColumn('status_aktif');
        });
    }
};
