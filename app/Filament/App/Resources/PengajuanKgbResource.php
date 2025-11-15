<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PengajuanKgbResource\Pages;
use App\Filament\App\Resources\PengajuanKgbResource\RelationManagers;
use App\Models\Pegawai;
use App\Models\PengajuanKgb;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PengajuanKgbResource extends Resource
{
    protected static ?string $model = PengajuanKgb::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Pengajuan KGB';
    
    protected static ?string $pluralModelLabel = 'Pengajuan KGB';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pegawai_id')
                    ->label('Pegawai')
                    ->relationship(
                        name: 'pegawai',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('tenant_id', auth()->user()->tenant_id)
                    )
                    ->required()
                    ->searchable()
                    ->preload(),
                
                Forms\Components\DatePicker::make('tmt_kgb_baru')
                    ->label('TMT KGB Baru')
                    ->required(),
                
                Forms\Components\Select::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->options([
                        'mandiri' => 'Mandiri (oleh Pegawai)',
                        'admin' => 'Otomatis (oleh Admin)',
                    ])
                    ->default('admin')
                    ->required(),
                
                Forms\Components\Textarea::make('catatan_verifikasi_dinas')
                    ->label('Catatan Verifikasi Dinas')
                    ->columnSpanFull(),
                
                Forms\Components\Textarea::make('catatan_verifikasi_kabupaten')
                    ->label('Catatan Verifikasi Kabupaten')
                    ->columnSpanFull(),
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
                
                Tables\Columns\TextColumn::make('tmt_kgb_baru')
                    ->label('TMT KGB Baru')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'diajukan' => 'warning',
                        'verifikasi_dinas' => 'info',
                        'verifikasi_kabupaten' => 'info',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->formatStateUsing(function ($state) {
                        return $state === 'mandiri' ? 'Mandiri' : 'Admin';
                    }),
                
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
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'verifikasi_dinas' => 'Verifikasi Dinas',
                        'verifikasi_kabupaten' => 'Verifikasi Kabupaten',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),
                Tables\Filters\SelectFilter::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->options([
                        'mandiri' => 'Mandiri',
                        'admin' => 'Admin',
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
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DokumenPengajuanRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuanKgbs::route('/'),
            'create' => Pages\CreatePengajuanKgb::route('/create'),
            'edit' => Pages\EditPengajuanKgb::route('/{record}/edit'),
        ];
    }
}
