<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Finance;
use Brick\Math\BigDecimal as D;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Database\Seeders\DemoFlowSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoFlowSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_dummy_flow_reconciles_and_repeated_seed_does_not_duplicate(): void
    {
        Carbon::setTestNow('2026-10-16 10:00:00');
        CarbonImmutable::setTestNow('2026-10-16 10:00:00');
        try {
            $this->seed(DemoFlowSeeder::class);
            $this->assertSame(8, DB::table('anggota')->count());
            $this->assertSame(7, DB::table('pinjaman')->count());
            $this->assertSame('16790000.00', app(Finance::class)->balance());
            $this->assertSame(1, DB::table('pinjaman')->where('workflow_status', 'submitted')->count());
            $this->assertSame(1, DB::table('pinjaman')->where('workflow_status', 'approved')->count());
            $this->assertSame(1, DB::table('pinjaman')->where('workflow_status', 'rejected')->count());
            $this->assertSame(1, DB::table('pinjaman')->where('workflow_status', 'closed')->count());
            $budi = DB::table('pinjaman as p')->join('anggota as a', 'a.id_anggota', '=', 'p.id_anggota')->where('a.nama', 'DEMO Budi')->select('p.*')->first();
            $this->assertTrue(D::of((string) $budi->sisa_pinjaman)->plus((string) $budi->sisa_jasa)->isEqualTo('6060000'));
            $this->assertSame(DB::table('ledger_entries')->count(), DB::table('receipts')->count());
            $this->assertSame(1, DB::table('financial_transactions')->whereNotNull('reversal_of')->count());
            $this->actingAs(User::firstOrFail());
            foreach (['overdue', 'soon', 'upcoming', 'paid'] as $status) {
                $this->get('/jatuh-tempo?status='.$status)->assertOk()->assertViewHas('schedules', fn ($s) => $s->total() > 0);
            }
            $counts = [];
            foreach (['users', 'anggota', 'pinjaman', 'simpanan', 'angsuran', 'financial_transactions', 'ledger_entries', 'receipts'] as $t) {
                $counts[$t] = DB::table($t)->count();
            }
            $this->seed(DemoFlowSeeder::class);
            foreach ($counts as $t => $count) {
                $this->assertSame($count, DB::table($t)->count());
            }
        } finally {
            Carbon::setTestNow();
            CarbonImmutable::setTestNow();
        }
    }

    public function test_existing_non_demo_data_is_not_seeded_over(): void
    {
        User::create(['name' => 'Existing', 'email' => 'existing@example.test', 'password' => bin2hex(random_bytes(16))]);
        $this->expectException(\RuntimeException::class);
        $this->seed(DemoFlowSeeder::class);
    }

    public function test_non_local_environment_is_rejected(): void
    {
        $this->app->instance('env', 'production');
        $this->expectException(\RuntimeException::class);
        $this->app->make(DemoFlowSeeder::class)->run();
    }
}
