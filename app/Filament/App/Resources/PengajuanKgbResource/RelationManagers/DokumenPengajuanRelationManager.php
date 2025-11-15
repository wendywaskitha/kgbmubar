<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\DokumenPengajuan;
use Filament\Support\Enums\IconSize;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\RelationManagers\RelationManager;

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
                // Tables\Columns\TextColumn::make('path_file')
                //     ->label('File')
                //     ->formatStateUsing(function ($record) {
                //         return 'File: ' . basename($record->path_file);
                //     })
                //     ->searchable(),
                Tables\Columns\TextColumn::make('status_verifikasi')
                    ->label('Status Verifikasi')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'belum_diperiksa' => 'Belum Diperiksa',
                            'valid' => 'Valid',
                            'tidak_valid' => 'Tidak Valid',
                            'revisi' => 'Revisi',
                            default => $state,
                        };
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'belum_diperiksa' => 'heroicon-m-x-circle',
                        'valid' => 'heroicon-m-check-circle',
                        'tidak_valid' => 'heroicon-m-exclamation-circle',
                        'revisi' => 'heroicon-m-arrow-path',
                        default => 'heroicon-m-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'belum_diperiksa' => 'gray',
                        'valid' => 'success',
                        'tidak_valid' => 'danger',
                        'revisi' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('catatan_verifikasi')
                    ->label('Catatan')
                    ->wrap()
                    ->limit(50),
                Tables\Columns\TextColumn::make('verifikator.name')
                    ->label('Verifikator'),
                Tables\Columns\TextColumn::make('tanggal_verifikasi')
                    ->label('Tanggal Verifikasi')
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_verifikasi')
                    ->label('Status Verifikasi')
                    ->options([
                        'belum_diperiksa' => 'Belum Diperiksa',
                        'valid' => 'Valid',
                        'tidak_valid' => 'Tidak Valid',
                        'revisi' => 'Revisi',
                    ]),
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
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalWidth('7xl')
                    ->modalContent(function (DokumenPengajuan $record) {
                        $extension = pathinfo($record->path_file, PATHINFO_EXTENSION);
                        $url = Storage::url($record->path_file);

                        if (in_array($extension, ['pdf'])) {
                            return view('filament.components.pdf-preview', ['url' => $url]);
                        } elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            return view('filament.components.image-preview', ['url' => $url]);
                        } else {
                            return view('filament.components.file-preview', [
                                'url' => $url,
                                'fileName' => $record->nama_file
                            ]);
                        }
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->visible(fn (DokumenPengajuan $record) => !empty($record->path_file)),
                Tables\Actions\Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->modalHeading('Verifikasi Dokumen')
                    ->modalWidth('md')
                    ->form([
                        Forms\Components\Select::make('status_verifikasi')
                            ->label('Status Verifikasi')
                            ->options([
                                'valid' => 'Valid',
                                'tidak_valid' => 'Tidak Valid',
                                'revisi' => 'Perlu Revisi',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\Textarea::make('catatan_verifikasi')
                            ->label('Catatan Verifikasi')
                            ->placeholder('Tambahkan catatan verifikasi (opsional)')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->action(function (DokumenPengajuan $record, array $data) {
                        $record->update([
                            'status_verifikasi' => $data['status_verifikasi'],
                            'catatan_verifikasi' => $data['catatan_verifikasi'] ?? null,
                            'verifikator_id' => Auth::id(),
                            'tanggal_verifikasi' => now(),
                        ]);
                    })
                    ->successNotificationTitle('Dokumen berhasil diverifikasi')
                    ->visible(fn (DokumenPengajuan $record) => in_array(Auth::user()->role, ['admin_dinas', 'verifikator_dinas'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('verifikasi_massal')
                        ->label('Verifikasi Massal')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->modalHeading('Verifikasi Massal')
                        ->modalWidth('md')
                        ->form([
                            Forms\Components\Select::make('status_verifikasi')
                                ->label('Status Verifikasi untuk Semua')
                                ->options([
                                    'valid' => 'Valid',
                                    'tidak_valid' => 'Tidak Valid',
                                    'revisi' => 'Perlu Revisi',
                                ])
                                ->required()
                                ->live(),
                            Forms\Components\Textarea::make('catatan_verifikasi')
                                ->label('Catatan Umum (akan ditambahkan ke semua dokumen)')
                                ->placeholder('Tambahkan catatan umum (opsional)')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->action(function (array $data, \Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(function (DokumenPengajuan $record) use ($data) {
                                $record->update([
                                    'status_verifikasi' => $data['status_verifikasi'],
                                    'catatan_verifikasi' => $data['catatan_verifikasi'] ?? $record->catatan_verifikasi,
                                    'verifikator_id' => Auth::id(),
                                    'tanggal_verifikasi' => now(),
                                ]);
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Dokumen berhasil diverifikasi secara massal')
                        ->visible(fn () => in_array(Auth::user()->role, ['admin_dinas', 'verifikator_dinas'])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
