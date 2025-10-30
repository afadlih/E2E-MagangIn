<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NegaraModel extends Model
{
    use HasFactory;

    protected $table = 'm_negara';

    protected $fillable = [
        'nama',
        'kode',
    ];

    public function lokasiMahasiswa()
    {
        return $this->hasMany(PrefrensiLokasiMahasiswaModel::class, 'negara_id', 'id');
    }
}
