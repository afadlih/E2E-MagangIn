<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogAktivitasMhsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'lamaran_id' => 1,
                'keterangan' => 'Mahasiswa mengirim lamaran magang',
                'waktu' => now()->subDays(13)->setTime(9, 0, 0),
            ],
            [
                'lamaran_id' => 1,
                'keterangan' => 'Mahasiswa diterima untuk magang di perusahaan',
                'waktu' => now()->subDays(9)->setTime(10, 0, 0),
            ],
            [
                'lamaran_id' => 1,
                'keterangan' => 'Hari pertama magang: Orientasi tim IT',
                'waktu' => now()->subDays(4)->setTime(9, 30, 0),
            ],
            [
                'lamaran_id' => 1,
                'keterangan' => 'Membantu setup database untuk proyek internal',
                'waktu' => now()->subDays(3)->setTime(10, 15, 0),
            ],
            [
                'lamaran_id' => 1,
                'keterangan' => 'Mengikuti pelatihan penggunaan API perusahaan',
                'waktu' => now()->subDays(2)->setTime(13, 0, 0),
            ],
            [
                'lamaran_id' => 1,
                'keterangan' => 'Membantu debugging kode aplikasi web',
                'waktu' => now()->subDay()->setTime(14, 20, 0),
            ],
            [
                'lamaran_id' => 1,
                'keterangan' => 'Membuat laporan harian perkembangan proyek',
                'waktu' => now()->setTime(11, 0, 0),
            ],
            [
                'lamaran_id' => 1,
                'keterangan' => 'Mengikuti meeting dengan tim developer',
                'waktu' => now()->addDay()->setTime(9, 30, 0),
            ],
            [
                'lamaran_id' => 1,
                'keterangan' => 'Menguji fitur baru pada aplikasi mobile',
                'waktu' => now()->addDays(2)->setTime(15, 10, 0),
            ],
            [
                'lamaran_id' => 1,
                'keterangan' => 'Membantu update dokumentasi teknis sistem',
                'waktu' => now()->addDays(3)->setTime(10, 0, 0),
            ],
        ];

        DB::table('t_log_aktivitas_mhs')->insert($data);
    }
}
