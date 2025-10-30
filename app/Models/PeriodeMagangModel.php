<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeMagangModel extends Model
{
    use HasFactory;

    protected $table = 'm_periode_magang';
    protected $primaryKey = 'periode_id';
    public $timestamps = false;

    protected $fillable = [
        'periode',
        'keterangan',
    ];

    // Relasi one-to-many ke Lowongan
    public function lowongan()
    {
        return $this->hasMany(LowonganModel::class, 'periode_id', 'periode_id');
    }
}