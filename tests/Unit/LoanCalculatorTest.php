<?php

namespace Tests\Unit;

use App\Services\LoanCalculator;
use Brick\Math\BigDecimal as D;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

final class LoanCalculatorTest extends TestCase
{
    public function test_approved_flat_example(): void
    {
        $q = (new LoanCalculator)->quote('6000000', 12, '2026-01-31');
        $this->assertSame('720000', $q['interest']);
        $this->assertSame('6720000', $q['total']);
        $this->assertSame('560000', $q['monthly']);
        $this->assertSame('2026-02-28', $q['schedule'][0]['due_date']);
        $this->assertSame('2026-03-31', $q['schedule'][1]['due_date']);
        foreach ($q['schedule'] as $s) {
            $this->assertSame('560000', $s['total']);
        }
    }

    public function test_rounding_reconciles_and_never_creates_negative_rows(): void
    {
        foreach (['1', '2', '35', '1000001', '9999999'] as $p) {
            foreach ([1, 3, 12, 36] as $n) {
                $q = (new LoanCalculator)->quote($p, $n, '2028-01-31');
                $total = D::of(0);
                $principal = D::of(0);
                foreach ($q['schedule'] as $s) {
                    $this->assertFalse(D::of($s['total'])->isNegative());
                    $total = $total->plus($s['total']);
                    $principal = $principal->plus($s['principal']);
                }
                $this->assertTrue($principal->isEqualTo($p));
                $this->assertTrue($total->isEqualTo($q['total']));
            }
        }
        $this->assertSame('2028-02-29', (new LoanCalculator)->quote('100', 2, '2028-01-31')['schedule'][0]['due_date']);
    }

    public function test_fractional_principal_rejected(): void
    {
        $this->expectException(ValidationException::class);
        (new LoanCalculator)->quote('100.50', 12, '2026-01-01');
    }
}
