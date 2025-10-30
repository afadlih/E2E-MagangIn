<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinatMahasiswaModel extends Model
{
    use HasFactory;

    protected $table = 't_minat_mahasiswa';

    protected $fillable = [
        'mhs_nim',
        'bidang_keahlian_id',
    ];

    public function bidangKeahlian()
    {
        return $this->belongsTo(BidangKeahlianModel::class, 'bidang_keahlian_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mhs_nim', 'mhs_nim');
    }
}
