<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\RelationManagers;

use App\Models\DokumenPengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class DokumenPengajuanRelationManager extends RelationManager
{
    protected static string $relationship = 'dokumenPengajuans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_file')
                    ->label('Nama Dokumen')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('jenis_dokumen')
                    ->label('Jenis Dokumen')
                    ->options([
                        'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                        'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                        'daftar_gaji' => 'Daftar Gaji Terakhir',
                        'ktp' => 'KTP',
                        'npwp' => 'NPWP',
                        'karpeg' => 'Kartu Pegawai',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required()
                    ->searchable(),

                Forms\Components\FileUpload::make('path_file')
                    ->label('File Dokumen')
                    ->directory('dokumen-pengajuan')
                    ->disk('public')
                    ->visibility('private')
                    ->required()
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(10240), // 10MB max

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_file')
                    ->label('Nama Dokumen')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_dokumen')
                    ->label('Jenis Dokumen')
                    ->formatStateUsing(function ($state) {
                        $jenisLabels = [
                            'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                            'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                            'daftar_gaji' => 'Daftar Gaji Terakhir',
                            'ktp' => 'KTP',
                            'npwp' => 'NPWP',
                            'karpeg' => 'Kartu Pegawai',
                            'lainnya' => 'Lainnya',
                        ];

                        return $jenisLabels[$state] ?? $state;
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('path_file')
                    ->label('File')
                    ->formatStateUsing(function ($record) {
                        return 'File: ' . basename($record->path_file);
                    })
                    ->url(function ($record) {
                        return Storage::url($record->path_file);
                    }, true) // Opens in new tab
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_dokumen')
                    ->label('Jenis Dokumen')
                    ->options([
                        'sk_pangkat_terakhir' => 'SK Pangkat Terakhir',
                        'sk_jabatan_terakhir' => 'SK Jabatan Terakhir',
                        'daftar_gaji' => 'Daftar Gaji Terakhir',
                        'ktp' => 'KTP',
                        'npwp' => 'NPWP',
                        'karpeg' => 'Kartu Pegawai',
                        'lainnya' => 'Lainnya',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
