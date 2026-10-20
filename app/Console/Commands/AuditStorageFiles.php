<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * php artisan storage:audit
 *
 * Membandingkan berkas di disk privat dengan path yang tercatat di database:
 *
 *   YATIM  = ada di disk, tidak tercatat di database  (sisa penghapusan yang gagal)
 *   HILANG = tercatat di database, tidak ada di disk  (berkas terhapus, barisnya tidak)
 *
 * Hanya MELAPORKAN, tidak menghapus apa pun. Exit code 1 bila ada temuan,
 * 0 bila bersih, jadi bisa dipakai di skrip pengujian atau CI.
 */
class AuditStorageFiles extends Command
{
    protected $signature = 'storage:audit';

    protected $description = 'Cari berkas yatim dan berkas hilang di disk privat (materials, assignments, submissions)';

    private const DISK = 'local'; // storage/app/private

    /** Folder di disk yang diperiksa. */
    private const FOLDERS = ['materials', 'assignments', 'submissions'];

    /** Tabel yang punya kolom file_path. */
    private const TABLES = ['materials', 'assignments', 'submissions', 'submission_revisions'];

    public function handle(): int
    {
        $disk = Storage::disk(self::DISK);

        // Semua path yang tercatat di database (versi terbaru + seluruh revisi).
        $inDatabase = collect(self::TABLES)
            ->flatMap(fn (string $table) => DB::table($table)
                ->whereNotNull('file_path')
                ->pluck('file_path'))
            ->unique()
            ->values();

        // Semua berkas yang benar-benar ada di folder-folder tersebut.
        $onDisk = collect(self::FOLDERS)
            ->flatMap(fn (string $folder) => $disk->allFiles($folder))
            ->reject(fn (string $path) => str_starts_with(basename($path), '.')) // .gitignore, .gitkeep
            ->unique()
            ->values();

        $orphans = $onDisk->diff($inDatabase)->values();
        $missing = $inDatabase->reject(fn (string $path) => $disk->exists($path))->values();

        $this->components->twoColumnDetail('Berkas tercatat di database', (string) $inDatabase->count());
        $this->components->twoColumnDetail('Berkas ada di disk', (string) $onDisk->count());
        $this->components->twoColumnDetail('Yatim  (di disk, tidak di database)', (string) $orphans->count());
        $this->components->twoColumnDetail('Hilang (di database, tidak di disk)', (string) $missing->count());
        $this->newLine();

        $this->report('Berkas yatim', $orphans);
        $this->report('Berkas hilang', $missing);

        if ($orphans->isEmpty() && $missing->isEmpty()) {
            $this->components->info('Bersih: setiap berkas di disk punya baris di database, dan sebaliknya.');

            return self::SUCCESS;
        }

        return self::FAILURE;
    }

    private function report(string $title, $paths): void
    {
        if ($paths->isEmpty()) {
            return;
        }

        $this->components->warn($title . ':');

        $paths->take(50)->each(fn (string $path) => $this->line("  - {$path}"));

        if ($paths->count() > 50) {
            $this->line('  ... dan ' . ($paths->count() - 50) . ' lainnya');
        }

        $this->newLine();
    }
}