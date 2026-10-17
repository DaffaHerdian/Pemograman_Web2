<?php

namespace App\Http\Controllers;

use App\Payments\SimulationProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function simulate(Request $r, SimulationProvider $provider)
    {
        abort_unless(app()->environment('local'), 403);
        $d = $r->validate(['amount' => 'required|integer|min:1|max:10000000']);

        return response()->json($provider->create((string) Str::uuid(), (string) $d['amount']));
    }

    public function webhook()
    {
        return response()->json(['status' => 'disabled', 'message' => 'Sandbox provider credentials and verified adapter required. No financial posting.'], 503);
    }
}
