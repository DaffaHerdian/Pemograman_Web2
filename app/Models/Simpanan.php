<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Simpanan extends Model
{
    protected $table = 'simpanan';

    protected $primaryKey = 'id_simpanan';

    protected $fillable = [
        'id_anggota',
        'tanggal',
        'jenis_simpanan',
        'jumlah', 'periode_jasa', 'tarif_jasa',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2', 'tarif_jasa' => 'decimal:2',
    ];

    public function getJenisTransaksiAttribute(): string
    {
        return $this->jumlah < 0 ? 'Penarikan' : 'Setoran';
    }

    // Relationships
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }
}
