<?php

namespace App\Filament\Pegawai\Resources\PengajuanKgbResource\Pages;

use App\Filament\Pegawai\Resources\PengajuanKgbResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use App\Models\User;
use Filament\Notifications\Notification;

class ListPengajuanKgbs extends ListRecords
{
    protected static string $resource = PengajuanKgbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make(),
            Tables\Actions\ViewAction::make(),
            Tables\Actions\Action::make('ajukan')
                ->label('Ajukan')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'draft')
                ->requiresConfirmation()
                ->modalHeading('Ajukan Pengajuan KGB')
                ->modalDescription('Pastikan semua data dan dokumen sudah benar. Anda yakin ingin mengajukan KGB ini ke admin dinas? Setelah diajukan, data tidak dapat diedit.')
                ->modalSubmitActionLabel('Ajukan Sekarang')
                ->action(function ($record) {
                    $record->update([
                        'status' => 'diajukan',
                        'tanggal_pengajuan' => now(),
                    ]);

                    // Send notification to admin_dinas, verifikator_dinas, operator_dinas in tenant
                    $targetRoles = ['admin_dinas', 'verifikator_dinas', 'operator_dinas'];
                    $user = auth()->user();
                    $pegawai = $user->pegawai;
                    $appRecipients = User::whereIn('role', $targetRoles)
                        ->where('tenant_id', $user->tenant_id)
                        ->get();

                    foreach ($appRecipients as $recipient) {
                        Notification::make()
                            ->title('Pengajuan KGB Baru')
                            ->body('Pengajuan KGB baru diajukan oleh ' . $pegawai->name . ' pada ' . now()->format('d M Y H:i'))
                            ->icon('heroicon-o-document-text')
                            ->success()
                            ->sendToDatabase($recipient);
                    }

                    Notification::make()
                        ->success()
                        ->title('Pengajuan Telah Diajukan')
                        ->body('Pengajuan KGB Anda telah dikirim ke admin dinas.')
                        ->send();
                }),
        ];
    }
}
