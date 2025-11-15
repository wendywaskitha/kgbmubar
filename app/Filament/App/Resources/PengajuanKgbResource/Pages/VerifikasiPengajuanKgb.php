<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\Pages;

use App\Filament\App\Resources\PengajuanKgbResource;
use Filament\Resources\Pages\Page;

class VerifikasiPengajuanKgb extends Page
{
    protected static string $resource = PengajuanKgbResource::class;

    protected static string $view = 'filament.app.resources.pengajuan-kgb-resource.pages.verifikasi-pengajuan-kgb';

    protected static ?string $title = 'Verifikasi Dokumen KGB';

    public $record;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
    }

    protected function resolveRecord(int | string $key): \Illuminate\Database\Eloquent\Model
    {
        return static::getResource()::resolveRecordRouteBinding($key);
    }
}
