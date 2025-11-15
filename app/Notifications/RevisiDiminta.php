<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PengajuanKgb;

class RevisiDiminta extends Notification
{
    use Queueable;

    protected $pengajuan;

    /**
     * Create a new notification instance.
     */
    public function __construct(PengajuanKgb $pengajuan)
    {
        $this->pengajuan = $pengajuan;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Revisi Dokumen Diperlukan')
            ->greeting('Hallo ' . $notifiable->name . ',')
            ->line('Pengajuan KGB atas nama ' . $this->pengajuan->pegawai?->nama . ' - ' . $this->pengajuan->pegawai?->nip . ' memerlukan perbaikan dokumen.')
            ->line('Catatan dari verifikator: ' . $this->pengajuan->catatan_verifikasi_dinas ?? 'Tidak disebutkan')
            ->action('Upload Revisi', url('/pegawai/pengajuan-kgb/' . $this->pengajuan->id . '/edit'))
            ->line('Silakan upload ulang dokumen yang bermasalah sesuai dengan catatan verifikator.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => '⚠️ Revisi Dokumen Diperlukan',
            'body' => 'Pengajuan KGB ' . $this->pengajuan->pegawai?->nama . ' - ' . $this->pengajuan->pegawai?->nip . ' memerlukan perbaikan dokumen',
            'icon' => 'exclamation-triangle',
            'color' => 'warning',
            'pengajuan_id' => $this->pengajuan->id,
            'pegawai_nama' => $this->pengajuan->pegawai?->nama,
            'pegawai_nip' => $this->pengajuan->pegawai?->nip,
            'catatan_verifikasi' => $this->pengajuan->catatan_verifikasi_dinas ?? 'Tidak disebutkan',
        ];
    }
}