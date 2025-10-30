<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable; // implementasi class Authenticatable

class UserModel extends Authenticatable
{
    use HasFactory;

    protected $table = 'm_users';
    protected $primaryKey = 'user_id';
    protected $fillable = ['username', 'password', 'level_id'];

    protected $hidden = ['password']; // jangan di tampilkan saat select

    protected $casts = ['password' => 'hashed']; // casting password agar otomatis di hash
    public $timestamps = false;


    /**
     * Relasi ke tabel level
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(LevelModel::class, 'level_id', 'level_id');
    }

    public function getRoleName():string
    {
        return $this->level->level_name;
    }

    public function hasRole($role): bool
    {
        return $this->level->level_name == $role;
    }

    public function mahasiswa()
    {
        return $this->hasOne(MahasiswaModel::class, 'user_id');
    }

    public function dosen()
    {
        return $this->hasOne(DosenModel::class, 'user_id');
    }

    public function admin()
    {
        return $this->hasOne(AdminModel::class, 'user_id');
    }

}
