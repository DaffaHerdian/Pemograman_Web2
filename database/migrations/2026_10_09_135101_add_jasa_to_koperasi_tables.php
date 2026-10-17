<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaturan_koperasi', function (Blueprint $table): void {
            $table->unsignedInteger('id')->primary();
            $table->decimal('jasa_simpanan_persen', 5, 2);
            $table->decimal('jasa_pinjaman_persen', 5, 2);
            $table->string('periode_mulai', 7);
            $table->string('periode_terakhir', 7)->nullable();
        });
        DB::table('pengaturan_koperasi')->insert([
            'id' => 1, 'jasa_simpanan_persen' => '0.25', 'jasa_pinjaman_persen' => '1.00',
            'periode_mulai' => '2026-10', 'periode_terakhir' => null,
        ]);
        Schema::table('simpanan', function (Blueprint $table): void {
            $table->string('periode_jasa', 7)->nullable();
            $table->decimal('tarif_jasa', 5, 2)->nullable();
            $table->unique(['id_anggota', 'periode_jasa']);
        });
        Schema::table('pinjaman', function (Blueprint $table): void {
            $table->decimal('tarif_jasa', 5, 2)->default(0);
            $table->decimal('total_jasa', 15, 2)->default(0);
            $table->decimal('sisa_jasa', 15, 2)->default(0);
        });
        Schema::table('angsuran', function (Blueprint $table): void {
            $table->decimal('jasa_bayar', 15, 2)->default(0);
            $table->decimal('sisa_jasa', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('angsuran', fn (Blueprint $table) => $table->dropColumn(['jasa_bayar', 'sisa_jasa']));
        Schema::table('pinjaman', fn (Blueprint $table) => $table->dropColumn(['tarif_jasa', 'total_jasa', 'sisa_jasa']));
        Schema::table('simpanan', function (Blueprint $table): void {
            $table->dropUnique(['id_anggota', 'periode_jasa']);
            $table->dropColumn(['periode_jasa', 'tarif_jasa']);
        });
        Schema::dropIfExists('pengaturan_koperasi');
    }
};
