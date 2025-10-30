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
            $table->string('mhs_nim')->nullable()->after('notifikasi_id');

            $table->foreign('mhs_nim')->references('mhs_nim')->on('m_mahasiswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_notifikasi', function (Blueprint $table) {
            $table->dropForeign(['mhs_nim']);
            $table->dropColumn('mhs_nim');
        });
    }
};
