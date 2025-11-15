<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PengajuanKgbResource\Pages;
use App\Filament\App\Resources\PengajuanKgbResource\RelationManagers;
use App\Models\Pegawai;
use App\Models\PengajuanKgb;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PengajuanKgbResource extends Resource
{
    protected static ?string $model = PengajuanKgb::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Pengajuan KGB';
    protected static ?string $pluralModelLabel = 'Pengajuan KGB';
    protected static ?string $modelLabel = 'Pengajuan KGB';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pegawai_id')
                    ->label('Pegawai')
                    ->relationship('pegawai', 'name')
                    ->required()
                    ->searchable()
                    ->disabled(),

                Forms\Components\DatePicker::make('tmt_kgb_baru')
                    ->label('TMT KGB Baru')
                    ->required(),

                Forms\Components\Select::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->options([
                        'mandiri' => 'Mandiri (oleh Pegawai)',
                        'admin' => 'Otomatis (oleh Admin)',
                    ])
                    ->required()
                    ->disabled(),

                Forms\Components\Textarea::make('catatan_verifikasi_dinas')
                    ->label('Catatan Verifikasi Dinas')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('catatan_verifikasi_kabupaten')
                    ->label('Catatan Verifikasi Kabupaten')
                    ->columnSpanFull()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('status', '!=', 'draft');
            })
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
                    ->color(fn(string $state): string => match ($state) {
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
                        'diajukan' => 'Diajukan (Menunggu Review Dinas)',
                        'verifikasi_dinas' => 'Siap Diverifikasi Kabupaten',
                        'verifikasi_kabupaten' => 'Verifikasi Kabupaten',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('preview_file')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Preview Dokumen')
                    ->modalContent(function ($record) {
                        $fileUrl = asset('storage/' . ltrim($record->path_file, '/'));
                        $extension = strtolower(pathinfo($record->path_file, PATHINFO_EXTENSION));
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
                        if ($isImage) {
                            return view('filament.app.resources.pengajuan-kgb-resource.partials.preview-image', [
                                'fileUrl' => $fileUrl,
                                'fileName' => $record->nama_file,
                            ]);
                        } else {
                            return view('filament.app.resources.pengajuan-kgb-resource.partials.preview-pdf', [
                                'fileUrl' => $fileUrl,
                                'fileName' => $record->nama_file,
                            ]);
                        }
                    })
                    ->modalWidth('7xl'),
                ... // keep other actions, not shown here for brevity
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
            'view' => Pages\ViewPengajuanKgb::route('/{record}'),
            'verifikasi' => Pages\VerifikasiPengajuanKgb::route('/{record}/verifikasi'),
        ];
    }
}
