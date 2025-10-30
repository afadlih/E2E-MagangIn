<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrefrensiLokasiMahasiswaModel extends Model
{
    use HasFactory;

    protected $table = 't_prefrensi_lokasi_mahasiswa';

    protected $fillable = [
        'mhs_nim',
        'negara_id',
        'kabupaten_id',
        'provinsi_id',
        'longitude',
        'latitude',
    ];

    protected $casts = [
        'longitude' => 'float',
        'latitude' => 'float',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mhs_nim', 'mhs_nim');
    }
    
    public function kabupaten()
    {
        return $this->belongsTo(KabupatenModel::class, 'kabupaten_id', 'id');
    }

    public function provinsi()
    {
        return $this->belongsTo(ProvinsiModel::class, 'provinsi_id', 'id');
    }

    public function negara()
    {
        return $this->belongsTo(NegaraModel::class, 'negara_id', 'id');
    }
}