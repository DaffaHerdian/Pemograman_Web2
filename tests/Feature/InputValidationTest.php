<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\User;
use App\Services\Finance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class InputValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    private function memberData(array $overrides = []): array
    {
        return array_replace([
            'nama' => 'Budi Santoso',
            'nik' => '0000000000000001',
            'telepon' => '081234567890',
            'jenis_kelamin' => 'Laki-laki',
            'alamat' => 'Jalan Mawar No. 12',
            'tanggal_bergabung' => now('Asia/Jakarta')->toDateString(),
            'status' => 'Aktif',
        ], $overrides);
    }

    public function test_create_preserves_leading_zeroes_and_accepts_human_names(): void
    {
        foreach (["M. Ali O'Connor", 'Siti-Aminah', 'Élodie Nguyễn'] as $index => $name) {
            $data = $this->memberData(['nama' => $name, 'nik' => str_pad((string) ($index + 1), 16, '0', STR_PAD_LEFT)]);
            $this->postJson('/anggota', $data)->assertRedirect(route('anggota.index'))->assertSessionHasNoErrors();
            $this->assertDatabaseHas('anggota', ['nama' => $name, 'nik' => $data['nik'], 'telepon' => '081234567890']);
        }
    }

    public function test_invalid_member_fields_never_create_a_member_or_audit_record(): void
    {
        $cases = [
            ['nama', 'Budi123'], ['nama', '<script>'], ['nama', '---'],
            ['nik', '123'], ['nik', '12345678901234567'], ['nik', '123456789012345A'],
            ['nik', '1e15'], ['telepon', '08123abc'], ['telepon', '+628123456789'],
            ['telepon', '0812 3456'], ['telepon', '1234567890123456'],
        ];
        foreach ($cases as [$field, $value]) {
            $this->postJson('/anggota', $this->memberData([$field => $value]))
                ->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $this->assertDatabaseCount('anggota', 0);
        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_edit_validates_the_same_fields_and_keeps_unchanged_nik_unique(): void
    {
        $member = Anggota::create($this->memberData());
        foreach (['nama' => 'Budi2', 'nik' => 'invalid', 'telepon' => '0800x'] as $field => $value) {
            $this->putJson('/anggota/'.$member->getKey(), $this->memberData([$field => $value]))
                ->assertUnprocessable()->assertJsonValidationErrors($field);
        }
        $this->assertDatabaseHas('anggota', ['id_anggota' => $member->getKey(), 'nama' => 'Budi Santoso', 'telepon' => '081234567890']);
        $this->putJson('/anggota/'.$member->getKey(), $this->memberData(['nama' => 'Budi Wijaya']))
            ->assertRedirect(route('anggota.index'))->assertSessionHasNoErrors();
        $other = Anggota::create($this->memberData(['nik' => '0000000000000002']));
        $this->putJson('/anggota/'.$member->getKey(), $this->memberData(['nik' => $other->nik]))
            ->assertUnprocessable()->assertJsonValidationErrors('nik');
    }

    public function test_cooperative_telephone_rejects_letters_without_changing_identity_or_policy(): void
    {
        $before = DB::table('cooperative_identity')->first();
        $policies = DB::table('financial_policies')->count();
        $rules = app(Finance::class)->rules();
        $data = ['name' => 'Koperasi 17 Agustus', 'telephone' => '021ABC', 'rate' => $rules['rate'], 'max_principal' => $rules['max_principal'], 'max_months' => $rules['max_months'], 'confirm_policy' => '1'];
        $this->postJson('/pengaturan', $data)->assertUnprocessable()->assertJsonValidationErrors('telephone');
        $this->assertEquals($before, DB::table('cooperative_identity')->first());
        $this->assertDatabaseCount('financial_policies', $policies);
        $data['telephone'] = '0211234567';
        $this->postJson('/pengaturan', $data)->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cooperative_identity', ['telephone' => '0211234567', 'name' => 'Koperasi 17 Agustus']);
    }

    public function test_money_and_tenor_reject_text_and_scientific_notation(): void
    {
        $member = Anggota::create($this->memberData());
        foreach (['1e3', '1000abc', '1,000'] as $value) {
            $this->postJson('/simpanan', ['id_anggota' => $member->getKey(), 'tanggal' => now('Asia/Jakarta')->toDateString(), 'jenis_simpanan' => 'Sukarela', 'jumlah' => $value, 'idempotency_key' => (string) Str::uuid()])
                ->assertUnprocessable()->assertJsonValidationErrors('jumlah');
        }
        $this->postJson('/pinjaman', ['id_anggota' => $member->getKey(), 'tanggal' => now('Asia/Jakarta')->toDateString(), 'jumlah_pinjaman' => '1000000', 'tenor' => '1e1', 'idempotency_key' => (string) Str::uuid()])
            ->assertUnprocessable()->assertJsonValidationErrors('tenor');
        $this->assertDatabaseCount('financial_transactions', 0);
    }
}
