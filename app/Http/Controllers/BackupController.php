<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackup;
use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function index(DatabaseBackup $backups)
    {
        return view('finance.backup', ['available' => $backups->available()]);
    }

    public function download(Request $r, DatabaseBackup $backups)
    {
        $r->validate(['confirm' => 'accepted', 'password' => 'required|current_password:web']);
        $path = $backups->create($r->user()->id);

        return response()->download($path, 'koperasiku-'.now('Asia/Jakarta')->format('Ymd-His').'.sql', ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff'])->deleteFileAfterSend(true);
    }
}
