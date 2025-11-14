<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PegawaiResource\Pages;
use App\Filament\App\Resources\PegawaiResource\RelationManagers;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Data Pegawai';
    
    protected static ?string $pluralModelLabel = 'Data Pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Dinas/OPD')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->searchable(),
                
                Forms\Components\TextInput::make('nip')
                    ->label('NIP')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('nrk')
                    ->label('NRK')
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('pangkat_golongan')
                    ->label('Pangkat dan Golongan')
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('unit_kerja')
                    ->label('Unit Kerja')
                    ->maxLength(255),
                
                Forms\Components\DatePicker::make('tmt_pangkat_terakhir')
                    ->label('TMT Pangkat Terakhir'),
                
                Forms\Components\DatePicker::make('tmt_kgb_terakhir')
                    ->label('TMT KGB Terakhir'),
                
                Forms\Components\DatePicker::make('tmt_kgb_berikutnya')
                    ->label('TMT KGB Berikutnya'),
                
                Forms\Components\Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),
                
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir'),
                
                Forms\Components\TextInput::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->maxLength(255),
                
                Forms\Components\Select::make('status_kepegawaian')
                    ->label('Status Kepegawaian')
                    ->options([
                        'PNS' => 'PNS',
                        'PPPK' => 'PPPK',
                    ])
                    ->required(),
                
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('phone')
                    ->label('No HP')
                    ->tel()
                    ->maxLength(20),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Dinas/OPD')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('pangkat_golongan')
                    ->label('Pangkat/Gol')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('tmt_kgb_berikutnya')
                    ->label('TMT KGB Berikutnya')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Dinas/OPD')
                    ->relationship('tenant', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
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
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}