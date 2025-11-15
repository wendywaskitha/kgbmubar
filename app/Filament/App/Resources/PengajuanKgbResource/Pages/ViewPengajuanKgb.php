<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\Pages;

use App\Filament\App\Resources\PengajuanKgbResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class ViewPengajuanKgb extends ViewRecord
{
    protected static string $resource = PengajuanKgbResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn ($record) => in_array($record->status, ['draft', 'ditolak'])),
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pegawai')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('pegawai.name')
                            ->label('Nama Lengkap')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('pegawai.nip')
                            ->label('NIP')
                            ->copyable()
                            ->copyMessage('NIP tersalin'),
                        TextEntry::make('pegawai.pangkat_golongan')
                            ->label('Pangkat/Golongan'),
                        TextEntry::make('pegawai.jabatan')
                            ->label('Jabatan'),
                        TextEntry::make('pegawai.unit_kerja')
                            ->label('Unit Kerja'),
                    ])
                    ->columns(2),
                
                Section::make('Riwayat KGB')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        TextEntry::make('pegawai.tmt_pangkat_terakhir')
                            ->label('TMT Pangkat Terakhir')
                            ->date('d F Y')
                            ->placeholder('-'),
                        TextEntry::make('pegawai.tmt_kgb_terakhir')
                            ->label('TMT KGB Terakhir')
                            ->date('d F Y')
                            ->placeholder('-'),
                        TextEntry::make('pegawai.tmt_kgb_berikutnya')
                            ->label('TMT KGB Berikutnya')
                            ->date('d F Y')
                            ->placeholder('-'),
                        TextEntry::make('tmt_kgb_baru')
                            ->label('TMT KGB Baru (Pengajuan)')
                            ->date('d F Y')
                            ->weight('bold')
                            ->color('primary'),
                    ])
                    ->columns(2),
                
                Section::make('Status Pengajuan')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status Saat Ini')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'secondary',
                                'diajukan' => 'warning',
                                'verifikasi_dinas' => 'info',
                                'verifikasi_kabupaten' => 'info',
                                'disetujui' => 'success',
                                'ditolak' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'draft' => 'Draft',
                                'diajukan' => 'Diajukan',
                                'verifikasi_dinas' => 'Verifikasi Dinas',
                                'verifikasi_kabupaten' => 'Verifikasi Kabupaten',
                                'disetujui' => 'Disetujui',
                                'ditolak' => 'Ditolak',
                                default => $state,
                            }),
                        TextEntry::make('jenis_pengajuan')
                            ->label('Jenis Pengajuan')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => $state === 'mandiri' ? 'Mandiri' : 'Admin')
                            ->color(fn (string $state): string => $state === 'mandiri' ? 'success' : 'info'),
                    ])
                    ->columns(2),
                
                Section::make('Timeline Pengajuan')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('-'),
                        TextEntry::make('tanggal_pengajuan')
                            ->label('Tanggal Diajukan')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('Belum diajukan'),
                        TextEntry::make('tanggal_verifikasi_dinas')
                            ->label('Tanggal Verifikasi Dinas')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('Belum diverifikasi'),
                        TextEntry::make('tanggal_verifikasi_kabupaten')
                            ->label('Tanggal Verifikasi Kabupaten')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('Belum diverifikasi'),
                        TextEntry::make('tanggal_approve')
                            ->label('Tanggal Disetujui')
                            ->dateTime('d F Y, H:i')
                            ->placeholder('Belum disetujui'),
                    ])
                    ->columns(2),
                
                Section::make('Catatan & Verifikasi')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->schema([
                        TextEntry::make('catatan_verifikasi_dinas')
                            ->label('Catatan Verifikasi Dinas')
                            ->placeholder('Belum ada catatan')
                            ->columnSpanFull(),
                        TextEntry::make('catatan_verifikasi_kabupaten')
                            ->label('Catatan Verifikasi Kabupaten')
                            ->placeholder('Belum ada catatan')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                
                Section::make('Informasi Lainnya')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        TextEntry::make('no_sk')
                            ->label('Nomor SK')
                            ->placeholder('Belum ada SK'),
                        TextEntry::make('tanggal_sk')
                            ->label('Tanggal SK')
                            ->date('d F Y')
                            ->placeholder('Belum ada SK'),
                        TextEntry::make('jumlah_revisi')
                            ->label('Jumlah Revisi')
                            ->badge()
                            ->color(fn ($state) => $state > 0 ? 'warning' : 'success'),
                        TextEntry::make('userPengaju.name')
                            ->label('Dibuat Oleh')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
