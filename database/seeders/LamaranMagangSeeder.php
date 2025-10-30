<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LamaranMagangSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['lamaran_id' => 1, 'dosen_id' => 1, 'mhs_nim' => '2341720226', 'lowongan_id' => 1, 'tanggal_lamaran' => '2025-06-01 08:00:00', 'status' => 'diterima'],
            ['lamaran_id' => 2, 'dosen_id' => 1, 'mhs_nim' => '2341720227', 'lowongan_id' => 2, 'tanggal_lamaran' => '2025-06-02 09:00:00', 'status' => 'diterima'],
            ['lamaran_id' => 3, 'dosen_id' => 3, 'mhs_nim' => '2341720228', 'lowongan_id' => 3, 'tanggal_lamaran' => '2025-06-03 10:00:00', 'status' => 'diterima'],
            ['lamaran_id' => 4, 'dosen_id' => 4, 'mhs_nim' => '2341720229', 'lowongan_id' => 4, 'tanggal_lamaran' => '2025-06-04 11:00:00', 'status' => 'diterima'],
            ['lamaran_id' => 5, 'dosen_id' => 1, 'mhs_nim' => '2341720230', 'lowongan_id' => 5, 'tanggal_lamaran' => '2025-06-05 13:00:00', 'status' => 'diterima'],
            ['lamaran_id' => 6, 'dosen_id' => 6, 'mhs_nim' => '2341720231', 'lowongan_id' => 6, 'tanggal_lamaran' => '2025-06-06 14:00:00', 'status' => 'diterima'],
            ['lamaran_id' => 7, 'dosen_id' => 7, 'mhs_nim' => '2341720232', 'lowongan_id' => 7, 'tanggal_lamaran' => '2025-06-07 15:00:00', 'status' => 'diterima'],
            ['lamaran_id' => 8, 'dosen_id' => 1, 'mhs_nim' => '2341720233', 'lowongan_id' => 8, 'tanggal_lamaran' => '2025-06-08 10:00:00', 'status' => 'diterima'],
            ['lamaran_id' => 9, 'dosen_id' => 9, 'mhs_nim' => '2341720234', 'lowongan_id' => 9, 'tanggal_lamaran' => '2025-06-09 09:00:00', 'status' => 'diterima'],
            ['lamaran_id' => 10, 'dosen_id' => 10, 'mhs_nim' => '2341720235', 'lowongan_id' => 10, 'tanggal_lamaran' => '2025-06-10 08:00:00', 'status' => 'diterima'],
        ];

        foreach ($data as $item) {
            DB::table('t_lamaran_magang')->insert([
                'lamaran_id' => $item['lamaran_id'],
                'dosen_id' => $item['dosen_id'],
                'mhs_nim' => $item['mhs_nim'],
                'lowongan_id' => $item['lowongan_id'],
                'tanggal_lamaran' => $item['tanggal_lamaran'],
                'status' => $item['status'],
                'deleted_at' => null,
            ]);
        }
    }
}