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
        Schema::table('m_dosen', function (Blueprint $table) {
            $table->string('cv')->nullable();          // nullable biar boleh kosong dulu
            $table->string('sertifikat')->nullable();
        });
    }

    public function down()
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            $table->dropColumn(['cv', 'sertifikat']);
        });
    }

};
