<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mahasiswa_skill', function (Blueprint $table) {
            $table->string('mhs_nim');         // FK to m_mahasiswa.mhs_nim
            $table->unsignedBigInteger('skill_id');  // FK to skills.id

            $table->primary(['mhs_nim', 'skill_id']);

            $table->foreign('mhs_nim')
                  ->references('mhs_nim')
                  ->on('m_mahasiswa')
                  ->onDelete('cascade');

            $table->foreign('skill_id')
                  ->references('id')
                  ->on('skills')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('mahasiswa_skill');
    }
};
