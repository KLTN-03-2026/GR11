<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Symfony\Component\Process\Process;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('backup:database', function () {
    $connection = config('database.default');
    $driver = config("database.connections.{$connection}.driver");
    $database = config("database.connections.{$connection}.database");
    $host = config("database.connections.{$connection}.host");
    $port = config("database.connections.{$connection}.port");
    $username = config("database.connections.{$connection}.username");
    $password = config("database.connections.{$connection}.password");

    $backupDir = storage_path('app/backups');
    if (!File::exists($backupDir)) {
        File::makeDirectory($backupDir, 0755, true);
    }

    $timestamp = Carbon::now()->format('Ymd_His');
    $filename = "db_backup_{$timestamp}.sql";
    $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

    $writeMeta = static function (string $backupDir, array $meta): void {
        File::put(
            $backupDir . DIRECTORY_SEPARATOR . 'last_backup.json',
            json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    };

    if ($driver !== 'mysql') {
        $meta = [
            'success' => false,
            'database' => $database,
            'error' => "Driver {$driver} chưa được hỗ trợ backup tự động.",
            'backup_time_iso' => Carbon::now()->toIso8601String(),
            'backup_time_human' => Carbon::now()->format('d/m/Y H:i:s'),
        ];
        $writeMeta($backupDir, $meta);
        $this->error($meta['error']);
        return self::FAILURE;
    }

    $mysqldumpPath = (string) env('MYSQLDUMP_PATH', 'mysqldump');
    $mysqldumpBin = str_contains($mysqldumpPath, ' ')
        ? '"' . str_replace('"', '\"', $mysqldumpPath) . '"'
        : $mysqldumpPath;

    $cmd = sprintf(
        '%s --host=%s --port=%s --user=%s --password=%s %s --single-transaction --quick --skip-lock-tables --default-character-set=utf8mb4',
        $mysqldumpBin,
        escapeshellarg((string) $host),
        escapeshellarg((string) $port),
        escapeshellarg((string) $username),
        escapeshellarg((string) $password),
        escapeshellarg((string) $database)
    );

    $process = Process::fromShellCommandline($cmd);
    $process->setTimeout(300);
    $process->run();

    if (!$process->isSuccessful()) {
        $rawError = trim($process->getErrorOutput()) ?: 'Backup thất bại không rõ nguyên nhân.';
        $help = str_contains(strtolower($rawError), 'not recognized')
            ? 'Không tìm thấy mysqldump. Hãy thêm MYSQLDUMP_PATH vào .env, ví dụ: MYSQLDUMP_PATH="C:\\xampp\\mysql\\bin\\mysqldump.exe"'
            : null;
        $meta = [
            'success' => false,
            'database' => $database,
            'error' => $rawError,
            'hint' => $help,
            'backup_time_iso' => Carbon::now()->toIso8601String(),
            'backup_time_human' => Carbon::now()->format('d/m/Y H:i:s'),
        ];
        $writeMeta($backupDir, $meta);
        $this->error('Backup thất bại: ' . $meta['error']);
        if (!empty($meta['hint'])) {
            $this->warn($meta['hint']);
        }
        return self::FAILURE;
    }

    File::put($filepath, $process->getOutput());

    $meta = [
        'success' => true,
        'database' => $database,
        'file' => $filename,
        'backup_path' => $filepath,
        'backup_time_iso' => Carbon::now()->toIso8601String(),
        'backup_time_human' => Carbon::now()->format('d/m/Y H:i:s'),
        'size_bytes' => File::size($filepath),
    ];
    $writeMeta($backupDir, $meta);

    // Keep only 7 newest backup files.
    $backupFiles = collect(File::files($backupDir))
        ->filter(static fn($file) => str_starts_with($file->getFilename(), 'db_backup_') && str_ends_with($file->getFilename(), '.sql'))
        ->sortByDesc(static fn($file) => $file->getMTime())
        ->values();

    $backupFiles->slice(7)->each(static function ($file): void {
        File::delete($file->getPathname());
    });

    $this->info("Backup thành công: {$filename}");
    return self::SUCCESS;
})->purpose('Backup database MySQL and write backup metadata');

Schedule::command('backup:database')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer();
