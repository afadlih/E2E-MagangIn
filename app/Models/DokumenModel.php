<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenModel extends Model
{
    use HasFactory;
    protected $table = 'd_dokumen';
    protected $primaryKey = 'dokumen_id';
    public $timestamps = false;

    protected $fillable = [
        'lamaran_id',
        'jenis',
        'nama_file',
        'ukuran',
        'path',
        'uploaded_at'
    ];

    /**
     * Relasi ke lamaran magang
     */
    public function lamaran()
    {
        return $this->belongsTo(LamaranMagangModel::class, 'lamaran_id', 'lamaran_id');
    }
}
