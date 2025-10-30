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
        Schema::create('t_minat_mahasiswa', function (Blueprint $table) {
    $table->string('mhs_nim', 20);
    $table->foreign('mhs_nim')
          ->references('mhs_nim')
          ->on('m_mahasiswa')
          ->onDelete('cascade');

    $table->unsignedBigInteger('bidang_keahlian_id');
    $table->foreign('bidang_keahlian_id')->references('id')->on('m_bidang_keahlian')->onDelete('cascade');

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_minat_mahasiswa');
    }
};
