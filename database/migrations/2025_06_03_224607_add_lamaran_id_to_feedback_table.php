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
        Schema::table('t_feedback', function (Blueprint $table) {
            // Tambah kolom jika belum ada
            if (!Schema::hasColumn('t_feedback', 'lamaran_id')) {
                $table->unsignedBigInteger('lamaran_id')->after('feedback_id');
            }

            // Tambahkan foreign key constraint
            $table->foreign('lamaran_id')
                  ->references('lamaran_id')->on('t_lamaran_magang')
                  ->onDelete('cascade'); // atau setNull / restrict sesuai kebutuhan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_feedback', function (Blueprint $table) {
            Schema::table('t_feedback', function (Blueprint $table) {
                $table->dropForeign(['lamaran_id']);
                $table->dropColumn('lamaran_id');
            });
        });
    }
};
