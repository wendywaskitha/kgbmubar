<div class="space-y-6">
    <x-filament-widgets::widgets
        :columns="[[
            'lg' => 2,
        ]]"
        :data="[]"
        :widgets="[
            App\Filament\Admin\Widgets\TenantsOverview::class,
            App\Filament\Admin\Widgets\PengajuanOverview::class,
        ]"
    />
</div>