<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data =[
            [
                'username' => 'admin',
                'password' => Hash::make('12345'),
                'level_id' => 1, // Admin
            ],
            [
                'username' => 'dosen',
                'password' => Hash::make('dsn'),
                'level_id' => 2, // Dosen
            ],
            [
                'username' => 'dosen2',
                'password' => Hash::make('12345'),
                'level_id' => 2, // Dosen
            ],
             [
                'username' => 'dosen3',
                'password' => Hash::make('12345'),
                'level_id' => 2, // Dosen
            ],
             [
                'username' => 'dosen4',
                'password' => Hash::make('12345'),
                'level_id' => 2, // Dosen
            ],
             [
                'username' => 'dosen5',
                'password' => Hash::make('12345'),
                'level_id' => 2, // Dosen
            ],
             [
                'username' => 'dosen6',
                'password' => Hash::make('12345'),
                'level_id' => 2, // Dosen
            ],
             [
                'username' => 'dosen7',
                'password' => Hash::make('12345'),
                'level_id' => 2, // Dosen
            ],
             [
                'username' => 'dosen8',
                'password' => Hash::make('12345'),
                'level_id' => 2, // Dosen
            ],
             [
                'username' => 'dosen9',
                'password' => Hash::make('12345'),
                'level_id' => 2, // Dosen
            ],
             [
                'username' => 'dosen10',
                'password' => Hash::make('12345'),
                'level_id' => 2, // Dosen
            ],
            [
                'username' => 'mahasiswa',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],
            [
                'username' => 'mhs2',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],
            [
                'username' => 'mhs3',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],
            [
                'username' => 'mhs4',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],[
                'username' => 'mhs5',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],[
                'username' => 'mhs6',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],[
                'username' => 'mhs7',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],[
                'username' => 'mhs8',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],[
                'username' => 'mhs9',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],[
                'username' => 'mhs10',
                'password' => Hash::make('mhs'),
                'level_id' => 3, // Mahasiswa
            ],
        ];
            DB::table('m_users')->insert($data);
    }
}
