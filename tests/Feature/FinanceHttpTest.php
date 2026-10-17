<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class FinanceHttpTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'QA', 'email' => 'http@example.test', 'password' => bin2hex(random_bytes(24))]);
    }

    public function test_guest_cannot_read_or_post_financial_operations(): void
    {
        foreach (['/keuangan', '/pengajuan', '/simulasi', '/jatuh-tempo'] as $url) {
            $this->get($url)->assertRedirect('/');
        }
        $this->post('/keuangan/pembukaan', [])->assertRedirect('/');
    }

    public function test_operational_pages_render_and_calculator_is_server_authoritative(): void
    {
        $this->actingAs($this->admin());
        foreach (['/keuangan', '/pengajuan', '/simulasi', '/jatuh-tempo', '/simpanan', '/angsuran', '/pinjaman/create'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->postJson('/simulasi', ['jumlah_pinjaman' => '6000000', 'tenor' => 12, 'tanggal' => '2026-01-31'])->assertOk()->assertJsonPath('total', '6720000')->assertJsonPath('schedule.0.due_date', '2026-02-28');
    }

    public function test_old_transaction_links_lead_to_one_loan_flow(): void
    {
        $this->actingAs($this->admin());
        $this->get('/simpanan?tab=pinjaman')->assertRedirect(route('finance.loans'));
        $this->get('/simpanan?tab=angsuran')->assertRedirect(route('angsuran.index'));
        $this->get('/pinjaman')->assertRedirect(route('finance.loans'));
        $this->get('/pembayaran-sandbox')->assertNotFound();
        $this->get('/simpanan')->assertOk()->assertDontSee('name="jumlah_pinjaman"', false)->assertDontSee('name="jumlah_bayar"', false);
        $this->get('/angsuran')->assertOk()->assertDontSee('name="jenis_simpanan"', false);
    }

    public function test_missing_idempotency_key_and_deposit_type_are_validation_errors(): void
    {
        $this->actingAs($this->admin());
        $this->postJson('/simpanan', [])->assertUnprocessable()->assertJsonValidationErrors(['idempotency_key', 'jenis_simpanan']);
    }

    public function test_admin_can_open_cash_once_and_retry_safely(): void
    {
        $this->actingAs($this->admin());
        $d = ['tanggal' => now('Asia/Jakarta')->toDateString(), 'amount' => '0.00', 'reason' => 'Kas kosong hasil verifikasi', 'confirm' => '1', 'idempotency_key' => (string) Str::uuid()];
        $this->post('/keuangan/pembukaan', $d)->assertSessionHasNoErrors();
        $this->post('/keuangan/pembukaan', $d)->assertSessionHasNoErrors();
        $this->assertSame(1, DB::table('ledger_entries')->count());
    }

    public function test_local_business_date_is_valid_across_utc_midnight_boundary(): void
    {
        $instant = CarbonImmutable::parse('2026-10-16 18:30:00', 'UTC');
        Carbon::setTestNow($instant);
        CarbonImmutable::setTestNow($instant);
        try {
            $this->actingAs($this->admin());
            $this->post('/keuangan/pembukaan', ['tanggal' => '2026-10-17', 'amount' => '0', 'reason' => 'Verifikasi kas pukul dini hari', 'confirm' => '1', 'idempotency_key' => (string) Str::uuid()])->assertSessionHasNoErrors();
            $this->assertSame('2026-10-17', DB::table('cash_book')->value('opening_date'));
        } finally {
            Carbon::setTestNow();
            CarbonImmutable::setTestNow();
        }
    }
}
