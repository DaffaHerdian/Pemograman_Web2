<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class FinancialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $rules = ['idempotency_key' => 'required|uuid', 'tanggal' => 'required|date_format:Y-m-d|before_or_equal:'.now('Asia/Jakarta')->toDateString()];

        return match ($this->route()->getName()) {
            'pinjaman.store' => $rules + ['id_anggota' => 'required|integer|exists:anggota,id_anggota', 'jumlah_pinjaman' => 'required|regex:/^\d{1,8}$/', 'tenor' => 'required|integer|min:1|max:36'],
            'angsuran.store','finance.settle' => $rules + ['id_pinjaman' => 'required|integer|exists:pinjaman,id_pinjaman', 'jumlah_bayar' => 'required|regex:/^\d{1,13}(\.\d{1,2})?$/'],
            'simpanan.store','simpanan.withdraw' => $rules + ['id_anggota' => 'required|integer|exists:anggota,id_anggota', 'jumlah' => 'required|regex:/^\d{1,13}(\.\d{1,2})?$/', 'jenis_simpanan' => ($this->route()->getName() === 'simpanan.store' ? 'required|in:Pokok,Wajib,Sukarela' : 'sometimes|in:Sukarela')],
            'finance.opening' => $rules + ['amount' => 'required|regex:/^\d{1,13}(\.\d{1,2})?$/', 'reason' => 'required|string|min:10|max:2000', 'confirm' => 'accepted'],
            'finance.reverse' => $rules + ['reason' => 'required|string|min:10|max:2000', 'confirm' => 'accepted'],
            default => $rules,
        };
    }
}
