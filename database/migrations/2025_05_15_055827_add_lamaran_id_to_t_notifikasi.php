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
        Schema::table('t_notifikasi', function (Blueprint $table) {
            $table->unsignedBigInteger('lamaran_id')->after('penerima_id'); // tambahkan kolom
            $table->foreign('lamaran_id')->references('lamaran_id')->on('t_lamaran_magang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_notifikasi', function (Blueprint $table) {
            $table->dropForeign(['lamaran_id']);
        });
    }
};
