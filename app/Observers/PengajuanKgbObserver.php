<?php

namespace App\Observers;

use App\Models\PengajuanKgb;
use App\Models\User;
use App\Notifications\PengajuanBaru;
use App\Notifications\VerifikasiSelesai;
use App\Notifications\PengajuanDitolak;
use App\Notifications\PengajuanDisetujui;
use App\Notifications\SKTersedia;

class PengajuanKgbObserver
{
    /**
     * Handle the PengajuanKgb "created" event.
     */
    public function created(PengajuanKgb $pengajuanKgb): void
    {
        // Only send notification if status is 'diajukan'
        if ($pengajuanKgb->status === 'diajukan') {
            $this->sendPengajuanBaruNotification($pengajuanKgb);
        }
    }

    /**
     * Handle the PengajuanKgb "updated" event.
     */
    public function updated(PengajuanKgb $pengajuanKgb): void
    {
        // Check if status has changed
        if ($pengajuanKgb->isDirty('status')) {
            $oldStatus = $pengajuanKgb->getOriginal('status');
            $newStatus = $pengajuanKgb->status;

            // Handle specific status transitions
            if ($oldStatus !== $newStatus) {
                $this->handleStatusChange($pengajuanKgb, $oldStatus, $newStatus);
            }
        }
    }

    /**
     * Handle the PengajuanKgb "deleted" event.
     */
    public function deleted(PengajuanKgb $pengajuanKgb): void
    {
        //
    }

    /**
     * Handle the PengajuanKgb "restored" event.
     */
    public function restored(PengajuanKgb $pengajuanKgb): void
    {
        //
    }

    /**
     * Handle the PengajuanKgb "force deleted" event.
     */
    public function forceDeleted(PengajuanKgb $pengajuanKgb): void
    {
        //
    }

    /**
     * Send notification when pengajuan status changes
     */
    private function handleStatusChange(PengajuanKgb $pengajuanKgb, string $oldStatus, string $newStatus): void
    {
        switch ($newStatus) {
            case 'diajukan':
                // Only send if it was a draft (not when status changes from other values to diajukan)
                if ($oldStatus === 'draft') {
                    $this->sendPengajuanBaruNotification($pengajuanKgb);
                }
                break;

            case 'verifikasi_dinas':
                // Notification for verifikasi dinas
                $this->sendVerifikasiSelesaiNotification($pengajuanKgb);
                break;

            case 'verifikasi_kabupaten':
                // Continue processing to kabupaten
                break;

            case 'disetujui':
                $this->sendPengajuanDisetujuiNotification($pengajuanKgb);
                break;

            case 'ditolak':
            case 'ditolak_dinas':
            case 'ditolak_kabupaten':
                $this->sendPengajuanDitolakNotification($pengajuanKgb);
                break;

            case 'selesai':
                $this->sendSKTersediaNotification($pengajuanKgb);
                break;
        }
    }

    /**
     * Send PengajuanBaru notification
     */
    private function sendPengajuanBaruNotification(PengajuanKgb $pengajuanKgb): void
    {
        // Get verifikator dinas for this tenant
        $verifikatorDinas = User::where('tenant_id', $pengajuanKgb->tenant_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'verifikator_dinas');
            })
            ->get();

        // Get admin dinas for this tenant
        $adminDinas = User::where('tenant_id', $pengajuanKgb->tenant_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin_dinas');
            })
            ->get();

        // Notify verifikator and admin dinas
        foreach ($verifikatorDinas as $user) {
            $user->notify(new PengajuanBaru($pengajuanKgb));
        }

        foreach ($adminDinas as $user) {
            $user->notify(new PengajuanBaru($pengajuanKgb));
        }
    }

    /**
     * Send VerifikasiSelesai notification
     */
    private function sendVerifikasiSelesaiNotification(PengajuanKgb $pengajuanKgb): void
    {
        // Get verifikator kabupaten
        $verifikatorKabupaten = User::whereHas('roles', function ($query) {
            $query->where('name', 'verifikator_kabupaten');
        })
        ->where('tenant_id', $pengajuanKgb->tenant_id) // Note: tenant_id may not be relevant for kabupaten role
        ->get();

        // Get original user who made the pengajuan (if it was self-service)
        $pengaju = $pengajuanKgb->pegawai?->user; // Assuming pegawai has a user relationship

        // Also notify admin if the pengajuan was made by admin
        $adminPengaju = $pengajuanKgb->userPengaju;

        foreach ($verifikatorKabupaten as $user) {
            $user->notify(new VerifikasiSelesai($pengajuanKgb));
        }

        // Notify the pegawai if they have an account
        if ($pengaju) {
            $pengaju->notify(new VerifikasiSelesai($pengajuanKgb));
        }

        // Notify the admin who made the pengajuan
        if ($adminPengaju) {
            $adminPengaju->notify(new VerifikasiSelesai($pengajuanKgb));
        }
    }

    /**
     * Send PengajuanDisetujui notification
     */
    private function sendPengajuanDisetujuiNotification(PengajuanKgb $pengajuanKgb): void
    {
        // Notify admin dinas
        $adminDinas = User::where('tenant_id', $pengajuanKgb->tenant_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin_dinas');
            })
            ->get();

        // Notify the pegawai if they have an account
        $pegawaiUser = $pengajuanKgb->pegawai?->user;

        foreach ($adminDinas as $user) {
            $user->notify(new PengajuanDisetujui($pengajuanKgb));
        }

        if ($pegawaiUser) {
            $pegawaiUser->notify(new PengajuanDisetujui($pengajuanKgb));
        }
    }

    /**
     * Send PengajuanDitolak notification
     */
    private function sendPengajuanDitolakNotification(PengajuanKgb $pengajuanKgb): void
    {
        // Notify admin dinas
        $adminDinas = User::where('tenant_id', $pengajuanKgb->tenant_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin_dinas');
            })
            ->get();

        // Notify the pegawai if they have an account
        $pegawaiUser = $pengajuanKgb->pegawai?->user;

        // Also notify the user who made the pengajuan
        $pengajuUser = $pengajuanKgb->userPengaju;

        foreach ($adminDinas as $user) {
            $user->notify(new PengajuanDitolak($pengajuanKgb));
        }

        if ($pegawaiUser) {
            $pegawaiUser->notify(new PengajuanDitolak($pengajuanKgb));
        }

        if ($pengajuUser && $pengajuUser->id !== ($pegawaiUser?->id)) {
            $pengajuUser->notify(new PengajuanDitolak($pengajuanKgb));
        }
    }

    /**
     * Send SKTersedia notification
     */
    private function sendSKTersediaNotification(PengajuanKgb $pengajuanKgb): void
    {
        // Notify admin dinas
        $adminDinas = User::where('tenant_id', $pengajuanKgb->tenant_id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin_dinas');
            })
            ->get();

        // Notify the pegawai if they have an account
        $pegawaiUser = $pengajuanKgb->pegawai?->user;

        foreach ($adminDinas as $user) {
            $user->notify(new SKTersedia($pengajuanKgb));
        }

        if ($pegawaiUser) {
            $pegawaiUser->notify(new SKTersedia($pengajuanKgb));
        }
    }
}
