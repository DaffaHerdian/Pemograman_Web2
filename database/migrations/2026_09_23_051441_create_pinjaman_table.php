<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->id('id_pinjaman');
            $table->unsignedBigInteger('id_anggota');
            $table->date('tanggal');
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->integer('tenor');
            $table->decimal('angsuran_per_bulan', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->enum('status', ['Berjalan', 'Lunas'])->default('Berjalan');
            $table->timestamps();

            $table->foreign('id_anggota')->references('id_anggota')->on('anggota')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pinjaman');
    }
};
