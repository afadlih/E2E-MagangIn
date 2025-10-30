<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'level_id' => 1,
                'level_name' => 'admin',
                'description' => 'Administrator dengan akses penuh ke sistem',
            ],
            [
                'level_id' => 2,
                'level_name' => 'dosen',
                'description' => 'Dosen pembimbing dan pengelola data mahasiswa',
            ],
            [
                'level_id' => 3,
                'level_name' => 'mahasiswa',
                'description' => 'Mahasiswa yang mengakses dan mengisi data magang',
            ],
        ];
        DB::table('r_auth_level')->insert($data);
    }
}
