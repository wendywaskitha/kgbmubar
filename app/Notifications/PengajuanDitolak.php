<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PengajuanKgb;

class PengajuanDitolak extends Notification
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
            ->subject('Pengajuan KGB Ditolak')
            ->greeting('Hallo ' . $notifiable->name . ',')
            ->line('Pengajuan KGB atas nama ' . $this->pengajuan->pegawai?->nama . ' - ' . $this->pengajuan->pegawai?->nip . ' telah ditolak.')
            ->line('Alasan penolakan: ' . $this->pengajuan->catatan ?? 'Tidak disebutkan')
            ->action('Lihat Detail Pengajuan', url('/app/pengajuan-kgb/' . $this->pengajuan->id . '/edit'))
            ->line('Silakan ajukan kembali jika masih eligible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => '❌ Pengajuan Ditolak',
            'body' => 'Pengajuan KGB ' . $this->pengajuan->pegawai?->nama . ' - ' . $this->pengajuan->pegawai?->nip . ' ditolak. Klik untuk lihat alasan.',
            'icon' => 'x-circle',
            'color' => 'danger',
            'pengajuan_id' => $this->pengajuan->id,
            'pegawai_nama' => $this->pengajuan->pegawai?->nama,
            'pegawai_nip' => $this->pengajuan->pegawai?->nip,
            'catatan' => $this->pengajuan->catatan ?? 'Tidak disebutkan',
        ];
    }
}