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
        Schema::table('m_dosen', function (Blueprint $table) {
            $table->unsignedBigInteger('id_minat')->nullable()->after('nama'); // atur letak sesuai kebutuhan
            $table->foreign('id_minat')
                ->references('id_minat')->on('d_bidang_penelitian')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            $table->dropForeign(['id_minat']); // hapus foreign key dulu
            $table->dropColumn('id_minat ');    // lalu hapus kolom
        });
    }
};
