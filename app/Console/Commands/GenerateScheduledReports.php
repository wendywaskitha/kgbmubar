<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExportService;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateScheduledReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-scheduled
                            {--type=monthly : Type of report to generate (daily, weekly, monthly)}
                            {--send-email : Send the report via email}
                            {--tenant= : Specific tenant ID to generate report for (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate scheduled reports for dinas';

    /**
     * Execute the console command.
     */
    public function handle(ExportService $exportService)
    {
        $this->info('Starting scheduled report generation...');

        $reportType = $this->option('type');
        $sendEmail = $this->option('send-email');
        $specificTenant = $this->option('tenant');

        if ($specificTenant) {
            $tenants = Tenant::where('id', $specificTenant)->get();
        } else {
            $tenants = Tenant::all();
        }

        foreach ($tenants as $tenant) {
            $this->info("Generating report for tenant: {$tenant->nama}");

            // Define filters based on report type
            $filters = $this->getFiltersForReportType($reportType);

            try {
                // Generate Excel report
                $excelPath = $exportService->exportPengajuanToExcel(array_merge($filters, ['tenant_id' => $tenant->id]));

                if ($sendEmail) {
                    // Find admin users for the tenant to send reports
                    $adminUsers = User::where('tenant_id', $tenant->id)
                        ->whereHas('roles', function ($query) {
                            $query->where('name', 'admin_dinas')
                                  ->orWhere('name', 'super_admin_dinas');
                        })
                        ->get();

                    foreach ($adminUsers as $user) {
                        $this->sendReportEmail($user, $excelPath, $reportType, $tenant);
                    }
                }

                $this->info("Report generated for tenant: {$tenant->nama}");
            } catch (\Exception $e) {
                $this->error("Error generating report for tenant {$tenant->nama}: " . $e->getMessage());
            }
        }

        $this->info('Scheduled report generation completed.');
    }

    /**
     * Get filters based on report type
     */
    private function getFiltersForReportType(string $type): array
    {
        switch ($type) {
            case 'daily':
                return [
                    'date_from' => now()->startOfDay(),
                    'date_to' => now()->endOfDay(),
                ];
            case 'weekly':
                return [
                    'date_from' => now()->startOfWeek(),
                    'date_to' => now()->endOfWeek(),
                ];
            case 'monthly':
            default:
                return [
                    'date_from' => now()->startOfMonth(),
                    'date_to' => now()->endOfMonth(),
                ];
        }
    }

    /**
     * Send report via email
     */
    private function sendReportEmail(User $user, string $filePath, string $reportType, Tenant $tenant): void
    {
        $reportTypeText = ucfirst($reportType);

        Mail::raw("Laporan KGB {$reportTypeText} untuk {$tenant->nama} telah tersedia.", function ($message) use ($user, $filePath, $reportTypeText, $tenant) {
            $message->to($user->email)
                    ->subject("Laporan KGB {$reportTypeText} - {$tenant->nama}")
                    ->attach($filePath);
        });
    }
}
