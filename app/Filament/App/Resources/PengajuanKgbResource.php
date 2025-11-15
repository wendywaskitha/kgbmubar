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
use Filament\Notifications\Notification;

class PengajuanKgbResource extends Resource
{
    protected static ?string $model = PengajuanKgb::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'Pengajuan KGB Saya';
    
    protected static ?string $pluralModelLabel = 'Pengajuan KGB';
    
    protected static ?string $modelLabel = 'Pengajuan KGB';

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $pegawai = $user->pegawai;
        
        return $form
            ->schema([
                // Info Section - Menampilkan data pegawai yang login
                Forms\Components\Section::make('Informasi Pegawai')
                    ->description('Data pegawai yang akan mengajukan KGB')
                    ->schema([
                        Forms\Components\Placeholder::make('pegawai_info')
                            ->label('Nama Pegawai')
                            ->content($pegawai?->name ?? 'Tidak ditemukan'),
                        
                        Forms\Components\Placeholder::make('nip_info')
                            ->label('NIP')
                            ->content($pegawai?->nip ?? '-'),
                        
                        Forms\Components\Placeholder::make('pangkat_info')
                            ->label('Pangkat/Golongan')
                            ->content($pegawai?->pangkat_golongan ?? '-'),
                        
                        Forms\Components\Placeholder::make('tmt_kgb_terakhir_info')
                            ->label('TMT KGB Terakhir')
                            ->content($pegawai?->tmt_kgb_terakhir?->format('d F Y') ?? '-'),
                        
                        Forms\Components\Placeholder::make('tmt_kgb_berikutnya_info')
                            ->label('TMT KGB Berikutnya')
                            ->content($pegawai?->tmt_kgb_berikutnya?->format('d F Y') ?? '-'),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                // Form Section
                Forms\Components\Section::make('Detail Pengajuan KGB')
                    ->description('Lengkapi informasi pengajuan KGB Anda')
                    ->schema([
                        Forms\Components\DatePicker::make('tmt_kgb_baru')
                            ->label('TMT KGB Baru')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default($pegawai?->tmt_kgb_berikutnya)
                            ->helperText('Tanggal Mulai Terhitung Kenaikan Gaji Berkala baru'),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'diajukan' => 'Diajukan',
                            ])
                            ->default('draft')
                            ->required()
                            ->helperText('Pilih "Draft" untuk menyimpan sementara atau "Diajukan" untuk mengirim pengajuan'),
                        
                        Forms\Components\Textarea::make('catatan')
                            ->label('Catatan Tambahan')
                            ->rows(3)
                            ->helperText('Isi catatan jika ada informasi tambahan yang perlu disampaikan')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                // Hidden field untuk jenis_pengajuan
                Forms\Components\Hidden::make('jenis_pengajuan')
                    ->default('mandiri'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('NIP tersalin')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('pegawai.name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->description(fn (PengajuanKgb $record): string => $record->pegawai?->pangkat_golongan ?? '-'),
                
                Tables\Columns\TextColumn::make('tmt_kgb_baru')
                    ->label('TMT KGB Baru')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar')
                    ->iconColor('primary'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'diajukan',
                        'info' => fn ($state) => in_array($state, ['verifikasi_dinas', 'verifikasi_kabupaten']),
                        'success' => 'disetujui',
                        'danger' => 'ditolak',
                    ])
                    ->icons([
                        'heroicon-o-pencil' => 'draft',
                        'heroicon-o-paper-airplane' => 'diajukan',
                        'heroicon-o-clock' => fn ($state) => in_array($state, ['verifikasi_dinas', 'verifikasi_kabupaten']),
                        'heroicon-o-check-circle' => 'disetujui',
                        'heroicon-o-x-circle' => 'ditolak',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'verifikasi_dinas' => 'Verifikasi Dinas',
                        'verifikasi_kabupaten' => 'Verifikasi Kabupaten',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('jenis_pengajuan')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'mandiri' ? 'Mandiri' : 'Admin')
                    ->color(fn (string $state): string => $state === 'mandiri' ? 'success' : 'info')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('tanggal_pengajuan')
                    ->label('Tanggal Diajukan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('Belum diajukan')
                    ->toggleable(),
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
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->visible(fn (PengajuanKgb $record): bool => in_array($record->status, ['draft', 'ditolak'])),
                Tables\Actions\Action::make('ajukan')
                    ->label('Ajukan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan Pengajuan KGB')
                    ->modalDescription('Apakah Anda yakin ingin mengajukan KGB ini? Setelah diajukan, pengajuan tidak dapat diubah.')
                    ->modalSubmitActionLabel('Ya, Ajukan')
                    ->action(function (PengajuanKgb $record) {
                        $record->update([
                            'status' => 'diajukan',
                            'tanggal_pengajuan' => now(),
                        ]);
                        
                        Notification::make()
                            ->success()
                            ->title('Pengajuan Berhasil')
                            ->body('Pengajuan KGB Anda telah diajukan dan akan diproses oleh verifikator.')
                            ->send();
                    })
                    ->visible(fn (PengajuanKgb $record): bool => $record->status === 'draft'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => false), // Disable bulk delete untuk keamanan
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pengajuan KGB')
            ->emptyStateDescription('Klik tombol "Buat Pengajuan" untuk membuat pengajuan KGB baru.')
            ->emptyStateIcon('heroicon-o-document-text')
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
            'view' => Pages\ViewPengajuanKgb::route('/{record}'),
            'edit' => Pages\EditPengajuanKgb::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        $pegawai = $user?->pegawai;
        
        if (!$pegawai) {
            return null;
        }
        
        return static::getModel()::where('pegawai_id', $pegawai->id)
            ->where('status', 'draft')
            ->count() ?: null;
    }
    
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
