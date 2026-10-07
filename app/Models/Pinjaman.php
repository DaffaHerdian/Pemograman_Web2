<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjaman';
    protected $primaryKey = 'id_pinjaman';
    
    protected $fillable = [
        'id_anggota',
        'tanggal',
        'jumlah_pinjaman',
        'tenor',
        'angsuran_per_bulan',
        'sisa_pinjaman',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_pinjaman' => 'decimal:2',
        'angsuran_per_bulan' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
    ];

    // Relationships
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    public function angsuran()
    {
        return $this->hasMany(Angsuran::class, 'id_pinjaman', 'id_pinjaman');
    }
}
