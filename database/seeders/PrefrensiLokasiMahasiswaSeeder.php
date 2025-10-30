<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrefrensiLokasiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listProvinsiID = DB::table('m_provinsi')->pluck('id');
        $listMahasiswaNIM = DB::table('m_mahasiswa')->pluck('mhs_nim');

        foreach ($listMahasiswaNIM as $nim) {
            for ($i = 0; $i < 1; $i++) { // langsung pakai angka (misal: 2 preferensi lokasi per mahasiswa)
                $provinsi = DB::table('m_provinsi')
                    ->select('id', 'nama')
                    ->where('id', $listProvinsiID->random())
                    ->first();

                $kabupaten = DB::table('m_kabupaten')
                    ->select('id', 'provinsi_id', 'nama')
                    ->where('provinsi_id', $provinsi->id)
                    ->inRandomOrder()
                    ->first();

                $kecamatan = DB::table('m_kecamatan')
                    ->select('id', 'kabupaten_id', 'nama', 'longitude', 'latitude')
                    ->where('kabupaten_id', $kabupaten->id)
                    ->inRandomOrder()
                    ->first();

                $desa = DB::table('m_desa')
                    ->select('id', 'kecamatan_id', 'nama')
                    ->where('kecamatan_id', $kecamatan->id)
                    ->inRandomOrder()
                    ->first();

                DB::table('t_prefrensi_lokasi_mahasiswa')->insert([
                    'mhs_nim'         => $nim,
                    'negara_id'       => 1,
                    'provinsi_id'     => $provinsi->id,
                    'kabupaten_id'    => $kabupaten->id,
                    'kecamatan_id'    => $kecamatan->id,
                    'desa_id'         => $desa->id,
                    'longitude'       => $kecamatan->longitude,
                    'latitude'        => $kecamatan->latitude,
                    'nama_tampilan'   => "{$desa->nama}, {$kecamatan->nama}, {$kabupaten->nama}, {$provinsi->nama}, INDONESIA"
                ]);
            }
        }
    }
}
