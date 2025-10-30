<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            // Tambahkan kolom durasi (bulan)
            $table->unsignedTinyInteger('durasi')
                  ->nullable()
                  ->comment('Durasi magang (bulan)');
        });
    }

    public function down(): void
    {
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            $table->dropColumn('durasi');
        });
    }
};
