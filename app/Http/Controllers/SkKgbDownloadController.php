<?php

namespace App\Http\Controllers;

use App\Models\SkKgb;
use App\Services\WatermarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class SkKgbDownloadController extends Controller
{
    protected WatermarkService $watermarkService;

    public function __construct(WatermarkService $watermarkService)
    {
        $this->watermarkService = $watermarkService;
    }

    /**
     * Download SK KGB document with watermark
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function download(SkKgb $skKgb)
    {
        // Check if user is authorized to download this file
        $user = Auth::user();

        // In a real application, you'd implement more complex authorization logic here
        // For example, checking if user belongs to the same tenant or has specific permissions
        if ($user->tenant_id && $user->tenant_id !== $skKgb->tenant_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh dokumen ini');
        }

        if (!$user->can('view', $skKgb)) {
            abort(403, 'Anda tidak memiliki izin untuk mengunduh dokumen ini');
        }

        try {
            // Check if the original file exists
            $originalFilePath = $skKgb->file_path;
            if (!Storage::disk('public')->exists($originalFilePath)) {
                abort(404, 'File tidak ditemukan');
            }

            // Create a watermarked version for download
            $watermarkText = 'DOKUMEN RESMI - ' . Auth::user()->name . ' - ' . now()->format('d/m/Y H:i:s');
            $watermarkedFilePath = $this->watermarkService->createTemporaryWatermarkedFile(
                $originalFilePath,
                $watermarkText
            );

            $fullPath = Storage::disk('public')->path($watermarkedFilePath);

            // Log the download activity
            \Log::info('SK KGB Download', [
                'user_id' => $user->id,
                'sk_kgb_id' => $skKgb->id,
                'action' => 'download',
                'file_path' => $originalFilePath,
                'watermarked_file_path' => $watermarkedFilePath,
            ]);

            return response()->download($fullPath, 'sk_kgb_' . $skKgb->no_sk . '_watermarked.pdf')->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            \Log::error('Error downloading SK KGB: ' . $e->getMessage(), [
                'sk_kgb_id' => $skKgb->id,
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'Terjadi kesalahan saat mengunduh dokumen');
        }
    }

    /**
     * Preview SK KGB document with watermark (in browser)
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function preview(SkKgb $skKgb)
    {
        // Check if user is authorized to preview this file
        $user = Auth::user();

        if ($user->tenant_id && $user->tenant_id !== $skKgb->tenant_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pratinjau dokumen ini');
        }

        if (!$user->can('view', $skKgb)) {
            abort(403, 'Anda tidak memiliki izin untuk melihat pratinjau dokumen ini');
        }

        try {
            // Check if the original file exists
            $originalFilePath = $skKgb->file_path;
            if (!Storage::disk('public')->exists($originalFilePath)) {
                abort(404, 'File tidak ditemukan');
            }

            // Create a watermarked version for preview
            $watermarkText = 'DOKUMEN PREVIEW - ' . Auth::user()->name . ' - ' . now()->format('d/m/Y H:i:s');
            $watermarkedFilePath = $this->watermarkService->createTemporaryWatermarkedFile(
                $originalFilePath,
                $watermarkText
            );

            $fullPath = Storage::disk('public')->path($watermarkedFilePath);

            // Log the preview activity
            \Log::info('SK KGB Preview', [
                'user_id' => $user->id,
                'sk_kgb_id' => $skKgb->id,
                'action' => 'preview',
                'file_path' => $originalFilePath,
                'watermarked_file_path' => $watermarkedFilePath,
            ]);

            return response()->file($fullPath, [
                'Content-Type' => 'application/pdf',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error previewing SK KGB: ' . $e->getMessage(), [
                'sk_kgb_id' => $skKgb->id,
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'Terjadi kesalahan saat menampilkan pratinjau dokumen');
        }
    }
}
