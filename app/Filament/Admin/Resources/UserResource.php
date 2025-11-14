<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Admin\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Manajemen Pengguna';
    
    protected static ?string $pluralModelLabel = 'Manajemen Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Dinas/OPD')
                    ->relationship('tenant', 'name')
                    ->helperText('Pilih dinas/OPD untuk pengguna ini (kosongkan untuk pengguna global)'),
                
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                
                Forms\Components\Select::make('role')
                    ->label('Peran')
                    ->options([
                        'super_admin' => 'Super Admin Kabupaten',
                        'verifikator_kabupaten' => 'Verifikator Kabupaten',
                        'admin_dinas' => 'Admin Dinas',
                        'verifikator_dinas' => 'Verifikator Dinas',
                        'operator_dinas' => 'Operator Dinas',
                        'pegawai' => 'Pegawai',
                    ])
                    ->required()
                    ->live(),
                
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create'),
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
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('role')
                    ->label('Peran')
                    ->formatStateUsing(function ($state) {
                        $roleLabels = [
                            'super_admin' => 'Super Admin Kabupaten',
                            'verifikator_kabupaten' => 'Verifikator Kabupaten',
                            'admin_dinas' => 'Admin Dinas',
                            'verifikator_dinas' => 'Verifikator Dinas',
                            'operator_dinas' => 'Operator Dinas',
                            'pegawai' => 'Pegawai',
                        ];
                        
                        return $roleLabels[$state] ?? $state;
                    })
                    ->searchable()
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
                Tables\Filters\SelectFilter::make('role')
                    ->label('Peran')
                    ->options([
                        'super_admin' => 'Super Admin Kabupaten',
                        'verifikator_kabupaten' => 'Verifikator Kabupaten',
                        'admin_dinas' => 'Admin Dinas',
                        'verifikator_dinas' => 'Verifikator Dinas',
                        'operator_dinas' => 'Operator Dinas',
                        'pegawai' => 'Pegawai',
                    ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}