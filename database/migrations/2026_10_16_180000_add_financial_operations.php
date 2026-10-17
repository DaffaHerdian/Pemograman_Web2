<?php

use App\Services\LoanCalculator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_policies', function (Blueprint $t) {
            $t->id();
            $t->string('label');
            $t->json('rules');
            $t->boolean('active')->default(true);
            $t->timestamps();
        });
        DB::table('financial_policies')->insert(['label' => 'DEMO disetujui 16-10-2026', 'rules' => json_encode(LoanCalculator::DEMO), 'active' => true, 'created_at' => now(), 'updated_at' => now()]);
        Schema::create('cash_book', function (Blueprint $t) {
            $t->unsignedInteger('id')->primary();
            $t->boolean('reconciled')->default(false);
            $t->date('opening_date')->nullable();
        });
        DB::table('cash_book')->insert(['id' => 1, 'reconciled' => false]);
        Schema::table('pengaturan_koperasi', fn (Blueprint $t) => $t->boolean('jasa_simpanan_enabled')->default(false));
        Schema::table('pinjaman', function (Blueprint $t) {
            $t->foreignId('policy_id')->nullable()->constrained('financial_policies')->restrictOnDelete();
            $t->json('policy_snapshot')->nullable();
            $t->string('workflow_status')->default('legacy')->index();
            $t->text('decision_reason')->nullable();
        });
        Schema::create('financial_transactions', function (Blueprint $t) {
            $t->string('id', 26)->primary();
            $t->string('number')->unique();
            $t->uuid('idempotency_key')->unique();
            $t->string('request_hash', 64);
            $t->string('kind', 32)->index();
            $t->decimal('amount', 15, 2)->default(0);
            $t->date('date')->index();
            $t->foreignId('actor_id')->constrained('users')->restrictOnDelete();
            $t->unsignedBigInteger('member_id')->nullable();
            $t->foreign('member_id')->references('id_anggota')->on('anggota')->restrictOnDelete();
            $t->unsignedBigInteger('loan_id')->nullable();
            $t->foreign('loan_id')->references('id_pinjaman')->on('pinjaman')->restrictOnDelete();
            $t->string('reversal_of', 26)->nullable()->unique();
            $t->foreign('reversal_of')->references('id')->on('financial_transactions')->restrictOnDelete();
            $t->text('reason')->nullable();
            $t->json('meta')->nullable();
            $t->timestamps();
        });
        Schema::create('ledger_entries', function (Blueprint $t) {
            $t->id();
            $t->string('transaction_id', 26)->unique();
            $t->foreign('transaction_id')->references('id')->on('financial_transactions')->restrictOnDelete();
            $t->decimal('amount', 15, 2);
            $t->timestamps();
        });
        Schema::create('loan_schedules', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('loan_id');
            $t->foreign('loan_id')->references('id_pinjaman')->on('pinjaman')->restrictOnDelete();
            $t->unsignedInteger('number');
            $t->date('due_date')->index();
            $t->decimal('principal', 15, 2);
            $t->decimal('interest', 15, 2);
            $t->decimal('paid_principal', 15, 2)->default(0);
            $t->decimal('paid_interest', 15, 2)->default(0);
            $t->unique(['loan_id', 'number']);
        });
        Schema::create('payment_allocations', function (Blueprint $t) {
            $t->id();
            $t->string('transaction_id', 26);
            $t->foreign('transaction_id')->references('id')->on('financial_transactions')->restrictOnDelete();
            $t->foreignId('schedule_id')->constrained('loan_schedules')->restrictOnDelete();
            $t->decimal('principal', 15, 2);
            $t->decimal('interest', 15, 2);
            $t->unique(['transaction_id', 'schedule_id']);
        });
        foreach (['simpanan', 'angsuran'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('transaction_id', 26)->nullable()->unique();
                $t->foreign('transaction_id')->references('id')->on('financial_transactions')->restrictOnDelete();
            });
        }
        Schema::create('activity_logs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('actor_id')->nullable()->constrained('users')->restrictOnDelete();
            $t->string('action');
            $t->string('reference');
            $t->json('summary')->nullable();
            $t->timestamp('created_at');
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Financial history is protected. Restore a verified backup into a separate database instead.');
    }
};
