<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

final class DatabaseBackup
{
    public function available(): bool
    {
        return config('database.default') === 'mysql' && is_file(config('koperasi.mysqldump')) && function_exists('proc_open');
    }

    public function create(int $actor): string
    {
        if (! $this->available()) {
            throw ValidationException::withMessages(['backup' => 'Dump aman tidak tersedia. Gunakan ekspor phpMyAdmin melalui akun hosting sendiri; fitur dibatasi.']);
        }

        return Cache::lock('koperasi-database-backup', 180)->block(1, function () use ($actor) {
            $dir = storage_path('app/private/backups');
            if (! is_dir($dir)) {
                mkdir($dir, 0700, true);
            }
            $path = $dir.'/backup-'.bin2hex(random_bytes(16)).'.sql';
            $cfg = config('database.connections.mysql');
            $args = [config('koperasi.mysqldump'), '--host='.$cfg['host'], '--port='.$cfg['port'], '--user='.$cfg['username'], '--single-transaction', '--routines', '--triggers', '--hex-blob', '--no-tablespaces', '--set-gtid-purged=OFF', '--result-file='.$path, $cfg['database']];
            $process = new Process($args, null, ['MYSQL_PWD' => (string) $cfg['password']], null, 120);
            try {
                $process->start();
                while ($process->isRunning()) {
                    $process->checkTimeout();
                    clearstatcache(true, $path);
                    if (is_file($path) && filesize($path) > config('koperasi.backup_limit_bytes')) {
                        $process->stop(0);
                        throw new \RuntimeException('Backup exceeds limit');
                    }usleep(100000);
                }
                clearstatcache(true, $path);
                if (! $process->isSuccessful() || ! is_file($path) || filesize($path) < 100 || filesize($path) > config('koperasi.backup_limit_bytes')) {
                    throw new \RuntimeException('Backup incomplete');
                }
                $handle = fopen($path, 'rb');
                $header = fread($handle, 200);
                fclose($handle);
                if (! str_contains($header, 'MySQL dump')) {
                    throw new \RuntimeException('Invalid dump header');
                }
                @chmod($path, 0600);
                (new Finance)->audit($actor, 'database_backup', basename($path), ['bytes' => filesize($path), 'sha256' => hash_file('sha256', $path)]);

                return $path;
            } catch (\Throwable) {
                if ($process->isRunning()) {
                    $process->stop(0);
                }if (is_file($path)) {
                    unlink($path);
                }throw ValidationException::withMessages(['backup' => 'Backup gagal atau melebihi 50 MB/120 detik. Tidak ada file tidak lengkap yang disediakan. Gunakan phpMyAdmin bila hosting membatasi dump.']);
            }
        });
    }
}
