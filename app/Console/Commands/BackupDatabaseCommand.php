<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Exception;
use Illuminate\Console\Command;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cadangkan struktur dan data basis data ke file SQL';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $backupService): int
    {
        $this->info('Memulai pencadangan basis data...');

        try {
            $result = $backupService->generateBackup();
            $formattedSize = $backupService->formatFileSize($result['size_bytes']);

            $this->info('Pencadangan berhasil!');
            $this->table(
                ['Parameter', 'Keterangan'],
                [
                    ['Nama File', $result['filename']],
                    ['Lokasi Penyimpanan', $result['absolute_path']],
                    ['Ukuran File', $formattedSize],
                    ['Jumlah Tabel', (string) $result['tables_count']],
                    ['Jumlah Baris Data', (string) $result['rows_count']],
                ]
            );

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Gagal mencadangkan basis data: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
