<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerusahaanMitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama'   => 'PT Teknologi Nusantara',
                'alamat' => 'Jl. Merdeka No. 88, Jakarta',
                'email'  => 'info@teknologinusantara.co.id',
                'telp'   => '021-5551234',
            ],
            [
                'nama'   => 'CV Solusi Digital',
                'alamat' => 'Jl. Taman Siswa No. 12, Yogyakarta',
                'email'  => 'contact@solusidigital.co.id',
                'telp'   => '0274-789456',
            ],
            [
                'nama'   => 'PT Kreatif Media',
                'alamat' => 'Jl. Gatot Subroto Kav. 45, Bandung',
                'email'  => 'hr@kreatifmedia.co.id',
                'telp'   => '022-7312345',
            ],
            [
                'nama'   => 'PT E-Commerce Indonesia',
                'alamat' => 'Jl. Hayam Wuruk No. 101, Surabaya',
                'email'  => 'careers@ecommerceindonesia.co.id',
                'telp'   => '031-8912345',
            ],
            [
                'nama'   => 'CV Data Cerdas',
                'alamat' => 'Jl. Diponegoro No. 23, Semarang',
                'email'  => 'recruitment@datacerdas.co.id',
                'telp'   => '024-7643210',
            ],
            [
                'nama'   => 'PT Mobile Mart',
                'alamat' => 'Jl. Sudirman No. 77, Medan',
                'email'  => 'info@mobilemart.co.id',
                'telp'   => '061-1234567',
            ],
            [
                'nama'   => 'PT AI Inovasi',
                'alamat' => 'Jl. Dipatiukur No. 15, Bandung',
                'email'  => 'hello@aiinovasi.co.id',
                'telp'   => '022-7654321',
            ],
            [
                'nama'   => 'CV Desain Grafis',
                'alamat' => 'Jl. Ahmad Yani No. 9, Yogyakarta',
                'email'  => 'contact@desaingrafis.co.id',
                'telp'   => '0274-654321',
            ],
            [
                'nama'   => 'PT Riset Tekno',
                'alamat' => 'Jl. Thamrin No. 56, Jakarta Pusat',
                'email'  => 'research@ristekno.co.id',
                'telp'   => '021-6664321',
            ],
            [
                'nama'   => 'CV Cloud Services',
                'alamat' => 'Jl. Kajaolalido No. 8, Makassar',
                'email'  => 'support@cloudservices.co.id',
                'telp'   => '0411-123987',
            ],
            [
                'nama'   => 'PT Automasi QA',
                'alamat' => 'Jl. MH. Thamrin No. 100, Jakarta',
                'email'  => 'hr@automasiqa.co.id',
                'telp'   => '021-7771234',
            ],
            [
                'nama'   => 'PT Blockchain Nusantara',
                'alamat' => 'Jl. Sudirman No. 55, Jakarta Selatan',
                'email'  => 'info@blockchain.co.id',
                'telp'   => '021-8885678',
            ],
            [
                'nama'   => 'PT Game Studio',
                'alamat' => 'Jl. Pahlawan No. 10, Surabaya',
                'email'  => 'games@studiosoftware.co.id',
                'telp'   => '031-9992345',
            ],
            [
                'nama'   => 'PT Digital Marketing',
                'alamat' => 'Jl. Kalimantan No. 33, Banjarmasin',
                'email'  => 'marketing@digitalco.id',
                'telp'   => '0511-321987',
            ],
            [
                'nama'   => 'PT Business Development Indonesia',
                'alamat' => 'Jl. Gajah Mada No. 45, Yogyakarta',
                'email'  => 'bd@businessdev.co.id',
                'telp'   => '0274-123890',
            ],
        ];

        DB::table('m_perusahaan_mitra')->insert($data);
    }
}
