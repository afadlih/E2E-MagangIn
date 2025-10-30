<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LamaranMagangModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 't_lamaran_magang';
    protected $primaryKey = 'lamaran_id';
    public $timestamps = false;
    protected $dates = ['deleted_at']; // Ensure deleted_at is treated as a date
    protected $fillable = [
        'mhs_nim',
        'lowongan_id',
        'tanggal_lamaran',
        'status',
        'dosen_id'
    ];

    protected $casts = [
        'tanggal_lamaran' => 'datetime',
    ];

    /**
     * Relasi ke dokumen-dokumen (d_dokumen)
     */
    public function dokumen()
    {
        return $this->hasMany(DokumenModel::class, 'lamaran_id', 'lamaran_id');
    }

    public function lowongan()
    {
        return $this->belongsTo(LowonganModel::class, 'lowongan_id', 'lowongan_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(NotifikasiModel::class, 'lamaran_id', 'lamaran_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function dosen()
    {
        return $this->belongsTo(
            DosenModel::class, 'dosen_id', 'dosen_id'
        );
    }

    public function feedback()
    {
        return $this->hasOne(FeedbackModel::class, 'lamaran_id', 'lamaran_id');
    }


}
