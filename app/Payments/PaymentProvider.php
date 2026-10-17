<?php

namespace App\Payments;

interface PaymentProvider
{
    public function create(string $reference, string $amount): array;

    public function verify(string $body, string $signature): bool;
}
