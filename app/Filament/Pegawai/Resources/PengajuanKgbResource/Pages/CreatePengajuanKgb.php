<?php

namespace App\Filament\Pegawai\Resources\PengajuanKgbResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Pegawai\Resources\PengajuanKgbResource;

class CreatePengajuanKgb extends CreateRecord
{
    protected static string $resource = PengajuanKgbResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Paksa assign user login
        $data['user_pengaju_id'] = Auth::id();
        return $data;
    }
}
