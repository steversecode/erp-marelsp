@php
    $modeItems = [];
    $smartButtons = [];

    foreach ($navigationItems as $item) {
        $labelLower = strtolower(trim($item['label'] ?? ''));
        $url = $item['url'] ?? '';

        // Identify mode items (View / Edit)
        $isMode = in_array($labelLower, ['view', 'edit', 'lihat', 'ubah', 'detail'])
            || (str_ends_with($url, '/edit') && empty($item['badge']))
            || (preg_match('#/\d+$#', $url) && empty($item['badge']) && str_contains($labelLower, 'view'));

        if ($isMode) {
            $modeItems[] = $item;
        } else {
            $smartButtons[] = $item;
        }
    }
@endphp

<x-filament-widgets::widget :loading="false" class="!p-0 bg-transparent shadow-none mb-3">
    @if(count($navigationItems) > 0)
        <div class="flex flex-wrap items-center justify-between gap-3 w-full">
            {{-- Left: Mode Switcher (View / Edit) --}}
            <div class="flex items-center gap-2">
                @if(count($modeItems) > 0)
                    <div class="inline-flex items-center p-1 rounded-xl bg-gray-100/80 dark:bg-white/5 border border-gray-200/80 dark:border-white/10 shadow-xs text-xs font-medium">
                        @foreach($modeItems as $item)
                            <a
                                href="{{ $item['url'] }}"
                                @class([
                                    'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all duration-150',
                                    'bg-white dark:bg-white/15 text-primary-600 dark:text-white shadow-xs font-semibold ring-1 ring-black/5 dark:ring-white/10' => $item['isActive'],
                                    'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' => ! $item['isActive'],
                                ])
                            >
                                @if($item['icon'])
                                    <x-filament::icon
                                        :icon="$item['icon']"
                                        class="w-3.5 h-3.5"
                                    />
                                @endif
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Right: Odoo-Style Smart Stat Buttons (Invoices, Deliveries, etc.) --}}
            <div class="flex flex-wrap items-center justify-end gap-2 ml-auto">
                @foreach($smartButtons as $item)
                    <a
                        href="{{ $item['url'] }}"
                        @class([
                            'group inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl border text-xs font-semibold transition-all duration-150 shadow-xs hover:shadow-sm',
                            'border-primary-500 bg-primary-50/70 text-primary-700 dark:border-primary-400 dark:bg-primary-500/15 dark:text-primary-300 ring-1 ring-primary-500/30' => $item['isActive'],
                            'border-gray-200/90 bg-white hover:bg-gray-50/80 hover:border-gray-300 text-gray-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10' => ! $item['isActive'],
                        ])
                    >
                        @if($item['icon'])
                            <x-filament::icon
                                :icon="$item['icon']"
                                @class([
                                    'w-4 h-4 transition-colors',
                                    'text-primary-600 dark:text-primary-400' => $item['isActive'],
                                    'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-200' => ! $item['isActive'],
                                ])
                            />
                        @endif

                        <span class="tracking-tight">{{ $item['label'] }}</span>

                        @if(isset($item['badge']) && $item['badge'] !== null && $item['badge'] !== '')
                            <span @class([
                                'inline-flex items-center justify-center min-w-[20px] px-1.5 py-0.5 text-[11px] font-bold rounded-full',
                                'bg-primary-600 text-white dark:bg-primary-500 dark:text-white' => $item['isActive'],
                                'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300 group-hover:bg-primary-50 group-hover:text-primary-700 dark:group-hover:bg-primary-500/20 dark:group-hover:text-primary-300' => ! $item['isActive'],
                            ])>
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</x-filament-widgets::widget>
