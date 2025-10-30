<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LowonganModel extends Model
{
    use HasFactory;

    protected $table = 't_lowongan_magang';
    protected $primaryKey = 'lowongan_id';
    public $timestamps = false;

    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal_mulai_magang',
        'deadline_lowongan',
        'lokasi',
        'perusahaan_id',
        'periode_id',
        'sylabus_path',
        'status',   
        'kuota', 
        'durasi',
        'tipe_bekerja', 
    ];

    /**
     * Cast tanggal fields to Carbon instances
     */
    protected $casts = [
        'tanggal_mulai_magang' => 'date',
        'deadline_lowongan'    => 'date',
        'status'               => 'string',
        'kuota'                => 'integer',
        'tipe_bekerja'  => 'string',
    ];

    // Relasi ke perusahaan
    public function perusahaan()
    {
        return $this->belongsTo(PerusahaanModel::class, 'perusahaan_id');
    }

    public function lamaran()
    {
        return $this->hasMany(LamaranMagangModel::class, 'lowongan_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodeMagangModel::class, 'periode_id', 'periode_id');
    }

    public function mahasiswa()
    {
        return $this->belongsToMany(MahasiswaModel::class, 't_lamaran_magang', 'lowongan_id', 'mhs_nim');
    }

    public function feedback()
    {
        return $this->hasMany(FeedbackModel::class, 'target_id', 'lowongan_id')
                    ->where('target_type', 'lowongan');
    }

        public function provinsi()
    {
        return $this->belongsTo(\App\Models\ProvinsiModel::class, 'lokasi', 'id');
    }
}
