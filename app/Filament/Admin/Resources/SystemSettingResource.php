<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SystemSettingResource\Pages;
use App\Filament\Admin\Resources\SystemSettingResource\RelationManagers;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan Sistem';

    protected static ?string $pluralModelLabel = 'Pengaturan Sistem';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Dinas')
                    ->relationship('tenant', 'name')
                    ->nullable()
                    ->searchable()
                    ->helperText('Biarkan kosong untuk pengaturan global'),

                Forms\Components\TextInput::make('key')
                    ->label('Kunci Pengaturan')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Gunakan format yang jelas, contoh: max_upload_size, email_notifications_enabled, etc.')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('value')
                    ->label('Nilai')
                    ->required()
                    ->maxLength(65535)
                    ->helperText('Nilai dari pengaturan ini')
                    ->columnSpanFull(),

                Forms\Components\Select::make('type')
                    ->label('Jenis Nilai')
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                    ])
                    ->default('string')
                    ->required(),

                Forms\Components\Select::make('group')
                    ->label('Grup')
                    ->options([
                        'general' => 'Umum',
                        'email' => 'Email',
                        'notifications' => 'Notifikasi',
                        'security' => 'Keamanan',
                        'upload' => 'Upload',
                        'workflow' => 'Alur Kerja',
                    ])
                    ->default('general')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(65535)
                    ->helperText('Deskripsi dari pengaturan ini')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_global')
                    ->label('Pengaturan Global')
                    ->helperText('Jika aktif, berlaku untuk semua tenant')
                    ->default(true),

                Forms\Components\Toggle::make('is_public')
                    ->label('Dapat Diakses Publik')
                    ->helperText('Jika aktif, pengaturan ini dapat diakses tanpa autentikasi')
                    ->default(false),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->helperText('Urutan penampilan pengaturan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Kunci Pengaturan')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'string' => 'info',
                        'integer' => 'success',
                        'boolean' => 'warning',
                        'json' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('group')
                    ->label('Grup')
                    ->formatStateUsing(function ($state) {
                        $groups = [
                            'general' => 'Umum',
                            'email' => 'Email',
                            'notifications' => 'Notifikasi',
                            'security' => 'Keamanan',
                            'upload' => 'Upload',
                            'workflow' => 'Alur Kerja',
                        ];
                        return $groups[$state] ?? $state;
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'primary',
                        'email' => 'success',
                        'notifications' => 'warning',
                        'security' => 'danger',
                        'upload' => 'info',
                        'workflow' => 'indigo',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Dinas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_global')
                    ->label('Global')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Publik')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'string' => 'String',
                        'integer' => 'Integer',
                        'boolean' => 'Boolean',
                        'json' => 'JSON',
                    ]),

                Tables\Filters\SelectFilter::make('group')
                    ->label('Grup')
                    ->options([
                        'general' => 'Umum',
                        'email' => 'Email',
                        'notifications' => 'Notifikasi',
                        'security' => 'Keamanan',
                        'upload' => 'Upload',
                        'workflow' => 'Alur Kerja',
                    ]),

                Tables\Filters\TernaryFilter::make('is_global')
                    ->label('Global'),

                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Publik'),

                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Dinas')
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
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListSystemSettings::route('/'),
            'create' => Pages\CreateSystemSetting::route('/create'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
