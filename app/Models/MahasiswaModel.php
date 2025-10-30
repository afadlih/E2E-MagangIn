<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaModel extends Model
{
    use HasFactory;

    protected $table      = 'm_mahasiswa';
    protected $primaryKey = 'mhs_nim';
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = false;

    protected $fillable = [
        'mhs_nim',
        'user_id',
        'full_name',
        'alamat',
        'telp',
        'prodi_id',
        'angkatan',
        'jenis_kelamin',
        'ipk',
        'file_cv',
        'provinsi',
        'kabupaten',
        'status_magang',
        'profile_picture',
        'bidang_keahlian_id',

        // <<-- tambahkan preferensi di sini
        'pref',           // preferensi kerja
        'skill',          // keahlian
        'lokasi',         // lokasi favorit
        'durasi',         // periode magang (bulan)
        'tipe_bekerja', 
    ];

    protected $casts = [
        'mhs_nim'       => 'string',
        'user_id'       => 'integer',
        'lokasi'        => 'string',
        'durasi'        => 'integer',
        'status_magang' => 'string',
        'tipe_bekerja'  => 'string',
        'ipk'          => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function level() {
        return $this->hasOneThrough(LevelModel::class, UserModel::class, 'user_id', 'level_id', 'user_id', 'level_id');
    }

    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id', 'prodi_id');
    }

    
    public function lowongan()
    {
        return $this->belongsToMany(LowonganModel::class, 't_lamaran_magang', 'mhs_nim', 'lowongan_id');
    }

    public function feedback()
    {
        return $this->hasMany(FeedbackModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function lamaran()
    {
        return $this->hasMany(LamaranMagangModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function dosen()
    {
        return $this->belongsTo(DosenModel::class, 'dosen_id', 'dosen_id');
    }

    public function bidangKeahlian()
    {
        return $this->belongsToMany(
            BidangKeahlianModel::class,
            't_minat_mahasiswa',
            'mhs_nim',                // foreign key di pivot ke Mahasiswa
            'bidang_keahlian_id',     // foreign key di pivot ke BidangKeahlian
            'mhs_nim',                // local key di MahasiswaModel
            'id'                      // local key di BidangKeahlianModel
        );
    }

    public function preferensiLokasi()
    {
        return $this->hasOne(PrefrensiLokasiMahasiswaModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function getGenderNameAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    public function getAllCorPrefrensiLokasi()
    {
        return $this->prefrensiLokasi->map(function ($item) {
            return [
                'nama' => $item->nama_tampilan,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
            ];
        })->toArray();
    }

    public function desa()
    {
        return $this->belongsTo(DesaModel::class, 'desa_id', 'desa_id');
    }
    public function kecamatan()
    {
        return $this->belongsTo(KecamatanModel::class, 'kecamatan   _id', 'kecamatan_id');
    }
    public function kabupaten()
    {
        return $this->belongsTo(KabupatenModel::class, 'kabupaten_id', 'kabupaten_id');
    }
    public function provinsi()
    {
        return $this->belongsTo(ProvinsiModel::class, 'provinsi_id', 'provinsi_id');
    }

    public function provinsipref()
    {
        return $this->belongsTo(ProvinsiModel::class, 'lokasi', 'id');
    }

    public function negara()
    {
        return $this->belongsTo(NegaraModel::class, 'negara_id', 'id');
    }


    public function minat()
    {
        return $this->hasMany(MinatMahasiswaModel::class, 'mhs_nim', 'mhs_nim');
    }

    public function skills()
{
    return $this->belongsToMany(
        SkillModel::class,
        'mahasiswa_skill',
        'mhs_nim',
        'skill_id',
        'mhs_nim',
        'id'
    );
}
}