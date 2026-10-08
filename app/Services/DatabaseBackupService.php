<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DatabaseBackupService
{
    protected string $disk = 'local';

    protected string $directory = 'backups';

    /**
     * Pastikan direktori backup tersedia pada storage.
     */
    protected function ensureDirectoryExists(): void
    {
        if (! Storage::disk($this->disk)->exists($this->directory)) {
            Storage::disk($this->disk)->makeDirectory($this->directory);
        }
    }

    /**
     * Dapatkan informasi ringkas mengenai basis data saat ini.
     *
     * @return array{driver: string, database: string, tables_count: int, backups_count: int}
     */
    public function getDatabaseStats(): array
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $databaseName = (string) $connection->getDatabaseName();

        $tables = $this->getTableNames();
        $backups = $this->getBackups();

        return [
            'driver' => $driver,
            'database' => $databaseName,
            'tables_count' => count($tables),
            'backups_count' => count($backups),
        ];
    }

    /**
     * Dapatkan daftar nama seluruh tabel pada basis data yang aktif.
     *
     * @return array<int, string>
     */
    public function getTableNames(): array
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $tables = [];

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $results = DB::select("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            foreach ($results as $row) {
                $rowArray = (array) $row;
                $tables[] = (string) reset($rowArray);
            }
        } elseif ($driver === 'sqlite') {
            $results = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($results as $row) {
                $tables[] = (string) $row->name;
            }
        } elseif ($driver === 'pgsql') {
            $results = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_type = 'BASE TABLE'");
            foreach ($results as $row) {
                $tables[] = (string) $row->table_name;
            }
        } else {
            // Fallback via schema tables
            $schemaTables = $connection->getDoctrineSchemaManager()->listTableNames();
            $tables = array_values($schemaTables);
        }

        return $tables;
    }

    /**
     * Jalankan proses backup penuh database ke dalam file SQL.
     *
     * @return array{filename: string, relative_path: string, absolute_path: string, size_bytes: int, tables_count: int, rows_count: int}
     */
    public function generateBackup(): array
    {
        $this->ensureDirectoryExists();

        $connection = DB::connection();
        $driver = $connection->getDriverName();
        $databaseName = (string) $connection->getDatabaseName();
        $cleanDbName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', basename($databaseName)) ?: 'database';

        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$cleanDbName}_{$timestamp}.sql";
        $relativePath = $this->directory.'/'.$filename;

        // Path penyimpanan riil
        $absolutePath = Storage::disk($this->disk)->path($relativePath);
        $directoryPath = dirname($absolutePath);
        if (! is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }

        $handle = fopen($absolutePath, 'w');
        if ($handle === false) {
            throw new RuntimeException("Gagal membuka file tujuan backup: {$absolutePath}");
        }

        $pdo = $connection->getPdo();
        $tables = $this->getTableNames();
        $totalRows = 0;

        // Tulis header SQL
        fwrite($handle, "-- ============================================================\n");
        fwrite($handle, "-- Sistem Informasi Penyaluran Bantuan Sosial\n");
        fwrite($handle, "-- File Cadangan Basis Data (Database Backup)\n");
        fwrite($handle, '-- Tanggal Pembuatan : '.now()->format('d/m/Y H:i:s')."\n");
        fwrite($handle, "-- Basis Data        : {$databaseName}\n");
        fwrite($handle, "-- Driver Mesin      : {$driver}\n");
        fwrite($handle, '-- Jumlah Tabel      : '.count($tables)."\n");
        fwrite($handle, "-- ============================================================\n\n");

        if ($driver === 'mysql' || $driver === 'mariadb') {
            fwrite($handle, "SET NAMES utf8mb4;\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
            fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
            fwrite($handle, "SET AUTOCOMMIT = 0;\n");
            fwrite($handle, "START TRANSACTION;\n");
            fwrite($handle, "SET time_zone = \"+00:00\";\n\n");
        } elseif ($driver === 'sqlite') {
            fwrite($handle, "PRAGMA foreign_keys = OFF;\n");
            fwrite($handle, "BEGIN TRANSACTION;\n\n");
        }

        foreach ($tables as $table) {
            fwrite($handle, "-- ------------------------------------------------------------\n");
            fwrite($handle, "-- Struktur dan Data untuk Tabel `{$table}`\n");
            fwrite($handle, "-- ------------------------------------------------------------\n\n");

            if ($driver === 'mysql' || $driver === 'mariadb') {
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                $createTableQuery = DB::select("SHOW CREATE TABLE `{$table}`");
                if (! empty($createTableQuery)) {
                    $firstCol = (array) $createTableQuery[0];
                    $createSql = $firstCol['Create Table'] ?? (end($firstCol) ?: '');
                    if ($createSql) {
                        fwrite($handle, $createSql.";\n\n");
                    }
                }
            } elseif ($driver === 'sqlite') {
                fwrite($handle, "DROP TABLE IF EXISTS \"{$table}\";\n");
                $createTableQuery = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                if (! empty($createTableQuery) && ! empty($createTableQuery[0]->sql)) {
                    fwrite($handle, $createTableQuery[0]->sql.";\n\n");
                }
            }

            // Dump data baris per baris secara bertahap (chunk)
            $count = DB::table($table)->count();
            if ($count > 0) {
                $totalRows += $count;
                $chunkSize = 200;
                $offset = 0;

                while ($offset < $count) {
                    $rows = DB::table($table)->offset($offset)->limit($chunkSize)->get();
                    if ($rows->isEmpty()) {
                        break;
                    }

                    $insertStatements = [];
                    $columns = null;

                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        if ($columns === null) {
                            $columns = array_keys($rowArray);
                        }

                        $escapedValues = [];
                        foreach ($rowArray as $value) {
                            if (is_null($value)) {
                                $escapedValues[] = 'NULL';
                            } elseif (is_int($value) || is_float($value)) {
                                $escapedValues[] = (string) $value;
                            } else {
                                $escapedValues[] = $pdo->quote((string) $value);
                            }
                        }
                        $insertStatements[] = '('.implode(', ', $escapedValues).')';
                    }

                    if (! empty($insertStatements) && ! empty($columns)) {
                        $colList = ($driver === 'sqlite')
                            ? '"'.implode('", "', $columns).'"'
                            : '`'.implode('`, `', $columns).'`';

                        $quoteTable = ($driver === 'sqlite') ? "\"{$table}\"" : "`{$table}`";

                        fwrite($handle, "INSERT INTO {$quoteTable} ({$colList}) VALUES\n");
                        fwrite($handle, implode(",\n", $insertStatements).";\n\n");
                    }

                    $offset += $chunkSize;
                }
            }

            fwrite($handle, "\n");
        }

        // Tulis footer penutup transaksi
        if ($driver === 'mysql' || $driver === 'mariadb') {
            fwrite($handle, "COMMIT;\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        } elseif ($driver === 'sqlite') {
            fwrite($handle, "COMMIT;\n");
            fwrite($handle, "PRAGMA foreign_keys = ON;\n");
        }

        fclose($handle);

        clearstatcache(true, $absolutePath);
        $sizeBytes = (int) (filesize($absolutePath) ?: 0);

        return [
            'filename' => $filename,
            'relative_path' => $relativePath,
            'absolute_path' => $absolutePath,
            'size_bytes' => $sizeBytes,
            'tables_count' => count($tables),
            'rows_count' => $totalRows,
        ];
    }

    /**
     * Dapatkan daftar seluruh file backup yang telah tersimpan.
     *
     * @return array<int, array{filename: string, relative_path: string, size_bytes: int, size_formatted: string, created_at: Carbon, created_at_human: string, timestamp: int}>
     */
    public function getBackups(): array
    {
        $this->ensureDirectoryExists();

        $files = Storage::disk($this->disk)->files($this->directory);
        $backups = [];

        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (! in_array($extension, ['sql', 'gz', 'zip'])) {
                continue;
            }

            $filename = basename($file);
            $sizeBytes = (int) (Storage::disk($this->disk)->size($file) ?: 0);
            $lastModified = Storage::disk($this->disk)->lastModified($file) ?: time();
            $createdAt = Carbon::createFromTimestamp($lastModified);

            $backups[] = [
                'filename' => $filename,
                'relative_path' => $file,
                'size_bytes' => $sizeBytes,
                'size_formatted' => $this->formatFileSize($sizeBytes),
                'created_at' => $createdAt,
                'created_at_human' => $createdAt->diffForHumans(),
                'timestamp' => $lastModified,
            ];
        }

        // Urutkan dari file terbaru ke terlama
        usort($backups, fn (array $a, array $b): int => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Dapatkan path relatif file backup yang valid dan aman dari directory traversal.
     */
    public function getValidBackupRelativePath(string $filename): ?string
    {
        $safeFilename = basename($filename);
        $relativePath = $this->directory.'/'.$safeFilename;

        if (Storage::disk($this->disk)->exists($relativePath)) {
            return $relativePath;
        }

        return null;
    }

    /**
     * Hapus file backup yang dipilih.
     */
    public function deleteBackup(string $filename): bool
    {
        $relativePath = $this->getValidBackupRelativePath($filename);
        if ($relativePath === null) {
            return false;
        }

        return Storage::disk($this->disk)->delete($relativePath);
    }

    /**
     * Format ukuran file ke format yang mudah dibaca (B, KB, MB, GB).
     */
    public function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2).' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }
}
