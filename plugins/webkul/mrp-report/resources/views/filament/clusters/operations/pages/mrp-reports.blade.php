<x-filament-panels::page>
    <form wire:submit="generateReport" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end gap-x-3">
            <x-filament::button
                type="submit"
                icon="heroicon-o-printer"
                size="lg"
            >
                Cetak / Unduh Laporan PDF
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
