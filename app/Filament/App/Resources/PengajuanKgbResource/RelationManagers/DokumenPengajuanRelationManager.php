<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\RelationManagers;

use App\Models\DokumenPengajuan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
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
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status_verifikasi')
                    ->label('Status Verifikasi')
                    ->colors([
                        'gray' => 'Belum Diperiksa',
                        'success' => 'valid',
                        'danger' => 'tidak_valid',
                        'warning' => 'revisi',
                    ])
                    ->enum([
                        'belum_diperiksa' => 'Belum Diperiksa',
                        'valid' => 'Valid',
                        'tidak_valid' => 'Tidak Valid',
                        'revisi' => 'Revisi',
                    ]),
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Preview Dokumen')
                    ->modalContent(function ($record) {
                        $fileUrl = asset('storage/' . ltrim($record->path_file, '/'));
                        $extension = strtolower(pathinfo($record->path_file, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
                        if ($isImage) {
                            return view('filament.app.resources.pengajuan-kgb-resource.partials.preview-image', [
                                'fileUrl' => $fileUrl,
                                'fileName' => $record->nama_file,
                            ]);
                        } else {
                            return view('filament.app.resources.pengajuan-kgb-resource.partials.preview-pdf', [
                                'fileUrl' => $fileUrl,
                                'fileName' => $record->nama_file,
                            ]);
                        }
                    })
                    ->modalWidth('7xl'),
                Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-badge-check')
                    ->form([
                        Forms\Components\Select::make('status_verifikasi')
                            ->label('Status Verifikasi')
                            ->options([
                                'valid' => 'Valid',
                                'tidak_valid' => 'Tidak Valid',
                                'revisi' => 'Revisi',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('catatan_verifikasi')
                            ->label('Catatan (opsional)')
                            ->maxLength(255),
                    ])
                    ->action(function ($record, $data) {
                        $record->status_verifikasi = $data['status_verifikasi'];
                        $record->catatan_verifikasi = $data['catatan_verifikasi'];
                        $record->tanggal_verifikasi = now();
                        $record->save();
                    })
                    ->color('success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
