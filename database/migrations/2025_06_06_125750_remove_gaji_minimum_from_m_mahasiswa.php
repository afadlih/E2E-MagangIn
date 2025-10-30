<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            // 1) If the old gaji_minimum column exists, drop it
            if (Schema::hasColumn('m_mahasiswa', 'gaji_minimum')) {
                $table->dropColumn('gaji_minimum');
            }

            // 2) Only add the enum column if it doesn’t already exist
            if (! Schema::hasColumn('m_mahasiswa', 'tipe_bekerja')) {
                $table->enum('tipe_bekerja', ['remote', 'on_site', 'hybrid'])
                      ->default('remote');
            }
        });
    }

    public function down()
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->dropColumn('tipe_bekerja');
            $table->integer('gaji_minimum')
                  ->nullable()
                  ->after('lokasi');
        });
    }
};
