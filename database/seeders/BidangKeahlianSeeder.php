<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BidangKeahlianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listBidangKeahlianTI = [
            'Fullstack Developer',
            'Frontend Developer',
            'Backend Developer',
            'PHP Developer',
            'Python Developer',
            'Node.js Developer',
            'Java Developer',
            'Mobile Developer',
            'Android Developer',
            'iOS Developer',
            'DevOps Engineer',
            'Cloud Engineer',
            'AWS Specialist',
            'Azure Specialist',
            'GCP Specialist',
            'Cybersecurity Analyst',
            'Network Engineer',
            'System Administrator',
            'Linux Administrator',
            'Windows Administrator',
            'Database Administrator (DBA)',
            'Data Scientist',
            'Data Analyst',
            'Big Data Engineer',
            'Machine Learning Engineer',
            'AI Engineer',
            'UI/UX Designer',
            'Graphic Designer (IT)',
            'Software Tester (QA Engineer)',
            'Business Analyst (IT)',
            'Technical Writer',
            'IT Project Manager',
            'Scrum Master',
            'Enterprise Architect',
            'Solution Architect',
            'System Analyst',
            'IT Consultant',
        ];
    
    foreach ($listBidangKeahlianTI as $bidangKeahlian) {
            DB::table('m_bidang_keahlian')->insert([
                'nama' => $bidangKeahlian
            ]);
        }
}
}
