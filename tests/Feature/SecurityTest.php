<?php

namespace Tests\Feature;

use App\Models\User;
use App\Payments\SimulationProvider;
use App\Services\Finance;
use App\Services\ReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class SecurityTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'Private Admin QA', 'email' => 'security@example.test', 'password' => bin2hex(random_bytes(24))]);
    }

    public function test_backup_requires_auth_and_current_password_and_private_files_are_not_routed(): void
    {
        $this->get('/backup')->assertRedirect('/');
        $this->post('/backup')->assertRedirect('/');
        $this->get('/storage/backups/fake.sql')->assertNotFound();
        $this->actingAs($this->admin());
        $this->postJson('/backup', ['confirm' => '1', 'password' => 'incorrect'])->assertUnprocessable()->assertJsonValidationErrors('password');
    }

    public function test_receipt_cancellation_is_public_but_pii_is_not(): void
    {
        $admin = $this->admin();
        $f = new Finance;
        $date = now('Asia/Jakarta')->toDateString();
        $f->opening('0', $date, 'Saldo nol hasil hitung', (string) Str::uuid(), $admin->id);
        $member = DB::table('anggota')->insertGetId(['nama' => 'Private Member QA', 'nik' => '0000000000000001', 'telepon' => '0123', 'jenis_kelamin' => 'Perempuan', 'alamat' => 'Private address QA', 'tanggal_bergabung' => $date, 'status' => 'Aktif']);
        $tx = $f->savings(['id_anggota' => $member, 'tanggal' => $date, 'jumlah' => '1000', 'jenis_simpanan' => 'Sukarela', 'idempotency_key' => (string) Str::uuid()], $admin->id);
        $receipt = (new ReceiptService)->ensure($tx->id);
        $token = Crypt::decryptString($receipt->token_encrypted);
        $this->get('/verifikasi/'.$token)->assertOk()->assertSee('VALID')->assertDontSee('Private Member QA')->assertDontSee('Private address QA')->assertDontSee($admin->name);
        $f->reverse($tx->id, 'Salah input saat uji', $date, (string) Str::uuid(), $admin->id);
        $this->get('/verifikasi/'.$token)->assertOk()->assertSee('DIBATALKAN');
        $this->assertSame(1, DB::table('simpanan')->where('transaction_id', $tx->id)->count());
        $this->get('/verifikasi/'.str_repeat('0', 64))->assertNotFound();
    }

    public function test_payment_simulation_cannot_post_money_and_webhook_fails_closed(): void
    {
        $this->actingAs($this->admin());
        $res = $this->postJson('/pembayaran-sandbox/simulasi', ['amount' => 1000]);
        // Testing environment is deliberately not allowed to impersonate a local operator.
        $res->assertForbidden();
        $this->postJson('/payments/webhook', ['status' => 'success', 'amount' => 1000])->assertStatus(503);
        $this->assertSame(0, DB::table('financial_transactions')->count());
        $this->assertSame(0, DB::table('ledger_entries')->count());
        $provider = new SimulationProvider;
        $this->assertFalse($provider->verify('{}', 'forged'));
    }

    public function test_public_verification_and_login_are_rate_limited(): void
    {
        for ($n = 0; $n < 31; $n++) {
            $response = $this->get('/verifikasi/'.str_repeat('0', 64));
        }$response->assertStatus(429);
        for ($n = 0; $n < 6; $n++) {
            $response = $this->post('/login', ['email' => 'invalid@example.test', 'password' => 'invalid']);
        }$response->assertStatus(429);
    }

    public function test_csrf_middleware_protects_financial_post_in_real_environment(): void
    {
        $this->actingAs($this->admin());
        $this->app->instance('env', 'local');
        $this->post('/keuangan/pembukaan', ['amount' => 0])->assertStatus(419);
    }

    public function test_settings_reject_svg_and_unconfirmed_money_rules(): void
    {
        $this->actingAs($this->admin());
        $d = ['name' => 'QA', 'rate' => '1', 'max_principal' => 10000000, 'max_months' => 36];
        $this->postJson('/pengaturan', $d)->assertUnprocessable();
        $d['confirm_policy'] = '1';
        $d['logo'] = UploadedFile::fake()->create('malicious.svg', 1, 'image/svg+xml');
        $this->postJson('/pengaturan', $d)->assertUnprocessable()->assertJsonValidationErrors('logo');
    }

    public function test_financial_posting_failure_rolls_back_receipt_ledger_and_audit(): void
    {
        $admin = $this->admin();
        $f = new Finance;
        try {
            $f->opening('0', now('Asia/Jakarta')->toDateString(), 'short', (string) Str::uuid(), $admin->id);
            $this->fail('Invalid reason accepted');
        } catch (ValidationException) {
        }
        foreach (['financial_transactions', 'ledger_entries', 'receipts', 'activity_logs'] as $t) {
            $this->assertSame(0, DB::table($t)->count());
        }
    }
}
