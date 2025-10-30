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
        if (!Schema::hasColumn('m_m_provinsi', 'latitude')) {
            Schema::table('m_provinsi', function (Blueprint $table) {
                $table->string('alt_name')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }

        if (!Schema::hasColumn('m_m_kabupaten', 'latitude')) {
            Schema::table('m_kabupaten', function (Blueprint $table) {
                $table->string('alt_name')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }

        if (!Schema::hasColumn('m_kecamatan', 'latitude')) {
            Schema::table('m_kecamatan', function (Blueprint $table) {
                $table->string('alt_name')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }

        if (!Schema::hasColumn('m_desa', 'latitude')) {
            Schema::table('m_desa', function (Blueprint $table) {
                $table->string('alt_name')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }

        if (!Schema::hasColumn('t_prefrensi_lokasi_mahasiswa', 'latitude')) {
            Schema::table('t_prefrensi_lokasi_mahasiswa', function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
