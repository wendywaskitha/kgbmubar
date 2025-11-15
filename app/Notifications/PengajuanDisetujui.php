<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PengajuanKgb;

class PengajuanDisetujui extends Notification
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
            ->subject('Pengajuan KGB Disetujui!')
            ->greeting('Hallo ' . $notifiable->name . ',')
            ->line('Selamat! KGB atas nama ' . $this->pengajuan->pegawai?->nama . ' - ' . $this->pengajuan->pegawai?->nip . ' telah disetujui.')
            ->line('Nomor SK: ' . $this->pengajuan->no_sk)
            ->action('Lihat SK', url('/pegawai/sk/' . $this->pengajuan->id . '/download'))
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
            'title' => '🎉 Pengajuan KGB Disetujui!',
            'body' => 'Selamat! KGB ' . $this->pengajuan->pegawai?->nama . ' - ' . $this->pengajuan->pegawai?->nip . ' telah disetujui dengan No SK: ' . $this->pengajuan->no_sk,
            'icon' => 'check-circle',
            'color' => 'success',
            'pengajuan_id' => $this->pengajuan->id,
            'pegawai_nama' => $this->pengajuan->pegawai?->nama,
            'pegawai_nip' => $this->pengajuan->pegawai?->nip,
            'no_sk' => $this->pengajuan->no_sk,
        ];
    }
}
