<div class="space-y-6">
    <x-filament-widgets::widgets
        :columns="[[
            'lg' => 2,
        ]]"
        :data="[]"
        :widgets="[
            App\Filament\Pegawai\Widgets\PengajuanStatus::class,
            App\Filament\Pegawai\Widgets\ProfileInfo::class,
        ]"
    />
</div>