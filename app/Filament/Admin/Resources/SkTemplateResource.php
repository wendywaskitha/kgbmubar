<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SkTemplateResource\Pages;
use App\Filament\Admin\Resources\SkTemplateResource\RelationManagers;
use App\Models\SkTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SkTemplateResource extends Resource
{
    protected static ?string $model = SkTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Template SK';
    protected static ?string $pluralModelLabel = 'Template SK';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Dinas')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Template')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('kode_template')
                    ->label('Kode Template')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Contoh: kgb-001, kgb-standar, dll'),

                Forms\Components\Select::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->options([
                        'kenaikan_gaji_berkala' => 'Kenaikan Gaji Berkala',
                        'kenaikan_pangkat' => 'Kenaikan Pangkat',
                    ])
                    ->default('kenaikan_gaji_berkala')
                    ->required(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Forms\Components\RichEditor::make('content')
                    ->label('Konten Template')
                    ->required()
                    ->helperText('Gunakan placeholder seperti {nama_pegawai}, {nip}, {golongan}, {tmt_kgb_baru}, dll')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Dinas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Template')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kode_template')
                    ->label('Kode Template')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'kenaikan_gaji_berkala' => 'Kenaikan Gaji Berkala',
                            'kenaikan_pangkat' => 'Kenaikan Pangkat',
                            default => $state,
                        };
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kenaikan_gaji_berkala' => 'primary',
                        'kenaikan_pangkat' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->options([
                        'kenaikan_gaji_berkala' => 'Kenaikan Gaji Berkala',
                        'kenaikan_pangkat' => 'Kenaikan Pangkat',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

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
            ]);
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
            'index' => Pages\ListSkTemplates::route('/'),
            'create' => Pages\CreateSkTemplate::route('/create'),
            'edit' => Pages\EditSkTemplate::route('/{record}/edit'),
        ];
    }
}
