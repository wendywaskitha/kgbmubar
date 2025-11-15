<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PengajuanKgb;

class SKTersedia extends Notification
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
            ->subject('SK KGB Tersedia untuk Diunduh')
            ->greeting('Hallo ' . $notifiable->name . ',')
            ->line('SK KGB atas nama ' . $this->pengajuan->pegawai?->nama . ' - ' . $this->pengajuan->pegawai?->nip . ' sudah tersedia.')
            ->line('Nomor SK: ' . $this->pengajuan->no_sk)
            ->action('Download SK', url('/pegawai/sk/' . $this->pengajuan->id . '/download'))
            ->line('Terima kasih telah menggunakan layanan kami.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => '📄 SK KGB Siap Diunduh',
            'body' => 'SK KGB ' . $this->pengajuan->pegawai?->nama . ' - ' . $this->pengajuan->pegawai?->nip . ' sudah tersedia dengan No SK: ' . $this->pengajuan->no_sk,
            'icon' => 'document-download',
            'color' => 'primary',
            'pengajuan_id' => $this->pengajuan->id,
            'pegawai_nama' => $this->pengajuan->pegawai?->nama,
            'pegawai_nip' => $this->pengajuan->pegawai?->nip,
            'no_sk' => $this->pengajuan->no_sk,
        ];
    }
}