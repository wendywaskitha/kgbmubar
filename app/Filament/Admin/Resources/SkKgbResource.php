<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SkKgbResource\Pages;
use App\Models\Pegawai;
use App\Models\PengajuanKgb;
use App\Models\SkKgb;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class SkKgbResource extends Resource
{
    protected static ?string $model = SkKgb::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'SK KGB';
    
    protected static ?string $pluralModelLabel = 'SK KGB';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pegawai_id')
                    ->label('Pegawai')
                    ->relationship('pegawai', 'name')
                    ->required()
                    ->searchable(),
                
                Forms\Components\Select::make('pengajuan_kgb_id')
                    ->label('Pengajuan KGB')
                    ->relationship('pengajuanKgb', 'id')
                    ->required()
                    ->searchable(),
                
                Forms\Components\TextInput::make('no_sk')
                    ->label('Nomor SK')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\DatePicker::make('tanggal_sk')
                    ->label('Tanggal SK')
                    ->required(),
                
                Forms\Components\DatePicker::make('tanggal_efektif')
                    ->label('Tanggal Efektif')
                    ->required(),
                
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-Aktif',
                    ])
                    ->default('aktif')
                    ->required(),
                
                Forms\Components\FileUpload::make('file_path')
                    ->label('Scan SK')
                    ->directory('sk-kgb')
                    ->disk('public')
                    ->visibility('private')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxSize(10240), // 10MB max
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('pegawai.name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('no_sk')
                    ->label('Nomor SK')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tanggal_sk')
                    ->label('Tanggal SK')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tanggal_efektif')
                    ->label('Tanggal Efektif')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'non-aktif' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('file_path')
                    ->label('Scan SK')
                    ->formatStateUsing(function ($record) {
                        return 'File: ' . basename($record->file_path);
                    })
                    ->url(function ($record) {
                        return Storage::url($record->file_path);
                    }, true) // Opens in new tab
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'non-aktif' => 'Non-Aktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tanggal_efektif', 'desc');
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
            'index' => Pages\ListSkKgbs::route('/'),
            'create' => Pages\CreateSkKgb::route('/create'),
            'edit' => Pages\EditSkKgb::route('/{record}/edit'),
        ];
    }
}