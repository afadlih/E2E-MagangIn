<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillModel extends Model
{
    protected $table = 'skills';
    protected $fillable = ['nama'];

    public function mahasiswas()
    {
        return $this->belongsToMany(
            MahasiswaModel::class,
            'mahasiswa_skill',
            'skill_id',
            'mhs_nim',
            'id',
            'mhs_nim'
        );
    }
}
