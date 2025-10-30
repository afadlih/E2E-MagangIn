<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidangPenelitianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('d_bidang_penelitian')->insert([
            ['bidang' => 'Kecerdasan Buatan'],
            ['bidang' => 'Rekayasa Perangkat Lunak'],
            ['bidang' => 'Jaringan Komputer'],
            ['bidang' => 'Sistem Informasi'],
            ['bidang' => 'Keamanan Siber'],
            ['bidang' => 'Big Data'],
            ['bidang' => 'Internet of Things (IoT)'],
            ['bidang' => 'Sistem Tertanam'],
            ['bidang' => 'Cloud Computing'],
            ['bidang' => 'Pengolahan Citra Digital'],
            ['bidang' => 'Komputasi Mobile'],
            ['bidang' => 'Teknologi Blockchain'],
            ['bidang' => 'Robotika'],
            ['bidang' => 'Interaksi Manusia dan Komputer'],
            ['bidang' => 'Sistem Cerdas'],
            ['bidang' => 'E-Government'],
            ['bidang' => 'E-Learning'],
            ['bidang' => 'Teknologi Pendidikan'],
            ['bidang' => 'Augmented Reality & Virtual Reality'],
            ['bidang' => 'Data Mining'],
        ]);
    }
}
