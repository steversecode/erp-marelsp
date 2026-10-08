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
                    <div class="inline-flex items-center p-1 rounded-xl bg-gray-100/90 dark:bg-white/5 border border-gray-200/80 dark:border-white/10 shadow-xs text-xs font-medium gap-0.5">
                        @foreach($modeItems as $item)
                            <a
                                href="{{ $item['url'] }}"
                                @class([
                                    'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all duration-150',
                                    'bg-white dark:bg-white/15 text-primary-600 dark:text-white shadow-xs font-semibold ring-1 ring-black/5 dark:ring-white/10' => $item['isActive'],
                                    'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-white/50 dark:hover:bg-white/5' => ! $item['isActive'],
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

            {{-- Right: Clean Unified Record Tabs Bar --}}
            <div class="flex items-center ml-auto">
                @if(count($smartButtons) > 0)
                    <div class="inline-flex flex-wrap items-center p-1 rounded-xl bg-gray-100/90 dark:bg-white/5 border border-gray-200/80 dark:border-white/10 shadow-xs text-xs font-medium gap-1">
                        @foreach($smartButtons as $item)
                            <a
                                href="{{ $item['url'] }}"
                                @class([
                                    'group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all duration-150',
                                    'bg-white dark:bg-white/15 text-primary-600 dark:text-white shadow-xs font-semibold ring-1 ring-black/5 dark:ring-white/10' => $item['isActive'],
                                    'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white hover:bg-white/60 dark:hover:bg-white/5' => ! $item['isActive'],
                                ])
                            >
                                @if($item['icon'])
                                    <x-filament::icon
                                        :icon="$item['icon']"
                                        @class([
                                            'w-3.5 h-3.5 transition-colors',
                                            'text-primary-600 dark:text-primary-400' => $item['isActive'],
                                            'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-200' => ! $item['isActive'],
                                        ])
                                    />
                                @endif

                                <span>{{ $item['label'] }}</span>

                                @if(isset($item['badge']) && $item['badge'] !== null && $item['badge'] !== '')
                                    <span @class([
                                        'inline-flex items-center justify-center min-w-[18px] px-1.5 py-0.5 text-[11px] font-bold rounded-full',
                                        'bg-primary-600 text-white dark:bg-primary-500 dark:text-white' => $item['isActive'],
                                        'bg-gray-200/80 text-gray-700 dark:bg-white/10 dark:text-gray-300 group-hover:bg-primary-50 group-hover:text-primary-700 dark:group-hover:bg-primary-500/20 dark:group-hover:text-primary-300' => ! $item['isActive'],
                                    ])>
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-filament-widgets::widget>
