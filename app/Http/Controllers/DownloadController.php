<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DownloadController extends Controller
{
    /**
     * Download a document template and track the download
     *
     * @param DocumentTemplate $documentTemplate
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function downloadDocumentTemplate(DocumentTemplate $documentTemplate)
    {
        // Check if user is authorized to download this file
        $user = Auth::user();

        // Check if the document template is active
        if (!$documentTemplate->is_active) {
            abort(404, 'Template dokumen tidak ditemukan atau tidak aktif');
        }

        // Check tenant access if applicable
        if ($user->tenant_id && $documentTemplate->tenant_id && $user->tenant_id !== $documentTemplate->tenant_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh template ini');
        }

        try {
            // Check if the file exists
            if (!Storage::disk('public')->exists($documentTemplate->file_path)) {
                abort(404, 'File template tidak ditemukan');
            }

            // Increment download count
            $documentTemplate->incrementDownloadCount();

            // Log the download activity
            \Log::info('Document Template Download', [
                'user_id' => $user->id,
                'document_template_id' => $documentTemplate->id,
                'document_template_name' => $documentTemplate->name,
                'file_path' => $documentTemplate->file_path,
            ]);

            // Create response with appropriate headers
            $downloadName = str_replace(' ', '_', $documentTemplate->name) . '.' . $documentTemplate->file_type;
            $filePath = Storage::disk('public')->path($documentTemplate->file_path);

            return response()->download($filePath, $downloadName);
        } catch (\Exception $e) {
            \Log::error('Error downloading document template: ' . $e->getMessage(), [
                'document_template_id' => $documentTemplate->id,
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'Terjadi kesalahan saat mengunduh template dokumen');
        }
    }
}
