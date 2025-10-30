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
        // 1) Drop the old lokasi column (if it exists)
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            if (Schema::hasColumn('t_lowongan_magang', 'lokasi')) {
                $table->dropColumn('lokasi');
            }
        });

        // 2) Re-add lokasi as a nullable FK to m_provinsi.id
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            $table->unsignedBigInteger('lokasi')
                  ->nullable()                // ← allow NULL for existing rows
                  ->after('periode_id');
            $table->foreign('lokasi')
                  ->references('id')
                  ->on('m_provinsi')
                  ->onUpdate('cascade')
                  ->onDelete('set null');     // ← when a provinsi is deleted, set this to NULL
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            $table->dropForeign(['lokasi']);
            $table->dropColumn('lokasi');
        });

        // restore the old string column
        Schema::table('t_lowongan_magang', function (Blueprint $table) {
            $table->string('lokasi', 255)->after('periode_id');
        });
    }
};
