<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdiModel extends Model
{
    use HasFactory;

    protected $table = 'm_program_studi';
    protected $primaryKey = 'prodi_id';
    public $timestamps = false;

    protected $fillable = [
        'nama_prodi',
        'jurusan',
    ];

    // Relasi ke mahasiswa (satu prodi memiliki banyak mahasiswa)
    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaModel::class, 'prodi_id');
    }
}
