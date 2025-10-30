<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            AuthLevelSeeder::class,
            UserSeeder::class,
            ProgramStudiSeeder::class,
            MahasiswaSeeder::class,
            DosenSeeder::class,
            AdminSeeder::class,
            
            PerusahaanMitraSeeder::class,
            PeriodeMagangSeeder::class,

            WilayahSeeder::class,

            LowonganMagangSeeder::class,
            LamaranMagangSeeder::class,

            LogAktivitasMhsSeeder::class,
            // FeedbackSeeder::class,

            BidangPenelitianSeeder::class,
            BidangKeahlianSeeder::class,

            // PrefrensiLokasiMahasiswaSeeder::class,

            SkillsTableSeeder::class,
        ]);
            
    }
}