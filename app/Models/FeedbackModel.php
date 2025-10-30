<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackModel extends Model
{
    use HasFactory;

    protected $table = 't_feedback';
    protected $primaryKey = 'feedback_id';
    public $timestamps = false;

    protected $fillable = [
        'mhs_nim',
        'target_type',
        'lowongan_id',
        'rating',
        'komentar',
        'created_at',
        'lamaran_id'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function lowongan()
    {
        return $this->belongsTo(LowonganModel::class, 'lowongan_id', 'lowongan_id');
    }

    public function lamaran()
    {
        return $this->belongsTo(LamaranMagangModel::class, 'lamaran_id', 'lamaran_id');
    }

}
