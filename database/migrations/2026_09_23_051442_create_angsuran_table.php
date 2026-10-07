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
        Schema::create('angsuran', function (Blueprint $table) {
            $table->id('id_angsuran');
            $table->unsignedBigInteger('id_pinjaman');
            $table->date('tanggal');
            $table->integer('angsuran_ke');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->timestamps();

            $table->foreign('id_pinjaman')->references('id_pinjaman')->on('pinjaman')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('angsuran');
    }
};
