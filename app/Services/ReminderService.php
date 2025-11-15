<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\User;
use App\Notifications\ReminderEligibleKGB;

class ReminderService
{
    /**
     * Send reminders to eligible pegawai
     */
    public function sendEligibleKGBReminders(): void
    {
        // Get all pegawai eligible for KGB (2 years since last KGB)
        $eligiblePegawai = $this->getEligiblePegawai();

        foreach ($eligiblePegawai as $pegawai) {
            // Find the user account associated with this pegawai
            $user = $pegawai->user;

            if ($user) {
                // Send notification to the user
                $user->notify(new ReminderEligibleKGB($pegawai));
            }
        }
    }

    /**
     * Get pegawai eligible for KGB (2 years since last KGB)
     */
    public function getEligiblePegawai(): \Illuminate\Support\Collection
    {
        // Calculate date 2 years ago
        $twoYearsAgo = now()->subYears(2);

        // Get pegawai who are eligible for KGB
        $eligiblePegawai = Pegawai::where('tmt_kgb_terakhir', '<=', $twoYearsAgo)
            ->whereDoesntHave('pengajuanKgbs', function ($query) {
                // Exclude pegawai who already have an active pengajuan
                $query->whereIn('status', ['draft', 'diajukan', 'verifikasi_dinas', 'verifikasi_kabupaten']);
            })
            ->get();

        return $eligiblePegawai;
    }

    /**
     * Send reminder to specific pegawai
     */
    public function sendReminderToPegawai(Pegawai $pegawai): void
    {
        $user = $pegawai->user;

        if ($user) {
            $user->notify(new ReminderEligibleKGB($pegawai));
        }
    }

    /**
     * Get pegawai eligible for KGB for specific tenant
     */
    public function getEligiblePegawaiForTenant(int $tenantId): \Illuminate\Support\Collection
    {
        // Calculate date 2 years ago
        $twoYearsAgo = now()->subYears(2);

        // Get pegawai who are eligible for KGB for specific tenant
        $eligiblePegawai = Pegawai::where('tenant_id', $tenantId)
            ->where('tmt_kgb_terakhir', '<=', $twoYearsAgo)
            ->whereDoesntHave('pengajuanKgbs', function ($query) {
                // Exclude pegawai who already have an active pengajuan
                $query->whereIn('status', ['draft', 'diajukan', 'verifikasi_dinas', 'verifikasi_kabupaten']);
            })
            ->get();

        return $eligiblePegawai;
    }

    /**
     * Send reminders to eligible pegawai for specific tenant
     */
    public function sendEligibleKGBRemindersForTenant(int $tenantId): void
    {
        // Get all pegawai eligible for KGB for specific tenant
        $eligiblePegawai = $this->getEligiblePegawaiForTenant($tenantId);

        foreach ($eligiblePegawai as $pegawai) {
            // Find the user account associated with this pegawai
            $user = $pegawai->user;

            if ($user) {
                // Send notification to the user
                $user->notify(new ReminderEligibleKGB($pegawai));
            }
        }
    }
}