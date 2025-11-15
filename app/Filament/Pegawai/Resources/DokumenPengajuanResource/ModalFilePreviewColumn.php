<?php

namespace App\Filament\Pegawai\Resources\DokumenPengajuanResource;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables; // for Table context
use Illuminate\Support\Facades\Storage;

class ModalFilePreviewColumn
{
    public static function makePreviewColumn()
    {
        return ViewColumn::make('Preview')->view('filament.columns.preview-dokumen')
            ->label('Preview');
    }

    public static function makeFileColumn()
    {
        return TextColumn::make('path_file')
            ->label('File')
            ->formatStateUsing(function ($record) {
                return 'File: ' . basename($record->path_file);
            })
            ->action(
                Tables\Actions\Action::make('previewFile')
                    ->icon('heroicon-o-eye')
                    ->label('Preview')
                    ->modalHeading('Preview Dokumen')
                    ->modalContent(function ($record) {
                        $url = Storage::disk('public')->url($record->path_file);
                        $fileExt = strtolower(pathinfo($url, PATHINFO_EXTENSION));
                        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                            return view('filament.columns.modal-preview-image', compact('url'));
                        } elseif ($fileExt === 'pdf') {
                            return view('filament.columns.modal-preview-pdf', compact('url'));
                        }
                        return 'Preview tidak tersedia.';
                    })
            );
    }
}
