<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BidangKeahlianModel extends Model
{
    use HasFactory;

    protected $table = 'm_bidang_keahlian';

    protected $fillable = [
        'nama',
    ];

    public function minat()
    {
        return $this->hasMany(MinatMahasiswaModel::class, 'bidang_keahlian_id');
    }

    public function mahasiswa()
{
    return $this->belongsToMany(
        MahasiswaModel::class,
        't_minat_mahasiswa',
        'bidang_keahlian_id',
        'mhs_nim',
        'id',
        'mhs_nim'
    );
}
 
}
