<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesaModel extends Model
{
    use HasFactory;

    protected $table = 'm_desa';

    public function kecamatan()
    {
        return $this->belongsTo(KecamatanModel::class, 'kecamatan_id');
    }
    
    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaModel::class, 'desa_id');
    }
}