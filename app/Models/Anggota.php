<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';
    
    protected $fillable = [
        'nama',
        'nik',
        'telepon',
        'jenis_kelamin',
        'alamat',
        'tanggal_bergabung',
        'status'
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
    ];

    // Relationships
    public function simpanan()
    {
        return $this->hasMany(Simpanan::class, 'id_anggota', 'id_anggota');
    }

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class, 'id_anggota', 'id_anggota');
    }
}
