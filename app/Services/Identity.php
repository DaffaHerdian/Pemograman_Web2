<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

final class Identity
{
    public static function get(): object
    {
        $i = DB::table('cooperative_identity')->first();
        $i->logo_data = null;
        if ($i->logo_path && preg_match('~^logos/[a-f0-9]{32}\.png$~D', $i->logo_path)) {
            $path = storage_path('app/private/'.$i->logo_path);
            if (is_file($path)) {
                $i->logo_data = 'data:image/png;base64,'.base64_encode(file_get_contents($path));
            }
        }

        return $i;
    }
}
