<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cooperative_identity', function (Blueprint $t) {
            $t->unsignedInteger('id')->primary();
            $t->string('name');
            $t->text('address')->nullable();
            $t->string('telephone')->nullable();
            $t->string('logo_path')->nullable();
        });
        DB::table('cooperative_identity')->insert(['id' => 1, 'name' => 'KoperasiKu']);
        Schema::create('receipts', function (Blueprint $t) {
            $t->id();
            $t->string('transaction_id', 26)->unique();
            $t->foreign('transaction_id')->references('id')->on('financial_transactions')->restrictOnDelete();
            $t->string('number')->unique();
            $t->string('token_hash', 64)->unique();
            $t->text('token_encrypted');
            $t->timestamp('created_at');
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Receipt history is protected. Restore verified backup separately.');
    }
};
