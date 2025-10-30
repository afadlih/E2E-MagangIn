<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiModel extends Model
{
    use HasFactory;

    protected $table = 't_notifikasi';
    protected $primaryKey = 'notifikasi_id';
    public $timestamps = false;

    protected $fillable = [
        'lamaran_id',
        'judul',
        'pesan',
        'waktu_dibuat',
        'status_baca',
        'mhs_nim'
    ];

    public function lamaran()
    {
        return $this->belongsTo(LamaranMagangModel::class, 'lamaran_id', 'lamaran_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mhs_nim', 'mhs_nim');
    }
}
