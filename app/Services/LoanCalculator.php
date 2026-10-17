<?php

namespace App\Services;

use Brick\Math\BigDecimal as D;
use Brick\Math\RoundingMode as R;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

final class LoanCalculator
{
    public const DEMO = ['label' => 'DEMO disetujui 16-10-2026', 'method' => 'flat', 'rate' => '1.00', 'min_months' => 1, 'max_months' => 36, 'max_principal' => '10000000', 'allocation' => 'interest_first', 'early_settlement' => 'full_contract_interest', 'penalty' => '0', 'admin_fee' => '0', 'timezone' => 'Asia/Jakarta', 'rounding' => 'HALF_UP_FINAL_RESIDUAL'];

    public function quote(string $principal, int $months, string $date, array $policy = self::DEMO): array
    {
        if (! preg_match('/^\d{1,8}$/D', $principal) || D::of($principal)->isLessThan('1') || D::of($principal)->isGreaterThan($policy['max_principal']) || $months < $policy['min_months'] || $months > $policy['max_months']) {
            throw ValidationException::withMessages(['jumlah_pinjaman' => 'Nominal harus rupiah bulat, maksimal limit kebijakan; tenor 1–36 bulan.']);
        }
        $start = CarbonImmutable::createFromFormat('!Y-m-d', $date, $policy['timezone']);
        if (! $start || $start->format('Y-m-d') !== $date) {
            throw ValidationException::withMessages(['tanggal' => 'Tanggal tidak valid.']);
        }
        $p = D::of($principal)->toScale(0);
        $interest = $p->multipliedBy($policy['rate'])->multipliedBy($months)->dividedBy(100, 0, R::HalfUp);
        // Round regular installments HALF_UP; final row carries residual. Cap tiny contracts at remaining amounts.
        $schedule = [];
        $prevP = D::of(0);
        $prevI = D::of(0);
        for ($n = 1; $n <= $months; $n++) {
            $cumP = $n === $months ? $p : D::min($p, $prevP->plus($p->dividedBy($months, 0, R::HalfUp)));
            $cumI = $n === $months ? $interest : D::min($interest, $prevI->plus($interest->dividedBy($months, 0, R::HalfUp)));
            $partP = $cumP->minus($prevP);
            $partI = $cumI->minus($prevI);
            $schedule[] = ['number' => $n, 'due_date' => $start->addMonthsNoOverflow($n)->toDateString(), 'principal' => (string) $partP, 'interest' => (string) $partI, 'total' => (string) $partP->plus($partI)];
            $prevP = $cumP;
            $prevI = $cumI;
        }

        return ['principal' => (string) $p, 'interest' => (string) $interest, 'total' => (string) $p->plus($interest), 'monthly' => (string) $p->plus($interest)->dividedBy($months, 0, R::HalfUp), 'schedule' => $schedule, 'policy' => $policy];
    }
}
