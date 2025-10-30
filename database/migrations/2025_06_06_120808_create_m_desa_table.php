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
        Schema::create('m_desa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('m_kecamatan')->onDelete('cascade');
            $table->string('nama', 100);
            $table->string('kode', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_desa');
    }
};
