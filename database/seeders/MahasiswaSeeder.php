<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
        {
            $data = [
                [
                    'mhs_nim'        => '2341720226',
                    'user_id'        => 12,
                    'full_name'      => 'Ramadhani Bi Hayyin',
                    'alamat'         => 'Jl. Kembang Kertas',
                    'telp'           => '081333537649',
                    'prodi_id'       => 1,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'L',
                    'ipk'            => 3.5,
                    'status_magang'  => 'Belum Magang',
                ],
                [
                    'mhs_nim'        => '2341720227',
                    'user_id'        => 13,
                    'full_name'      => 'Muhammad Rizky',
                    'alamat'         => 'Jl. Melati Indah',
                    'telp'           => '08123456789',
                    'prodi_id'       => 1,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'L',
                    'ipk'            => 3.2,
                    'status_magang'  => 'Belum Magang',
                ],
                [
                    'mhs_nim'        => '2341720228',
                    'user_id'        => 14,
                    'full_name'      => 'Muhammad Fauzan',
                    'alamat'         => 'Jl. Mawar Biru',
                    'telp'           => '08123456788',
                    'prodi_id'       => 1,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'L',
                    'ipk'            => 3.8,
                    'status_magang'  => 'Belum Magang',
                ],
                [
                    'mhs_nim'        => '2341720229',
                    'user_id'        => 15,
                    'full_name'      => 'Aisyah Putri',
                    'alamat'         => 'Jl. Anggrek No. 5',
                    'telp'           => '08125678900',
                    'prodi_id'       => 2,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'P',
                    'ipk'            => 3.7,
                    'status_magang'  => 'Belum Magang',
                ],
                [
                    'mhs_nim'        => '2341720230',
                    'user_id'        => 16,
                    'full_name'      => 'Zahra Nabila',
                    'alamat'         => 'Jl. Seruni Indah',
                    'telp'           => '081278945612',
                    'prodi_id'       => 2,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'P',
                    'ipk'            => 3.6,
                    'status_magang'  => 'Belum Magang',
                ],
                [
                    'mhs_nim'        => '2341720231',
                    'user_id'        => 17,
                    'full_name'      => 'Fadlan Maulana',
                    'alamat'         => 'Jl. Dahlia 7',
                    'telp'           => '081298765432',
                    'prodi_id'       => 1,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'L',
                    'ipk'            => 3.1,
                    'status_magang'  => 'Belum Magang',
                ],
                [
                    'mhs_nim'        => '2341720232',
                    'user_id'        => 18,
                    'full_name'      => 'Rina Safitri',
                    'alamat'         => 'Jl. Kenanga Blok C',
                    'telp'           => '081245678910',
                    'prodi_id'       => 2,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'P',
                    'ipk'            => 3.4,
                    'status_magang'  => 'Belum Magang',
                ],
                [
                    'mhs_nim'        => '2341720233',
                    'user_id'        => 19,
                    'full_name'      => 'Ilham Pratama',
                    'alamat'         => 'Jl. Cemara Hijau',
                    'telp'           => '081298761234',
                    'prodi_id'       => 1,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'L',
                    'ipk'            => 3.3,
                    'status_magang'  => 'Belum Magang',
                ],
                [
                    'mhs_nim'        => '2341720234',
                    'user_id'        => 20,
                    'full_name'      => 'Dewi Anggraini',
                    'alamat'         => 'Jl. Taman Siswa',
                    'telp'           => '081276543210',
                    'prodi_id'       => 2,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'P',
                    'ipk'            => 3.9,
                    'status_magang'  => 'Belum Magang',
                ],
                [
                    'mhs_nim'        => '2341720235',
                    'user_id'        => 21,
                    'full_name'      => 'Reza Kurniawan',
                    'alamat'         => 'Jl. Teratai Merah',
                    'telp'           => '081267890123',
                    'prodi_id'       => 1,
                    'angkatan'       => '2023',
                    'jenis_kelamin'  => 'L',
                    'ipk'            => 3.0,
                    'status_magang'  => 'Belum Magang',
                ],
            ];

            DB::table('m_mahasiswa')->insert($data);
        }
    }

