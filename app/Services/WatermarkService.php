<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use setasign\Fpdi\Fpdi;

class WatermarkService
{
    /**
     * Add watermark to PDF file and return the watermarked version
     *
     * @param string $filePath Path to the original PDF file
     * @param string|null $watermarkText Custom watermark text (default: "DRAFT")
     * @return string Path to the watermarked PDF file
     */
    public function addWatermark(string $filePath, ?string $watermarkText = null): string
    {
        if (!$watermarkText) {
            $watermarkText = 'DOKUMEN RESMI';
        }

        $originalFile = Storage::disk('public')->path($filePath);
        
        if (!file_exists($originalFile)) {
            throw new \Exception('File tidak ditemukan');
        }

        $pdf = new Fpdi();
        
        // Import the original PDF
        $pageCount = $pdf->setSourceFile($originalFile);
        
        // Import each page and add watermark
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);
            
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
            
            // Set watermark properties
            $pdf->SetFont('Arial', 'I', 40);
            $pdf->SetTextColor(200, 200, 200); // Light gray
            
            // Calculate position for the watermark
            $x = $size['width'] / 2;
            $y = $size['height'] / 2;
            
            $pdf->SetXY($x, $y);
            $pdf->SetFontSize(40);
            
            // Rotate and place the watermark
            $pdf->StartTransform();
            $pdf->Rotate(-45, $x, $y);
            $pdf->Cell(0, 0, $watermarkText, 0, 0, 'C');
            $pdf->StopTransform();
        }
        
        // Generate new filename for watermarked file
        $pathInfo = pathinfo($filePath);
        $watermarkedFilename = $pathInfo['filename'] . '_watermarked_' . time() . '.pdf';
        $watermarkedPath = $pathInfo['dirname'] . '/' . $watermarkedFilename;
        
        // Save the watermarked PDF
        $watermarkedFile = Storage::disk('public')->path($watermarkedPath);
        $pdf->Output('F', $watermarkedFile);
        
        return $watermarkedPath;
    }
    
    /**
     * Create a temporary watermarked version for download
     *
     * @param string $filePath Path to the original PDF file
     * @param string|null $watermarkText Custom watermark text
     * @return string Path to the temporary watermarked PDF file
     */
    public function createTemporaryWatermarkedFile(string $filePath, ?string $watermarkText = null): string
    {
        $watermarkedPath = $this->addWatermark($filePath, $watermarkText);
        
        // Schedule the file for deletion after a short time
        $this->scheduleFileCleanup($watermarkedPath, 300); // Delete after 5 minutes
        
        return $watermarkedPath;
    }
    
    /**
     * Schedule a file for cleanup after specified time
     *
     * @param string $filePath
     * @param int $delaySeconds
     * @return void
     */
    protected function scheduleFileCleanup(string $filePath, int $delaySeconds): void
    {
        // Use Laravel's queue system to schedule the deletion
        \Illuminate\Support\Facades\Queue::later(
            now()->addSeconds($delaySeconds),
            function () use ($filePath) {
                try {
                    $fullPath = Storage::disk('public')->path($filePath);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                } catch (\Exception $e) {
                    // Log the error but don't fail the main request
                    \Log::warning('Failed to delete temporary watermarked file: ' . $e->getMessage());
                }
            }
        );
    }
}