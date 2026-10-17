<?php

namespace App\Payments;

/** Deliberately cannot post to Finance or a real provider. A demo is never a live payment. */
final class SimulationProvider implements PaymentProvider
{
    public function create(string $reference, string $amount): array
    {
        return ['reference' => $reference, 'amount' => $amount, 'status' => 'pending', 'label' => 'SIMULASI — tidak menerima pembayaran nyata', 'provider' => 'local_mock'];
    }

    public function verify(string $body, string $signature): bool
    {
        return false;
    }
}
