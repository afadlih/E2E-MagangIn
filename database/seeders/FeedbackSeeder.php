<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'mhs_nim' => '2341720226',
                'target_type' => 'lowongan',
                'lowongan_id' => 1,
                'rating' => 4,
                'komentar' => 'Lowongan ini sangat sesuai dengan minat saya.',
                'created_at' => now(),
            ],
        ];
        DB::table('t_feedback')->insert($data);
    }
}
