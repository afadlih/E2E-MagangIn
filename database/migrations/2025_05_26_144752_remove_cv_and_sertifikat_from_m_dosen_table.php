<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCvAndSertifikatFromMDosenTable extends Migration
{
    public function up()
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            $table->dropColumn(['cv', 'sertifikat']);
        });
    }

    public function down()
    {
        Schema::table('m_dosen', function (Blueprint $table) {
            $table->string('cv')->nullable();
            $table->string('sertifikat')->nullable();
        });
    }
}
