<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvinsiModel extends Model
{
    use HasFactory;

    protected $table = 'm_provinsi';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function kabupaten()
    {
        return $this->hasMany(KabupatenModel::class, 'provinsi_id');
    }

    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaModel::class, 'provinsi_id');
    }

        public static function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) ** 2;
        return 2 * $R * asin(min(1, sqrt($a)));
    }

    public static function lokasiScore(float $distKm, float $radiusKm = 1000): float
    {
        return max(0.0, 1 - ($distKm / $radiusKm));
    }
}