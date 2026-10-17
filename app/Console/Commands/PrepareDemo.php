<?php

namespace App\Console\Commands;

use Database\Seeders\DemoFlowSeeder;
use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class PrepareDemo extends Command
{
    protected $signature = 'koperasi:demo';

    protected $description = 'Siapkan data fiktif latihan pada SQLite terpisah, tanpa mengubah database aplikasi utama';

    public function handle(): int
    {
        if (! app()->environment('local') || app()->configurationIsCached()) {
            $this->error('Jalankan pada aplikasi lokal tanpa config cache.');

            return self::FAILURE;
        }
        $path = database_path('demo-flow.sqlite');
        $envPath = base_path('.env.demo');
        if (is_file($envPath)) {
            $env = Dotenv::parse(file_get_contents($envPath));
            if (($env['DB_CONNECTION'] ?? '') !== 'sqlite' || str_replace('\\', '/', $env['DB_DATABASE'] ?? '') !== str_replace('\\', '/', $path) || empty($env['APP_KEY'])) {
                throw new RuntimeException('.env.demo sudah ada tetapi bukan konfigurasi latihan ini. Tidak ditimpa.');
            }
            $key = $env['APP_KEY'];
        } else {
            $key = 'base64:'.base64_encode(random_bytes(32));
        }
        if (! is_file($path)) {
            $handle = fopen($path, 'x');
            fclose($handle);
        }
        config(['database.connections.demo_flow' => array_merge(config('database.connections.sqlite'), ['url' => null, 'database' => $path]), 'database.default' => 'demo_flow', 'app.key' => $key]);
        app()->forgetInstance('encrypter');
        Crypt::clearResolvedInstance('encrypter');
        $contents = "APP_NAME=\"KoperasiKu Latihan\"\nAPP_ENV=local\nAPP_KEY={$key}\nAPP_DEBUG=true\nAPP_URL=http://127.0.0.1:8002\nAPP_LOCALE=id\nDB_CONNECTION=sqlite\nDB_DATABASE=\"".str_replace('\\', '/', $path)."\"\nSESSION_DRIVER=file\nSESSION_COOKIE=koperasiku_demo_session\nCACHE_STORE=file\nCACHE_PREFIX=koperasiku_demo_\nQUEUE_CONNECTION=sync\nMAIL_MAILER=log\nLOG_CHANNEL=single\n";
        if (file_put_contents($envPath, $contents) === false) {
            throw new RuntimeException('Gagal menyimpan konfigurasi latihan.');
        }
        if ($this->call('migrate', ['--database' => 'demo_flow', '--force' => true]) !== 0) {
            return self::FAILURE;
        }
        if ($this->call('db:seed', ['--database' => 'demo_flow', '--class' => DemoFlowSeeder::class, '--force' => true]) !== 0) {
            return self::FAILURE;
        }
        $this->info('Latihan: http://127.0.0.1:8002 — login admin@koperasiku.id / demo-training-only');
        $this->line('Jalankan: php artisan serve --env=demo --host=127.0.0.1 --port=8002 --tries=1 --no-reload');
        $this->line('Aplikasi utama dan .env tetap memakai konfigurasi semula. Panduan: DEMO_FLOW_GUIDE.md');

        return self::SUCCESS;
    }
}
