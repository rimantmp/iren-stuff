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
            $table->foreignId('dusun_id')->nullable()->after('kelurahan_id')->constrained('t_dusun')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyaluran_bantuan', function (Blueprint $table): void {
            $table->dropForeign(['dusun_id']);
            $table->dropColumn('dusun_id');
        });
    }
};
