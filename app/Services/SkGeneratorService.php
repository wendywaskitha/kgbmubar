<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\PengajuanKgb;
use App\Models\SkTemplate;
use App\Models\SkKgb;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class SkGeneratorService
{
    /**
     * Generate SK document from template and save it
     *
     * @param PengajuanKgb $pengajuanKgb
     * @param SkTemplate|null $template
     * @return string Path to the generated SK file
     */
    public function generateSkDocument(PengajuanKgb $pengajuanKgb, ?SkTemplate $template = null): string
    {
        // Use provided template or find the default active template for this tenant
        if (!$template) {
            $template = SkTemplate::where('tenant_id', $pengajuanKgb->tenant_id)
                ->where('jenis_pengajuan', $pengajuanKgb->jenis_pengajuan ?? 'kenaikan_gaji_berkala')
                ->where('is_active', true)
                ->first();
                
            if (!$template) {
                throw new \Exception('Template SK tidak ditemukan untuk dinas ini');
            }
        }

        // Fill template with data from Pegawai and PengajuanKgb
        $pegawai = $pengajuanKgb->pegawai;
        if (!$pegawai) {
            throw new \Exception('Data pegawai tidak ditemukan untuk pengajuan ini');
        }

        $content = $template->content;
        $content = $this->replacePlaceholders($content, $pegawai, $pengajuanKgb);

        // Generate PDF from the filled template
        $pdf = Pdf::loadHTML($content)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Arial, sans-serif',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);

        // Save the generated PDF
        $filename = 'sk_kgb_' . $pegawai->nip . '_' . date('Y-m-d_H-i-s') . '.pdf';
        $path = 'sk-generated/' . date('Y/m/d') . '/' . $filename;

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Replace placeholders in template with actual data
     *
     * @param string $templateContent
     * @param Pegawai $pegawai
     * @param PengajuanKgb $pengajuanKgb
     * @return string
     */
    protected function replacePlaceholders(string $templateContent, Pegawai $pegawai, PengajuanKgb $pengajuanKgb): string
    {
        $replacements = [
            '{nomor_sk}' => $pengajuanKgb->no_sk ?? '[Nomor SK]',
            '{tanggal_sk}' => $pengajuanKgb->tanggal_sk ? $pengajuanKgb->tanggal_sk->format('d F Y') : '[Tanggal SK]',
            '{nip}' => $pegawai->nip ?? '[NIP]',
            '{nama_pegawai}' => $pegawai->name ?? '[Nama Pegawai]',
            '{pangkat_golongan}' => $pegawai->pangkat . ' (' . $pegawai->golongan . ')' ?? '[Pangkat/Golongan]',
            '{jabatan}' => $pegawai->jabatan ?? '[Jabatan]',
            '{unit_kerja}' => $pegawai->unit_kerja ?? '[Unit Kerja]',
            '{tmt_kgb_baru}' => $pengajuanKgb->tmt_kgb_baru ? $pengajuanKgb->tmt_kgb_baru->format('d F Y') : '[TMT KGB Baru]',
            '{masa_kerja}' => $pegawai->masa_kerja ?? '[Masa Kerja]',
            '{tanggal_lahir}' => $pegawai->tanggal_lahir ? $pegawai->tanggal_lahir->format('d F Y') : '[Tanggal Lahir]',
            '{tempat_lahir}' => $pegawai->tempat_lahir ?? '[Tempat Lahir]',
            '{nama_dinas}' => $pegawai->tenant->name ?? '[Nama Dinas]',
            '{alamat_dinas}' => $pegawai->tenant->alamat ?? '[Alamat Dinas]',
            '{nama_kepala_dinas}' => $pegawai->tenant->nama_kepala ?? '[Nama Kepala Dinas]',
            '{nip_kepala_dinas}' => $pegawai->tenant->nip_kepala ?? '[NIP Kepala Dinas]',
        ];

        $content = Str::of($templateContent);

        foreach ($replacements as $placeholder => $value) {
            $content = $content->replace($placeholder, $value);
        }

        return $content->toString();
    }

    /**
     * Create SK record after generating the document
     *
     * @param PengajuanKgb $pengajuanKgb
     * @param string $filePath
     * @return SkKgb
     */
    public function createSkRecord(PengajuanKgb $pengajuanKgb, string $filePath): SkKgb
    {
        $skRecord = SkKgb::create([
            'tenant_id' => $pengajuanKgb->tenant_id,
            'pegawai_id' => $pengajuanKgb->pegawai_id,
            'pengajuan_kgb_id' => $pengajuanKgb->id,
            'no_sk' => $pengajuanKgb->no_sk,
            'tanggal_sk' => $pengajuanKgb->tanggal_sk,
            'file_path' => $filePath,
            'tanggal_efektif' => $pengajuanKgb->tmt_kgb_baru,
            'status' => 'aktif',
        ]);

        // Update pegawai data after SK generation
        $this->updatePegawaiAfterApproval($pengajuanKgb);

        return $skRecord;
    }

    /**
     * Update pegawai data after KGB approval
     *
     * @param PengajuanKgb $pengajuanKgb
     * @return void
     */
    protected function updatePegawaiAfterApproval(PengajuanKgb $pengajuanKgb): void
    {
        if (!$pengajuanKgb->pegawai) {
            return;
        }

        // Calculate the next KGB date (usually 2 years after the new effective date)
        $nextKgbDate = $pengajuanKgb->tmt_kgb_baru->copy()->addYears(2);

        $pengajuanKgb->pegawai->update([
            'tmt_kgb_terakhir' => $pengajuanKgb->tmt_kgb_baru,
            'tmt_kgb_berikutnya' => $nextKgbDate,
        ]);
    }
}