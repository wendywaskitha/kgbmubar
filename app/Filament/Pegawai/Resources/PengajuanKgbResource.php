<?php

namespace App\Filament\Pegawai\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pegawai;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PengajuanKgb;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pegawai\Resources\PengajuanKgbResource\Pages;
use App\Filament\Pegawai\Resources\PengajuanKgbResource\RelationManagers;
use App\Models\User;
use Filament\Notifications\Notification;

class PengajuanKgbResource extends Resource
{
    protected static ?string $model = PengajuanKgb::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Pengajuan KGB Saya';

    protected static ?string $pluralModelLabel = 'Pengajuan KGB Saya';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tmt_kgb_baru')
                    ->label('TMT KGB Baru')
                    ->required(),

                Forms\Components\Select::make('jenis_pengajuan')
                    ->label('Jenis Pengajuan')
                    ->options([
                        'mandiri' => 'Mandiri (oleh Pegawai)',
                    ])
                    ->default('mandiri')
                    ->required()
                    ->disabled(), // Only mandiri for pegawai panel

                Forms\Components\Textarea::make('catatan_verifikasi_dinas')
                    ->label('Catatan Verifikasi Dinas')
                    ->disabled(),

                Forms\Components\Textarea::make('catatan_verifikasi_kabupaten')
                    ->label('Catatan Verifikasi Kabupaten')
                    ->disabled(),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('ajukan')
                    ->label('Ajukan')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->modalHeading('Ajukan Pengajuan KGB')
                    ->modalDescription('Pastikan semua data dan dokumen sudah benar. Anda yakin ingin mengajukan KGB ini ke admin dinas? Setelah diajukan, data tidak dapat diedit.')
                    ->modalSubmitActionLabel('Ajukan Sekarang')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'diajukan',
                            'tanggal_pengajuan' => now(),
                        ]);

                        $targetRoles = ['admin_dinas', 'verifikator_dinas', 'operator_dinas'];
                        $user = Auth::user();
                        $pegawai = $user->pegawai;
                        $appRecipients = User::whereIn('role', $targetRoles)
                            ->where('tenant_id', $user->tenant_id)
                            ->get();

                        foreach ($appRecipients as $recipient) {
                            Notification::make()
                                ->title('Pengajuan KGB Baru')
                                ->body('Pengajuan KGB baru diajukan oleh ' . $pegawai->name . ' pada ' . now()->format('d M Y H:i'))
                                ->icon('heroicon-o-document-text')
                                ->success()
                                ->sendToDatabase($recipient);
                        }

                        Notification::make()
                            ->success()
                            ->title('Pengajuan Telah Diajukan')
                            ->body('Pengajuan KGB Anda telah dikirim ke admin dinas.')
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                $pegawai = $user->pegawai;
                if ($pegawai) {
                    $query->where('pegawai_id', $pegawai->id);
                } else {
                    $query->whereRaw('1 = 0');
                }
            });
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
