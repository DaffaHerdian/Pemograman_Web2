<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $count = DB::table('loan_schedules as s')->join('pinjaman as p', 'p.id_pinjaman', '=', 's.loan_id')->where('p.workflow_status', 'disbursed')->where('s.due_date', '<=', now('Asia/Jakarta')->addDays(7)->toDateString())->whereRaw('s.principal+s.interest>s.paid_principal+s.paid_interest')->count();
            $view->with('dueReminderCount', $count);
            $view->with('cashOpeningRequired', ! DB::table('cash_book')->where('id', 1)->value('reconciled'));
        });
    }
}
