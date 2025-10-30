<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    use HasFactory;

    protected $table = 'r_auth_level';
    protected $primaryKey = 'level_id';
    public $timestamps = false;

    protected $fillable = [
        'level_name',
        'description',
    ];

    /**
     * Relasi ke MUser (satu level bisa punya banyak user)
     */
    public function users()
    {
        return $this->hasMany(UserModel::class, 'level_id', 'level_id');
    }
}
