<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VerifikasiResource\Pages;
use App\Models\PengajuanKgb;
use App\Models\Verifikasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VerifikasiResource extends Resource
{
    protected static ?string $model = Verifikasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    
    protected static ?string $navigationLabel = 'Verifikasi Pengajuan';
    
    protected static ?string $pluralModelLabel = 'Verifikasi Pengajuan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pengajuan_kgb_id')
                    ->label('Pengajuan KGB')
                    ->relationship('pengajuanKgb', 'id')
                    ->required()
                    ->searchable(),
                
                Forms\Components\Select::make('verifikator_id')
                    ->label('Verifikator')
                    ->relationship('verifikator', 'name')
                    ->required()
                    ->searchable(),
                
                Forms\Components\Select::make('jenis_verifikasi')
                    ->label('Jenis Verifikasi')
                    ->options([
                        'dinas' => 'Verifikasi Dinas',
                        'kabupaten' => 'Verifikasi Kabupaten',
                    ])
                    ->required(),
                
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                    ])
                    ->required(),
                
                Forms\Components\Textarea::make('catatan')
                    ->label('Catatan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                
                Forms\Components\DateTimePicker::make('tanggal_verifikasi')
                    ->label('Tanggal Verifikasi'),
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
                
                Tables\Columns\TextColumn::make('verifikator.name')
                    ->label('Verifikator')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('jenis_verifikasi')
                    ->label('Jenis Verifikasi')
                    ->formatStateUsing(function ($state) {
                        return $state === 'dinas' ? 'Verifikasi Dinas' : 'Verifikasi Kabupaten';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dinas' => 'info',
                        'kabupaten' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'verified' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tanggal_verifikasi')
                    ->label('Tanggal Verifikasi')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_verifikasi')
                    ->label('Jenis Verifikasi')
                    ->options([
                        'dinas' => 'Verifikasi Dinas',
                        'kabupaten' => 'Verifikasi Kabupaten',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
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
            ->defaultSort('tanggal_verifikasi', 'desc');
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
            'index' => Pages\ListVerifikasis::route('/'),
            'create' => Pages\CreateVerifikasi::route('/create'),
            'edit' => Pages\EditVerifikasi::route('/{record}/edit'),
        ];
    }
}