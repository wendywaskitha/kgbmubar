<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Pegawai;

class ReminderEligibleKGB extends Notification
{
    use Queueable;

    protected $pegawai;

    /**
     * Create a new notification instance.
     */
    public function __construct(Pegawai $pegawai)
    {
        $this->pegawai = $pegawai;
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
            ->subject('Anda Sudah Eligible untuk KGB')
            ->greeting('Hallo ' . $notifiable->name . ',')
            ->line('Anda sudah memenuhi syarat 2 tahun untuk mengajukan KGB.')
            ->line('Karyawan atas nama ' . $this->pegawai->nama . ' (NIP: ' . $this->pegawai->nip . ') sudah eligible untuk mengajukan KGB.')
            ->action('Ajukan Sekarang', url('/pegawai/pengajuan-kgb/create'))
            ->line('Silakan ajukan KGB sesuai dengan prosedur yang berlaku.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => '⏰ Anda Sudah Eligible untuk KGB',
            'body' => 'Anda sudah memenuhi syarat 2 tahun untuk mengajukan KGB',
            'icon' => 'bell',
            'color' => 'info',
            'pegawai_id' => $this->pegawai->id,
            'pegawai_nama' => $this->pegawai->nama,
            'pegawai_nip' => $this->pegawai->nip,
        ];
    }
}