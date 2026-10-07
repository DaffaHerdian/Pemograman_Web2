<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    protected $table = 'angsuran';
    protected $primaryKey = 'id_angsuran';
    
    protected $fillable = [
        'id_pinjaman',
        'tanggal',
        'angsuran_ke',
        'jumlah_bayar',
        'sisa_pinjaman'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_bayar' => 'decimal:2',
        'sisa_pinjaman' => 'decimal:2',
    ];

    // Relationships
    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'id_pinjaman', 'id_pinjaman');
    }
}
