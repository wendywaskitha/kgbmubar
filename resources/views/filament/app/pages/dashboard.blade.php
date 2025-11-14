<div class="space-y-6">
    <x-filament-widgets::widgets
        :columns="[[
            'lg' => 2,
        ]]"
        :data="[]"
        :widgets="[
            App\Filament\App\Widgets\PegawaiOverview::class,
            App\Filament\App\Widgets\PengajuanOverview::class,
        ]"
    />
</div>