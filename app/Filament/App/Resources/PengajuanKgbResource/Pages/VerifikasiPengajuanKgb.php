<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\Pages;

use App\Filament\App\Resources\PengajuanKgbResource;
use App\Filament\App\Resources\PengajuanKgbResource\RelationManagers\DokumenPengajuanRelationManager;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Pages\Concerns\InteractsWithHeaderActions;
use Illuminate\Support\Facades\Auth;

class VerifikasiPengajuanKgb extends Page
{
    use InteractsWithRecord;
    use InteractsWithHeaderActions;

    protected static string $resource = PengajuanKgbResource::class;

    protected static string $view = 'filament.app.resources.pengajuan-kgb-resource.pages.verifikasi-pengajuan-kgb';

    protected static ?string $title = 'Verifikasi Dokumen KGB';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        static::authorizeResourceAccess();
    }

    // Hilangkan semua widgets atas
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    public function getRelationManagers(): array
    {
        return [
            DokumenPengajuanRelationManager::class,
        ];
    }
    
    public function getWidgets(): array
    {
        return $this->getHeaderWidgets();
    }
}
