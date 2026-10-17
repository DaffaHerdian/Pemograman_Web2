<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

final class ReceiptService
{
    public function ensure(string $transaction): object
    {
        return DB::transaction(function () use ($transaction) {
            $tx = DB::table('financial_transactions')->where('id', $transaction)->lockForUpdate()->firstOrFail();
            if ($old = DB::table('receipts')->where('transaction_id', $transaction)->first()) {
                return $old;
            }
            if (! DB::table('ledger_entries')->where('transaction_id', $transaction)->exists()) {
                abort(404);
            }
            $token = bin2hex(random_bytes(32));
            $id = DB::table('receipts')->insertGetId(['transaction_id' => $transaction, 'number' => 'KWT-'.$transaction, 'token_hash' => hash('sha256', $token), 'token_encrypted' => Crypt::encryptString($token), 'created_at' => now()]);

            return DB::table('receipts')->where('id', $id)->first();
        });
    }

    public function data(string $transaction): array
    {
        $receipt = $this->ensure($transaction);
        $tx = DB::table('financial_transactions')->where('id', $transaction)->firstOrFail();
        $base = config('koperasi.receipt_public_url');
        $url = null;
        $qr = null;
        if ($base && filter_var($base, FILTER_VALIDATE_URL) && parse_url($base, PHP_URL_SCHEME) === 'https') {
            $url = rtrim($base, '/').'/verifikasi/'.Crypt::decryptString($receipt->token_encrypted);
            $qr = (new Writer(new ImageRenderer(new RendererStyle(180), new SvgImageBackEnd)))->writeString($url);
        }

        return ['receipt' => $receipt, 'transaction' => $tx, 'actor' => DB::table('users')->where('id', $tx->actor_id)->value('name'), 'identity' => Identity::get(), 'cancelled' => DB::table('financial_transactions')->where('reversal_of', $transaction)->exists(), 'verificationUrl' => $url, 'qr' => $qr];
    }
}
