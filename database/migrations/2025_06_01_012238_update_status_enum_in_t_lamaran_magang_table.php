<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('t_lamaran_magang', function (Blueprint $table) {
             DB::statement("ALTER TABLE t_lamaran_magang MODIFY status ENUM('pending', 'diterima', 'ditolak', 'selesai') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_lamaran_magang', function (Blueprint $table) {
            DB::statement("ALTER TABLE t_lamaran_magang MODIFY status ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending'");
        });
    }
};
