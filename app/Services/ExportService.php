<?php

namespace App\Services;

use App\Models\PengajuanKgb;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    /**
     * Export pengajuan data to Excel
     */
    public function exportPengajuanToExcel(array $filters = []): string
    {
        $query = PengajuanKgb::query();

        // Apply tenant filter if user is not super admin
        $user = auth()->user();
        if (!$user->hasRole('super_admin_kabupaten')) {
            $query->where('tenant_id', $user->tenant_id);
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Get the data
        $pengajuanData = $query->with(['pegawai', 'tenant', 'userPengaju'])->get();

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator("KGB System")
            ->setLastModifiedBy("KGB System")
            ->setTitle("Laporan Pengajuan KGB")
            ->setSubject("Laporan Pengajuan KGB")
            ->setDescription("Laporan Pengajuan KGB untuk sistem KGB PNS");

        // Set headers
        $headers = [
            'No', 'NIP', 'Nama Pegawai', 'Dinas', 'Status', 'Tanggal Pengajuan', 
            'Tanggal Verifikasi Dinas', 'Tanggal Verifikasi Kabupaten', 
            'Tanggal Approve', 'Tanggal Selesai', 'Jenis Pengajuan', 
            'Catatan', 'No SK', 'TMT KGB Baru'
        ];

        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        // Set the data
        $row = 2;
        foreach ($pengajuanData as $index => $pengajuan) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $pengajuan->pegawai?->nip ?? '');
            $sheet->setCellValue('C' . $row, $pengajuan->pegawai?->nama ?? '');
            $sheet->setCellValue('D' . $row, $pengajuan->tenant?->nama ?? '');
            $sheet->setCellValue('E' . $row, ucfirst(str_replace('_', ' ', $pengajuan->status)));
            $sheet->setCellValue('F' . $row, $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('Y-m-d') : '');
            $sheet->setCellValue('G' . $row, $pengajuan->tanggal_verifikasi_dinas ? $pengajuan->tanggal_verifikasi_dinas->format('Y-m-d') : '');
            $sheet->setCellValue('H' . $row, $pengajuan->tanggal_verifikasi_kabupaten ? $pengajuan->tanggal_verifikasi_kabupaten->format('Y-m-d') : '');
            $sheet->setCellValue('I' . $row, $pengajuan->tanggal_approve ? $pengajuan->tanggal_approve->format('Y-m-d') : '');
            $sheet->setCellValue('J' . $row, $pengajuan->tanggal_selesai ? $pengajuan->tanggal_selesai->format('Y-m-d') : '');
            $sheet->setCellValue('K' . $row, ucfirst(str_replace('_', ' ', $pengajuan->jenis_pengajuan)));
            $sheet->setCellValue('L' . $row, $pengajuan->catatan ?? '');
            $sheet->setCellValue('M' . $row, $pengajuan->no_sk ?? '');
            $sheet->setCellValue('N' . $row, $pengajuan->tmt_kgb_baru ? $pengajuan->tmt_kgb_baru->format('Y-m-d') : '');

            $row++;
        }

        // Auto size columns
        foreach (range('A', $spreadsheet->getActiveSheet()->getHighestDataColumn()) as $col) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($col)
                ->setAutoSize(true);
        }

        // Create writer and save
        $writer = new Xlsx($spreadsheet);
        $fileName = 'laporan_pengajuan_kgb_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/temp/' . $fileName);
        
        // Ensure the temp directory exists
        Storage::makeDirectory('temp');
        
        $writer->save($filePath);

        return $filePath;
    }

    /**
     * Export pengajuan data to PDF
     */
    public function exportPengajuanToPdf(array $filters = []): string
    {
        // For PDF export, we'll use Laravel's built-in PDF functionality with dompdf
        // First, let's create a view for the PDF

        $query = PengajuanKgb::query();

        // Apply tenant filter if user is not super admin
        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin_kabupaten')) {
            $query->where('tenant_id', $user->tenant_id);
        }

        // Apply filters
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Get the data
        $pengajuanData = $query->with(['pegawai', 'tenant', 'userPengaju'])->get();

        // Create PDF using dompdf
        $pdf = \PDF::loadView('exports.pengajuan-pdf', [
            'pengajuanData' => $pengajuanData,
            'title' => 'Laporan Pengajuan KGB',
            'date' => date('Y-m-d H:i:s')
        ]);

        $fileName = 'laporan_pengajuan_kgb_' . date('Y-m-d_H-i-s') . '.pdf';
        $filePath = storage_path('app/temp/' . $fileName);

        // Ensure the temp directory exists
        Storage::makeDirectory('temp');

        $pdf->save($filePath);

        return $filePath;
    }
}