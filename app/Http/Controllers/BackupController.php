<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function __construct(
        protected DatabaseBackupService $backupService
    ) {}

    /**
     * Tampilkan halaman kelola backup database beserta riwayat file backup.
     */
    public function index(): View
    {
        $backups = $this->backupService->getBackups();
        $stats = $this->backupService->getDatabaseStats();

        return view('admin.backup.index', compact('backups', 'stats'));
    }

    /**
     * Proses pembuatan file cadangan (backup) basis data baru.
     */
    public function store(): RedirectResponse
    {
        try {
            $result = $this->backupService->generateBackup();
            $formattedSize = $this->backupService->formatFileSize($result['size_bytes']);

            return redirect()->route('admin.backup.index')->with(
                'success',
                "Cadangan basis data berhasil dibuat: {$result['filename']} ({$formattedSize}, {$result['tables_count']} tabel, {$result['rows_count']} baris data)."
            );
        } catch (Exception $e) {
            return redirect()->route('admin.backup.index')->with(
                'error',
                'Gagal membuat cadangan database: '.$e->getMessage()
            );
        }
    }

    /**
     * Unduh file backup basis data yang dipilih.
     */
    public function download(string $filename): StreamedResponse|RedirectResponse
    {
        $relativePath = $this->backupService->getValidBackupRelativePath($filename);

        if (! $relativePath) {
            return redirect()->route('admin.backup.index')->with(
                'error',
                'File cadangan tidak ditemukan atau nama file tidak valid.'
            );
        }

        $safeFilename = basename($filename);

        return Storage::disk('local')->download(
            $relativePath,
            $safeFilename,
            [
                'Content-Type' => 'application/sql',
                'Content-Disposition' => 'attachment; filename="'.$safeFilename.'"',
            ]
        );
    }

    /**
     * Hapus file cadangan basis data dari penyimpanan.
     */
    public function destroy(string $filename): RedirectResponse
    {
        $safeFilename = basename($filename);
        $deleted = $this->backupService->deleteBackup($safeFilename);

        if ($deleted) {
            return redirect()->route('admin.backup.index')->with(
                'success',
                "File cadangan '{$safeFilename}' berhasil dihapus."
            );
        }

        return redirect()->route('admin.backup.index')->with(
            'error',
            'File cadangan tidak ditemukan atau gagal dihapus.'
        );
    }
}
