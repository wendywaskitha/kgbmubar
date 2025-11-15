<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VideoTutorialResource\Pages;
use App\Filament\Admin\Resources\VideoTutorialResource\RelationManagers;
use App\Models\VideoTutorial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class VideoTutorialResource extends Resource
{
    protected static ?string $model = VideoTutorial::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationLabel = 'Video Tutorial';

    protected static ?string $pluralModelLabel = 'Video Tutorial';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Dinas')
                    ->relationship('tenant', 'name')
                    ->nullable()
                    ->searchable(),

                Forms\Components\TextInput::make('title')
                    ->label('Judul Video')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->maxLength(65535)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('video_url')
                    ->label('URL Video')
                    ->required()
                    ->url()
                    ->helperText('URL YouTube, Vimeo, atau video hosting lainnya')
                    ->maxLength(500),

                Forms\Components\Select::make('provider')
                    ->label('Penyedia')
                    ->options([
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                        'internal' => 'Internal',
                    ])
                    ->default('youtube')
                    ->required(),

                Forms\Components\TextInput::make('thumbnail_url')
                    ->label('URL Thumbnail')
                    ->url()
                    ->maxLength(500),

                Forms\Components\TextInput::make('duration')
                    ->label('Durasi (detik)')
                    ->numeric()
                    ->helperText('Durasi video dalam detik'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->helperText('Urutan penampilan video'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Dinas')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('provider')
                    ->label('Penyedia')
                    ->formatStateUsing(function ($state) {
                        $providers = [
                            'youtube' => 'YouTube',
                            'vimeo' => 'Vimeo',
                            'internal' => 'Internal',
                        ];
                        return $providers[$state] ?? $state;
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'youtube' => 'danger',
                        'vimeo' => 'info',
                        'internal' => 'warning',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Durasi')
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'N/A';
                        $hours = floor($state / 3600);
                        $minutes = floor(($state % 3600) / 60);
                        $seconds = $state % 60;

                        if ($hours > 0) {
                            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
                        }
                        return sprintf('%d:%02d', $minutes, $seconds);
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->label('Penyedia')
                    ->options([
                        'youtube' => 'YouTube',
                        'vimeo' => 'Vimeo',
                        'internal' => 'Internal',
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
            'index' => Pages\ListVideoTutorials::route('/'),
            'create' => Pages\CreateVideoTutorial::route('/create'),
            'edit' => Pages\EditVideoTutorial::route('/{record}/edit'),
        ];
    }
}
