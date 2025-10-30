<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id' => 2,
                'nama'    => 'Dr. Siti Dosen, M.Kom',
                'email'   => 'siti.dosen@gmail.com',
                'telp'    => '081298765432',
            ],
            [
                'user_id' => 3,
                'nama'    => 'Dr. Budi Raharjo, M.T',
                'email'   => 'budi.raharjo@gmail.com',
                'telp'    => '081211111111',
            ],
            [
                'user_id' => 4,
                'nama'    => 'Dr. Ani Lestari, M.Kom',
                'email'   => 'ani.lestari@gmail.com',
                'telp'    => '081222222222',
            ],
            [
                'user_id' => 5,
                'nama'    => 'Prof. Joko Santoso, Ph.D',
                'email'   => 'joko.santoso@gmail.com',
                'telp'    => '081233333333',
            ],
            [
                'user_id' => 6,
                'nama'    => 'Dr. Rina Marlina, M.Si',
                'email'   => 'rina.marlina@gmail.com',
                'telp'    => '081244444444',
            ],
            [
                'user_id' => 7,
                'nama'    => 'Dr. Ahmad Fauzi, M.Kom',
                'email'   => 'ahmad.fauzi@gmail.com',
                'telp'    => '081255555555',
            ],
            [
                'user_id' => 8,
                'nama'    => 'Dr. Lina Kusuma, M.T',
                'email'   => 'lina.kusuma@gmail.com',
                'telp'    => '081266666666',
            ],
            [
                'user_id' => 9,
                'nama'    => 'Dr. Hendra Wijaya, M.Sc',
                'email'   => 'hendra.wijaya@gmail.com',
                'telp'    => '081277777777',
            ],
            [
                'user_id' => 10,
                'nama'    => 'Dr. Sari Utami, M.Kom',
                'email'   => 'sari.utami@gmail.com',
                'telp'    => '081288888888',
            ],
            [
                'user_id' => 11,
                'nama'    => 'Dr. Bambang Prasetyo, M.T',
                'email'   => 'bambang.prasetyo@gmail.com',
                'telp'    => '081299999999',
            ],
        ];

        DB::table('m_dosen')->insert($data);
    }
}