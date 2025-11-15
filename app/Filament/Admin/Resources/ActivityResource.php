<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ActivityResource\Pages;
use App\Filament\Admin\Resources\ActivityResource\RelationManagers;
use Spatie\Activitylog\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Audit Log';

    protected static ?string $pluralModelLabel = 'Audit Logs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('causer_type')
                    ->label('Tipe Pengguna')
                    ->options([
                        'App\Models\User' => 'User',
                        'App\Models\Pegawai' => 'Pegawai',
                    ])
                    ->searchable(),

                Forms\Components\TextInput::make('causer_id')
                    ->label('ID Pengguna')
                    ->numeric(),

                Forms\Components\Select::make('subject_type')
                    ->label('Tipe Subjek')
                    ->options([
                        'App\Models\User' => 'User',
                        'App\Models\Pegawai' => 'Pegawai',
                        'App\Models\PengajuanKgb' => 'Pengajuan KGB',
                        'App\Models\SkKgb' => 'SK KGB',
                    ])
                    ->searchable(),

                Forms\Components\TextInput::make('subject_id')
                    ->label('ID Subjek')
                    ->numeric(),

                Forms\Components\TextInput::make('description')
                    ->label('Deskripsi')
                    ->maxLength(255),

                Forms\Components\Textarea::make('properties')
                    ->label('Properti')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Pengguna')
                    ->formatStateUsing(function ($record) {
                        if ($record->causer) {
                            return $record->causer->name ?? $record->causer_type . ': ' . $record->causer_id;
                        }
                        return 'Sistem';
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Tipe Subjek')
                    ->formatStateUsing(function ($state) {
                        $mappings = [
                            'App\Models\User' => 'User',
                            'App\Models\Pegawai' => 'Pegawai',
                            'App\Models\PengajuanKgb' => 'Pengajuan KGB',
                            'App\Models\SkKgb' => 'SK KGB',
                        ];

                        return $mappings[$state] ?? $state;
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject_id')
                    ->label('ID Subjek')
                    ->numeric(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('causer_type')
                    ->label('Tipe Pengguna')
                    ->options([
                        'App\Models\User' => 'User',
                        'App\Models\Pegawai' => 'Pegawai',
                    ]),

                Tables\Filters\SelectFilter::make('subject_type')
                    ->label('Tipe Subjek')
                    ->options([
                        'App\Models\User' => 'User',
                        'App\Models\Pegawai' => 'Pegawai',
                        'App\Models\PengajuanKgb' => 'Pengajuan KGB',
                        'App\Models\SkKgb' => 'SK KGB',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dari'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], function (Builder $query, $date): Builder {
                                return $query->whereDate('created_at', '>=', $date);
                            })
                            ->when($data['created_until'], function (Builder $query, $date): Builder {
                                return $query->whereDate('created_at', '<=', $date);
                            });
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // We won't allow bulk deletion of activity logs for audit trail integrity
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
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}
