<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up(): void
{
    Schema::table('m_mahasiswa', function (Blueprint $table) {
        $table->string('pref', 100)->nullable();
        $table->string('skill', 100)->nullable();
        $table->string('lokasi', 100)->nullable();
        $table->unsignedBigInteger('gaji_minimum')->nullable();
        $table->unsignedTinyInteger('durasi')
              ->nullable()
              ->comment('Durasi preferensi (bulan)');
    });

    Schema::table('t_lowongan_magang', function (Blueprint $table) {
        $table->unsignedBigInteger('gaji')->nullable()
              ->comment('Gaji minimal / paket yang ditawarkan');
    });
}
    public function down(): void
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['pref', 'skill', 'lokasi', 'gaji_minimum', 'durasi']);
        });

        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            $table->dropColumn('gaji');
        });
    }
};
