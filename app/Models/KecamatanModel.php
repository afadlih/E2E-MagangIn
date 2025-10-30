<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KecamatanModel extends Model
{
    use HasFactory;

    protected $table = 'm_kecamatan';

    public function kabupaten()
    {
        return $this->belongsTo(KabupatenModel::class, 'kabupaten_id');
    }

    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaModel::class, 'kecamatan_id');
    }
}