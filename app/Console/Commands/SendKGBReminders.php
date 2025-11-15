<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReminderService;

class SendKGBReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-kgb-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send KGB eligibility reminders to eligible pegawai';

    /**
     * Execute the console command.
     */
    public function handle(ReminderService $reminderService)
    {
        $this->info('Sending KGB eligibility reminders...');

        $reminderService->sendEligibleKGBReminders();

        $this->info('KGB eligibility reminders sent successfully.');
    }
}
