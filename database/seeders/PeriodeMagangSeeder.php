<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodeMagangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'periode' => 'Semester Genap 2024/2025',
                'keterangan' => 'Periode magang untuk semester genap tahun ajaran 2024/2025.',
            ],
            [
                'periode' => 'Semester Ganjil 2025/2026',
                'keterangan' => 'Periode magang untuk semester ganjil tahun ajaran 2025/2026.',
            ],
        ];
        DB::table('m_periode_magang')->insert($data);
    }
}
