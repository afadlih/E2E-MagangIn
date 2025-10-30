<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KabupatenModel extends Model
{
    use HasFactory;

    protected $table = 'm_kabupaten';

    public function provinsi()
    {
        return $this->belongsTo(ProvinsiModel::class, 'provinsi_id');
    }

    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaModel::class, 'kabupaten_id');
    }
}