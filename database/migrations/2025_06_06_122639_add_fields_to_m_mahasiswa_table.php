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
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->string('angkatan', 4);
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->string('file_cv')->nullable()->after('ipk');
            $table->string('provinsi', 100)->nullable()->after('file_cv');
            $table->string('Kabupaten', 100)->nullable()->after('provinsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_mahasiswa', function (Blueprint $table) {
            $table->dropColumn(['angkatan', 'jenis_kelamin', 'ipk', 'file_cv', 'provinsi', 'Kabupaten']);
        });
    }
};
