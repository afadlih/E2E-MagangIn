<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
                'user_id' => 1,
                'nama'    => 'Admin Sistem',
                'email'   => 'admin@gmail.com',
                'telp'    => '08123456789',
            ];
            DB::table('m_admin')->insert($data);
        }
    }

