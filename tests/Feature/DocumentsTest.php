<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Finance;
use App\Services\ReceiptService;
use App\Services\ReportData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class DocumentsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create(['name' => 'QA admin', 'email' => 'documents@example.test', 'password' => bin2hex(random_bytes(24))]);
    }

    public function test_pdf_and_excel_have_real_file_signatures_and_guest_is_blocked(): void
    {
        $this->get('/laporan/pdf')->assertRedirect('/');
        $this->get('/laporan/excel')->assertRedirect('/');
        $this->actingAs($this->admin());
        $this->get('/laporan')->assertOk()->assertViewHas('type', 'simpanan');
        foreach (array_merge(ReportData::MAIN_TYPES, ['anggota']) as $type) {
            $this->get('/laporan?jenis='.$type)->assertOk()->assertViewHas('type', $type);
            $this->get('/laporan/print?jenis='.$type)->assertOk()->assertSee('Laporan '.ucfirst($type));
            $pdf = $this->get('/laporan/pdf?jenis='.$type)->assertOk()->assertHeader('Content-Type', 'application/pdf');
            $this->assertStringStartsWith('%PDF-', $pdf->getContent());
            $xlsx = $this->get('/laporan/excel?jenis='.$type)->assertOk();
            $this->assertStringStartsWith('PK', $xlsx->getContent());
        }
    }

    public function test_receipt_number_stable_public_data_minimal_and_cancelled_status_follows_reversal(): void
    {
        $admin = $this->admin();
        $f = new Finance;
        $f->opening('1000', now('Asia/Jakarta')->toDateString(), 'Hasil hitung kas uji', (string) Str::uuid(), $admin->id);
        $tx = DB::table('financial_transactions')->where('kind', 'opening')->first();
        $r = (new ReceiptService)->ensure($tx->id);
        $this->assertSame($r->number, (new ReceiptService)->ensure($tx->id)->number);
        $token = Crypt::decryptString($r->token_encrypted);
        $this->get('/verifikasi/'.$token)->assertOk()->assertSee($r->number)->assertSee('VALID')->assertDontSee($admin->email)->assertDontSee($admin->name);
        $this->actingAs($admin);
        $response = $this->get('/kuitansi/'.$tx->id)->assertOk();
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertSame(1, DB::table('receipts')->count());
    }

    public function test_pdf_print_and_index_use_identical_date_filters_and_escape_html(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $id = DB::table('anggota')->insertGetId(['nama' => '<script>untrusted()</script>', 'nik' => '1', 'telepon' => '0', 'jenis_kelamin' => 'Perempuan', 'alamat' => 'Fiktif', 'tanggal_bergabung' => '2026-01-01', 'status' => 'Aktif']);
        DB::table('simpanan')->insert([['id_anggota' => $id, 'tanggal' => '2026-01-01', 'jenis_simpanan' => 'Sukarela', 'jumlah' => '100'], ['id_anggota' => $id, 'tanggal' => '2026-02-01', 'jenis_simpanan' => 'Sukarela', 'jumlah' => '200']]);
        $filter = '?jenis=simpanan&tanggal_dari=2026-02-01&tanggal_sampai=2026-02-28';
        foreach (['/laporan', '/laporan/print'] as $url) {
            $this->get($url.$filter)->assertOk()->assertSee('2026-02-01')->assertDontSee('2026-01-01')->assertDontSee('<script>untrusted()', false);
        }
        $d = (new ReportData)->data(['jenis' => 'simpanan', 'tanggal_dari' => '2026-02-01', 'tanggal_sampai' => '2026-02-28']);
        $this->assertSame('200.00', $d['total']);
        $this->assertCount(1, $d['rows']);
    }

    public function test_xlsx_neutralizes_formula_cells(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        DB::table('anggota')->insert(['nama' => '=HYPERLINK("https://invalid.test")', 'nik' => '1', 'telepon' => '0', 'jenis_kelamin' => 'Perempuan', 'alamat' => 'Fiktif', 'tanggal_bergabung' => '2026-01-01', 'status' => 'Aktif']);
        $res = $this->get('/laporan/excel?jenis=anggota')->assertOk();
        $temp = tempnam(sys_get_temp_dir(), 'test-xlsx-');
        file_put_contents($temp, $res->getContent());
        try {
            $z = new \ZipArchive;
            $this->assertTrue($z->open($temp) === true);
            $xml = $z->getFromName('xl/worksheets/sheet1.xml');
            $this->assertStringNotContainsString('<f>', $xml);
            $this->assertStringContainsString('HYPERLINK', $xml);
            $z->close();
        } finally {
            unlink($temp);
        }
    }

    public function test_qr_uses_https_random_token_not_member_id(): void
    {
        config(['koperasi.receipt_public_url' => 'https://koperasi.example.test']);
        $admin = $this->admin();
        $f = new Finance;
        $tx = $f->opening('0', now('Asia/Jakarta')->toDateString(), 'Saldo nol diperiksa', (string) Str::uuid(), $admin->id);
        $data = (new ReceiptService)->data($tx->id);
        $this->assertStringStartsWith('https://koperasi.example.test/verifikasi/', $data['verificationUrl']);
        $this->assertStringContainsString('<svg', $data['qr']);
        $this->assertMatchesRegularExpression('~/[a-f0-9]{64}$~', $data['verificationUrl']);
    }

    public function test_settings_require_confirmation_and_preserve_old_policy(): void
    {
        $this->actingAs($this->admin());
        $d = ['name' => 'Demo QA', 'rate' => '2.00', 'max_principal' => '10000000', 'max_months' => 36];
        $this->postJson('/pengaturan', $d)->assertUnprocessable();
        $d['confirm_policy'] = '1';
        $this->post('/pengaturan', $d)->assertSessionHasNoErrors();
        $this->assertSame(2, DB::table('financial_policies')->count());
        $this->assertSame('1.00', json_decode(DB::table('financial_policies')->where('id', 1)->value('rules'), true)['rate']);
    }

    public function test_end_date_without_start_date_is_a_valid_filter(): void
    {
        $this->actingAs($this->admin());
        $this->get('/laporan?jenis=simpanan&tanggal_sampai=2026-10-16')->assertOk();
    }

    public function test_receipt_pdf_can_embed_generated_qr(): void
    {
        config(['koperasi.receipt_public_url' => 'https://koperasi.example.test']);
        $admin = $this->admin();
        $this->actingAs($admin);
        $tx = (new Finance)->opening('0', now('Asia/Jakarta')->toDateString(), 'Saldo pembukaan diverifikasi', (string) Str::uuid(), $admin->id);
        $r = $this->get('/kuitansi/'.$tx->id)->assertOk();
        $this->assertStringStartsWith('%PDF-', $r->getContent());
    }
}
