<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_prodi' => 'Teknik Informatika',
                'jurusan'    => 'Teknologi Informasi',
            ],
            [
                'nama_prodi' => 'Sistem Informasi Bisnis',
                'jurusan'    => 'Teknologi Informasi',
            ],
        ];
            DB::table('m_program_studi')->insert($data);
    }
}
