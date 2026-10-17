<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialRequest;
use App\Services\Finance;
use Illuminate\Http\Request;

class AngsuranController extends Controller
{
    public function index(Request $request)
    {
        return app(SimpananController::class)->index($request);
    }

    public function store(FinancialRequest $request, Finance $finance)
    {
        $finance->pay($request->validated(), $request->user()->id);

        return back()->with('success', 'Pembayaran tercatat beserta alokasi dan buku kas.');
    }
}
