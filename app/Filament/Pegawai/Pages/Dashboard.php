<?php

namespace App\Filament\Pegawai\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pegawai.pages.dashboard';
    
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?int $navigationSort = 1;
    
    public static function getNavigationLabel(): string
    {
        return 'Dasbor';
    }
}