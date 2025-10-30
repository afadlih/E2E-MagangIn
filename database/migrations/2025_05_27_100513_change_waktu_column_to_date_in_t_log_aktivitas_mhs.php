<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeWaktuColumnToDateInTLogAktivitasMhs extends Migration
{
    public function up()
    {
        // Ubah data waktu menjadi hanya tanggal
        DB::statement('UPDATE t_log_aktivitas_mhs SET waktu = DATE(waktu)');
       
    }

    public function down()
    {
        Schema::table('t_log_aktivitas_mhs', function (Blueprint $table) {
            // Kembalikan ke timestamp jika migrasi dibatalkan
            
        });
    }
}