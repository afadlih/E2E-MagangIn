<?php
// app/Models/PerusahaanModel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerusahaanModel extends Model
{
    use HasFactory;

    protected $table = 'm_perusahaan_mitra';  
    protected $primaryKey = 'perusahaan_id';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'alamat',
        'email',
        'telp',
    ];

    public function lowongan()
    {
        return $this->hasMany(LowonganModel::class, 'perusahaan_id');
    }
}
