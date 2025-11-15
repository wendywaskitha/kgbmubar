<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DokumenPengajuanResource\Pages;
use App\Models\DokumenPengajuan;
use App\Models\PengajuanKgb;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DokumenPengajuanResource extends Resource
{
    protected static ?string $model = DokumenPengajuan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    
    protected static ?string $navigationLabel = 'Dokumen Pengajuan';
    
    protected static ?string $pluralModelLabel = 'Dokumen Pengajuan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pengajuan_kgb_id')
                    ->label('Pengajuan KGB')
                    ->relationship('pengajuanKgb', 'id')
                    ->options(function () {
                        $user = auth()->user();
                        // Get all pengajuan from the user's tenant
                        return PengajuanKgb::where('tenant_id', $user->tenant_id)
                            ->pluck('id', 'id');
                    })
                    ->required()
                    ->searchable(),
                
                Forms\Components\TextInput::make('nama_dokumen')
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
                
                Forms\Components\FileUpload::make('file_path')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pengajuanKgb.id')
                    ->label('ID Pengajuan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('nama_dokumen')
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
                
                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->formatStateUsing(function ($record) {
                        return 'File: ' . basename($record->file_path);
                    })
                    ->url(function ($record) {
                        return Storage::url($record->file_path);
                    }, true) // Opens in new tab
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('ukuran_file')
                    ->label('Ukuran (KB)')
                    ->formatStateUsing(function ($state) {
                        return round($state / 1024, 2) . ' KB';
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDokumenPengajuans::route('/'),
            'create' => Pages\CreateDokumenPengajuan::route('/create'),
            'edit' => Pages\EditDokumenPengajuan::route('/{record}/edit'),
        ];
    }
}