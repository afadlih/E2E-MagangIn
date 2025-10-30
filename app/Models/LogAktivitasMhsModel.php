<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitasMhsModel extends Model
{
    use HasFactory;
    protected $table = 't_log_aktivitas_mhs';
    protected $primaryKey = 'aktivitas_id';
    public $timestamps = false;

    protected $fillable = [
        'aktivitas_id',
        'lamaran_id',
        'keterangan',
        'waktu',
    ];

    protected $casts = [
        'waktu' => 'date',
    ];

    /**
     * Relasi ke komentar log aktivitas (d_komentar_log_aktivitas)
     */
    public function komentar()
    {
        return $this->hasMany(KomenLogAktivitasModel::class, 'aktivitas_id', 'aktivitas_id');
    }

    /**
     * Relasi ke lamaran magang (t_lamaran_magang)
     */
   

    public function lamaran()
    {
        return $this->belongsTo(LamaranMagangModel::class, 'lamaran_id', 'lamaran_id');
    }


}
