<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $now = Carbon::now();

        $skills = [
            'Teamwork',
            'Communication',
            'Problem Solving',
            'Leadership',
            'Creativity',
            'Adaptability',
            'Time Management',
            'Critical Thinking',
            'Technical Writing',
            'Programming',
        ];

        $data = array_map(function ($skill) use ($now) {
            return [
                'nama'       => $skill,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $skills);

        DB::table('skills')->insert($data);
    }
}
