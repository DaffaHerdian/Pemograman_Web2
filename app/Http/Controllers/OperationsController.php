<?php

namespace App\Http\Controllers;

use App\Services\Documents;
use App\Services\Finance;
use App\Services\Identity;
use App\Services\ReceiptService;
use Brick\Math\BigDecimal as D;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OperationsController extends Controller
{
    public function receipt(string $transaction, ReceiptService $receipts, Documents $docs, Request $r, Finance $finance)
    {
        $data = $receipts->data($transaction);
        $finance->audit($r->user()->id, 'receipt_print', $transaction);

        return $docs->pdf(view('finance.receipt', $data)->render(), $data['receipt']->number.'.pdf');
    }

    public function verify(string $token)
    {
        abort_unless(preg_match('/^[a-f0-9]{64}$/D', $token), 404);
        $q = DB::table('receipts as r')->join('financial_transactions as t', 't.id', '=', 'r.transaction_id')->where('r.token_hash', hash('sha256', $token))->select('r.number', 't.date', 't.amount', 't.id')->first();
        abort_unless($q, 404);
        $cancelled = DB::table('financial_transactions')->where('reversal_of', $q->id)->exists();

        return response()->view('finance.verify', ['number' => $q->number, 'date' => $q->date, 'amount' => $q->amount, 'cancelled' => $cancelled])->header('Cache-Control', 'no-store')->header('X-Robots-Tag', 'noindex, nofollow')->header('Referrer-Policy', 'no-referrer');
    }

    public function activity(Request $r)
    {
        return view('finance.activity', ['logs' => DB::table('activity_logs as l')->leftJoin('users as u', 'u.id', '=', 'l.actor_id')->select('l.*', 'u.name as actor')->orderByDesc('l.id')->paginate(30)]);
    }

    public function settings(Finance $finance)
    {
        return view('finance.settings', ['identity' => Identity::get(), 'rules' => $finance->rules()]);
    }

    public function saveSettings(Request $r, Finance $finance)
    {
        $d = $r->validate(['name' => 'required|string|max:255', 'address' => 'nullable|string|max:2000', 'telephone' => ['nullable', 'string', 'max:15', 'regex:/\A[0-9]+\z/'], 'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:1024|dimensions:max_width=2000,max_height=2000', 'rate' => 'required|regex:/^\d{1,2}(\.\d{1,2})?$/', 'max_principal' => 'required|integer|min:1|max:10000000', 'max_months' => 'required|integer|min:1|max:36', 'confirm_policy' => 'accepted'], ['telephone.regex' => 'Nomor telepon hanya boleh berisi angka, tanpa spasi atau tanda tambah.', 'telephone.max' => 'Nomor telepon maksimal 15 digit angka.']);
        $logo = null;
        if ($r->hasFile('logo')) {
            $image = @imagecreatefromstring(file_get_contents($r->file('logo')->getRealPath()));
            abort_unless($image, 422);
            $logo = 'logos/'.bin2hex(random_bytes(16)).'.png';
            Storage::disk('local')->makeDirectory('logos');
            imagepng($image, storage_path('app/private/'.$logo));
            imagedestroy($image);
        }
        DB::transaction(function () use ($d, $r, $finance, $logo) {
            DB::table('cash_book')->where('id', 1)->lockForUpdate()->firstOrFail();
            $identity = ['name' => $d['name'], 'address' => $d['address'] ?? null, 'telephone' => $d['telephone'] ?? null];
            if ($logo) {
                $identity['logo_path'] = $logo;
            }DB::table('cooperative_identity')->where('id', 1)->update($identity);
            $rules = $finance->rules();
            $rate = (string) D::of($d['rate'])->toScale(2);
            $changed = $rate !== $rules['rate'] || (string) $d['max_principal'] !== $rules['max_principal'] || (int) $d['max_months'] !== $rules['max_months'];
            if ($changed) {
                $rules['rate'] = $rate;
                $rules['max_principal'] = (string) $d['max_principal'];
                $rules['max_months'] = (int) $d['max_months'];
                $rules['label'] = 'DEMO disetujui admin '.now('Asia/Jakarta')->format('d-m-Y H:i');
                DB::table('financial_policies')->where('active', true)->update(['active' => false]);
                DB::table('financial_policies')->insert(['label' => $rules['label'], 'rules' => json_encode($rules), 'active' => true, 'created_at' => now(), 'updated_at' => now()]);
            }
            $finance->audit($r->user()->id, 'settings_update', 'cooperative', ['new_policy' => $changed, 'logo_updated' => $logo !== null]);
        });

        return back()->with('success', 'Pengaturan tersimpan. Perubahan kebijakan hanya untuk pengajuan baru.');
    }
}
