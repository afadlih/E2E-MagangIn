<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_negara')->insert([
            'id' => 1,
            'nama' => 'Indonesia',
            'kode' => 'ID',
        ]);

        $sqlPath = database_path('seeders/sql/provinsi.sql');
        if (File::exists($sqlPath)) {
            DB::unprepared(File::get($sqlPath));
        }

        $sqlPath = database_path('seeders/sql/kabupaten.sql');
        if (File::exists($sqlPath)) {
            DB::unprepared(File::get($sqlPath));
        }

        $sqlPath = database_path('seeders/sql/kecamatan.sql');
        if (File::exists($sqlPath)) {
            DB::unprepared(File::get($sqlPath));
        }

        $sqlPath = database_path('seeders/sql/desa.sql');
        if (File::exists($sqlPath)) {
            DB::unprepared(File::get($sqlPath));
        }
    }
}
