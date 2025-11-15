<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->maxSize(10240),
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('path_file')
                    ->label('File')
                    ->url(fn($record) => asset('storage/' . ltrim($record->path_file,'/')), true)
                    ->openUrlInNewTab()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status_verifikasi')
                    ->label('Status Verifikasi')
                    ->enum([
                        'belum_diperiksa' => 'Belum Diperiksa',
                        'valid' => 'Valid',
                        'tidak_valid' => 'Tidak Valid',
                        'revisi' => 'Revisi',
                    ])
                    ->colors([
                        'gray' => 'belum_diperiksa',
                        'success' => 'valid',
                        'danger' => 'tidak_valid',
                        'warning' => 'revisi',
                    ]),
                Tables\Columns\TextColumn::make('catatan_verifikasi')
                    ->label('Catatan')
                    ->wrap(),
            ]);
    }
}
