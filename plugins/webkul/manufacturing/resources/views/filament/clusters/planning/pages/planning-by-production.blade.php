<x-filament-panels::page>
    @php
        $data = $this->timelineData;
        $stats = $data['stats'];
        $columns = $data['columns'];
        $rows = $data['rows'];
        $selectedOrder = $this->selectedOrder;
        $minWidth = $viewMode === 'month' ? 'min-w-[1300px]' : 'min-w-[900px]';
    @endphp

    <style>
        /* Modern Scoped Gantt Design Tokens */
        .gantt-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
        :is(.dark, [data-theme="dark"]) .gantt-card {
            background-color: #0f172a !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .gantt-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-header {
            background-color: #1e293b !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .gantt-header-cell {
            border-right: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-header-cell {
            border-right: 1px solid rgba(255, 255, 255, 0.06) !important;
        }

        .gantt-sidebar-cell {
            border-right: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-sidebar-cell {
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .gantt-row {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.15s ease;
        }
        :is(.dark, [data-theme="dark"]) .gantt-row {
            border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
        }
        .gantt-row:hover {
            background-color: rgba(241, 245, 249, 0.6);
        }
        :is(.dark, [data-theme="dark"]) .gantt-row:hover {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        .gantt-grid-line {
            border-right: 1px solid #f1f5f9;
        }
        :is(.dark, [data-theme="dark"]) .gantt-grid-line {
            border-right: 1px solid rgba(255, 255, 255, 0.04) !important;
        }

        .gantt-weekend-col {
            background-color: rgba(241, 245, 249, 0.6);
        }
        :is(.dark, [data-theme="dark"]) .gantt-weekend-col {
            background-color: rgba(255, 255, 255, 0.015) !important;
        }

        .gantt-today-col {
            background-color: rgba(59, 130, 246, 0.05) !important;
        }
        :is(.dark, [data-theme="dark"]) .gantt-today-col {
            background-color: rgba(59, 130, 246, 0.08) !important;
        }

        /* Modern Gantt Bars */
        .gantt-bar {
            border-radius: 8px;
            cursor: pointer;
            user-select: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
        }
        .gantt-bar:hover {
            transform: translateY(-1px);
            filter: brightness(1.06);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            z-index: 30;
        }

        .gantt-bar-progress {
            background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%) !important;
            border: 1px solid #fbbf24 !important;
            color: #ffffff !important;
        }
        .gantt-bar-confirmed, .gantt-bar-ready {
            background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%) !important;
            border: 1px solid #60a5fa !important;
            color: #ffffff !important;
        }
        .gantt-bar-done {
            background: linear-gradient(180deg, #10b981 0%, #059669 100%) !important;
            border: 1px solid #34d399 !important;
            color: #ffffff !important;
        }
        .gantt-bar-overdue {
            background: linear-gradient(180deg, #f43f5e 0%, #e11d48 100%) !important;
            border: 1px solid #fb7185 !important;
            color: #ffffff !important;
        }
        .gantt-bar-waiting, .gantt-bar-pending {
            background: linear-gradient(180deg, #64748b 0%, #475569 100%) !important;
            border: 1px solid #94a3b8 !important;
            color: #ffffff !important;
        }

        /* Custom dropdown with guaranteed chevron icon */
        .gantt-select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.25em 1.25em;
            padding-right: 2rem;
        }

        .gantt-tile {
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-tile {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
    </style>

    <div class="space-y-4" x-data="{ tooltip: null }">
        {{-- Unified Executive Header Card --}}
        <div class="gantt-card shadow-xs overflow-hidden">
            {{-- Top Controls Bar --}}
            <div class="p-3 sm:px-4 flex flex-wrap items-center justify-between gap-3">
                {{-- Left: Date Navigation & Title --}}
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.04] p-0.5">
                        <button
                            type="button"
                            wire:click="previous"
                            class="p-1.5 rounded-md text-gray-600 hover:text-gray-950 hover:bg-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/10 transition"
                            title="Previous"
                        >
                            <x-filament::icon icon="heroicon-m-chevron-left" class="w-4 h-4" />
                        </button>
                        <button
                            type="button"
                            wire:click="today"
                            class="px-3 py-1 text-xs font-semibold text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400 rounded-md transition"
                        >
                            Today
                        </button>
                        <button
                            type="button"
                            wire:click="next"
                            class="p-1.5 rounded-md text-gray-600 hover:text-gray-950 hover:bg-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/10 transition"
                            title="Next"
                        >
                            <x-filament::icon icon="heroicon-m-chevron-right" class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="flex items-center gap-2 text-sm sm:text-base font-bold text-gray-950 dark:text-white">
                        <x-filament::icon icon="heroicon-o-calendar" class="w-4 h-4 text-primary-500" />
                        <span>{{ $data['period_title'] }}</span>
                    </div>
                </div>

                {{-- Right: View Mode, Filter, Search --}}
                <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                    {{-- Scale Switcher --}}
                    <div class="inline-flex p-0.5 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.04]">
                        <button
                            type="button"
                            wire:click="setViewMode('week')"
                            class="px-3 py-1 text-xs font-medium rounded-md transition {{ $viewMode === 'week' ? 'bg-primary-600 text-white font-semibold shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Week
                        </button>
                        <button
                            type="button"
                            wire:click="setViewMode('month')"
                            class="px-3 py-1 text-xs font-medium rounded-md transition {{ $viewMode === 'month' ? 'bg-primary-600 text-white font-semibold shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Month
                        </button>
                    </div>

                    {{-- Status Filter --}}
                    <select
                        wire:model.live="statusFilter"
                        class="gantt-select py-1 pl-3 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800/80 text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 cursor-pointer"
                    >
                        <option value="all">All Status</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="progress">In Progress</option>
                        <option value="to_close">To Close</option>
                        <option value="done">Done</option>
                    </select>

                    {{-- Search Box --}}
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search MO or product..."
                            class="py-1 pl-8 pr-3 text-xs rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800/80 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 w-36 sm:w-48"
                        />
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="absolute w-3.5 h-3.5 text-gray-400 left-2.5 top-2 pointer-events-none" />
                    </div>
                </div>
            </div>

            {{-- Seamless Metrics Sub-Bar (No ugly grey strip!) --}}
            <div class="px-4 py-2.5 border-t border-gray-100 dark:border-white/5 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.05] dark:text-gray-300">
                        Total Orders: <strong class="ml-1 text-gray-950 dark:text-white">{{ $stats['total_orders'] }}</strong>
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Confirmed: <strong>{{ $stats['confirmed'] }}</strong>
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        In Progress: <strong>{{ $stats['in_progress'] }}</strong>
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Done: <strong>{{ $stats['done'] }}</strong>
                    </span>

                    @if(!empty($stats['overdue']))
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-500/10 text-rose-500 border border-rose-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Overdue: <strong>{{ $stats['overdue'] }}</strong>
                        </span>
                    @endif
                </div>

                <div class="text-gray-500 dark:text-gray-400 text-xs">
                    Planned Output: <strong class="text-gray-950 dark:text-white font-semibold">{{ $stats['total_qty'] }} Units</strong>
                </div>
            </div>
        </div>

        {{-- Gantt Matrix Card --}}
        <div class="gantt-card shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <div class="{{ $minWidth }}">
                    {{-- Header Row --}}
                    <div class="flex gantt-header">
                        {{-- Left Column Header --}}
                        <div class="w-72 sm:w-80 shrink-0 px-4 py-2.5 text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400 gantt-sidebar-cell flex items-center justify-between">
                            <span>Manufacturing Order</span>
                            <span class="text-[10px] text-gray-400 font-normal lowercase">({{ count($rows) }})</span>
                        </div>

                        {{-- Timeline Columns Header --}}
                        <div class="flex-1 flex">
                            @foreach($columns as $col)
                                @php
                                    $isWeekend = $col['is_weekend'] ?? false;
                                    $colBg = $col['is_today'] ? 'gantt-today-col' : ($isWeekend ? 'gantt-weekend-col' : '');
                                @endphp
                                <div class="flex-1 px-1 py-1.5 text-center gantt-header-cell last:border-r-0 {{ $colBg }}">
                                    @if($col['is_today'])
                                        <div class="w-5 h-5 mx-auto rounded-full bg-primary-600 text-white font-bold text-[11px] flex items-center justify-center shadow-xs">
                                            {{ $col['label'] }}
                                        </div>
                                    @else
                                        <div class="text-xs font-bold {{ $isWeekend ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $col['label'] }}
                                        </div>
                                    @endif

                                    @if(!empty($col['sublabel']))
                                        <div class="text-[10px] font-medium leading-none mt-0.5 {{ $col['is_today'] ? 'text-primary-600 dark:text-primary-400 font-bold' : ($isWeekend ? 'text-gray-400 dark:text-gray-600' : 'text-gray-400 dark:text-gray-500') }}">
                                            {{ $col['sublabel'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Rows --}}
                    @forelse($rows as $row)
                        @php
                            $order = $row['order'];
                            $theme = $row['color_theme'];
                            $barClass = $row['bar_class'] ?? 'gantt-bar-confirmed';
                            $isShortBar = $row['width_percent'] < 14;
                        @endphp
                        <div class="flex gantt-row">
                            {{-- MO Left Card (High readability) --}}
                            <div class="w-72 sm:w-80 shrink-0 p-3 sm:px-4 gantt-sidebar-cell flex flex-col justify-center">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-bold font-mono text-xs text-gray-950 dark:text-white truncate">
                                        {{ $order->name }}
                                    </span>

                                    {{-- Filament-Style Pill Badge --}}
                                    @if($row['state'] === 'progress')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/25">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            In Progress
                                        </span>
                                    @elseif($row['state'] === 'done')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/25">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Done
                                        </span>
                                    @elseif($row['state'] === 'confirmed')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-500/10 text-blue-500 border border-blue-500/25">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            Confirmed
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full {{ $theme['badge'] }}">
                                            {{ $row['state_label'] }}
                                        </span>
                                    @endif
                                </div>

                                <div class="text-xs text-gray-600 dark:text-gray-300 truncate mt-1 flex items-center gap-1 font-medium">
                                    <span class="text-gray-400">📦</span>
                                    <span class="truncate">{{ $order->product?->name }}</span>
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-gray-400 dark:text-gray-500 mt-1.5 pt-1 border-t border-gray-100 dark:border-white/5">
                                    <span>{{ (float) $order->quantity }} {{ $order->product?->uom?->name }}</span>
                                    <span>•</span>
                                    <span>{{ $row['done_wo_count'] }}/{{ $row['work_orders_count'] }} Ops</span>
                                    <span>•</span>
                                    <div class="flex items-center gap-1">
                                        <div class="w-10 h-1.5 rounded-full bg-gray-200 dark:bg-white/10 overflow-hidden">
                                            <div class="h-full {{ $row['progress_percent'] === 100 ? 'bg-emerald-500' : 'bg-primary-500' }}" style="width: {{ $row['progress_percent'] }}%"></div>
                                        </div>
                                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ $row['progress_percent'] }}%</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Timeline Canvas Track --}}
                            <div class="flex-1 relative h-16">
                                {{-- Background Grid Lines --}}
                                <div class="absolute inset-0 flex pointer-events-none">
                                    @foreach($columns as $col)
                                        @php
                                            $isWeekend = $col['is_weekend'] ?? false;
                                            $colBg = $col['is_today'] ? 'gantt-today-col' : ($isWeekend ? 'gantt-weekend-col' : '');
                                        @endphp
                                        <div class="flex-1 gantt-grid-line last:border-r-0 {{ $colBg }}"></div>
                                    @endforeach
                                </div>

                                {{-- Gantt Bar Wrapper --}}
                                <div
                                    class="absolute top-3 bottom-3"
                                    style="left: {{ $row['left_percent'] }}%; width: {{ max(4.0, $row['width_percent']) }}%;"
                                >
                                    {{-- Clickable Gantt Bar --}}
                                    <div
                                        wire:click="openOrderModal({{ $order->id }})"
                                        class="w-full h-full px-2.5 flex items-center justify-between gap-1.5 overflow-hidden gantt-bar {{ $barClass }}"
                                        x-on:mouseenter="tooltip = {
                                            name: '{{ $order->name }}',
                                            product: '{{ addslashes($order->product?->name ?? '') }}',
                                            quantity: '{{ (float) $order->quantity }} {{ $order->product?->uom?->name }}',
                                            start: '{{ $row['start_formatted'] }}',
                                            deadline: '{{ $row['deadline_formatted'] }}',
                                            progress: '{{ $row['progress_percent'] }}%',
                                            state: '{{ $row['state_label'] }}',
                                            is_overdue: {{ $row['is_overdue'] ? 'true' : 'false' }},
                                            wo_count: '{{ $row['work_orders_count'] }} operations',
                                            badge: '{{ $theme['badge'] }}'
                                        }"
                                        x-on:mouseleave="tooltip = null"
                                    >
                                        <div class="flex items-center gap-1.5 truncate">
                                            @if($row['state'] === 'progress')
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping shrink-0"></span>
                                            @elseif($row['state'] === 'done')
                                                <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @endif

                                            @if(! $isShortBar)
                                                <span class="font-bold text-xs text-white truncate drop-shadow-xs">
                                                    {{ $order->name }}: {{ $order->product?->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <span class="shrink-0 text-[10px] font-bold bg-black/25 text-white rounded px-1.5 py-0.5 leading-none">
                                            {{ $row['progress_percent'] }}%
                                        </span>
                                    </div>

                                    {{-- Exterior Label for Short Bars (Effortless Readability!) --}}
                                    @if($isShortBar)
                                        <div class="absolute left-full ml-2.5 top-0 bottom-0 flex items-center pointer-events-none whitespace-nowrap z-20">
                                            <span class="text-xs font-semibold text-gray-800 dark:text-gray-200 drop-shadow-xs flex items-center gap-1.5">
                                                <span>{{ $order->name }}:</span>
                                                <span class="text-gray-500 dark:text-gray-400 font-medium">{{ $order->product?->name }}</span>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-10 h-10 mx-auto text-gray-400 mb-2 opacity-40" />
                            <div class="font-semibold text-sm">No manufacturing orders scheduled in this period</div>
                            <div class="text-xs text-gray-400 mt-1">Try switching to another period or changing filters.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Floating Tooltip --}}
        <div
            x-show="tooltip"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="fixed bottom-6 right-6 z-50 max-w-sm p-4 bg-gray-900/95 backdrop-blur-md text-white rounded-xl shadow-2xl border border-white/10 pointer-events-none text-xs space-y-1.5"
        >
            <div class="font-bold text-sm text-primary-400 flex items-center justify-between">
                <span x-text="tooltip ? tooltip.name : ''"></span>
                <template x-if="tooltip && tooltip.is_overdue">
                    <span class="text-rose-400 font-bold flex items-center gap-1 text-[11px] bg-rose-500/20 px-1.5 py-0.5 rounded">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-3.5 h-3.5" />
                        OVERDUE
                    </span>
                </template>
            </div>
            <div class="text-gray-300"><span class="text-gray-400">Product:</span> <span class="font-medium" x-text="tooltip ? tooltip.product : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Quantity:</span> <span class="font-medium" x-text="tooltip ? tooltip.quantity : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Timeline:</span> <span class="font-medium" x-text="tooltip ? tooltip.start + ' -> ' + tooltip.deadline : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Operations:</span> <span class="font-medium" x-text="tooltip ? tooltip.wo_count : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Progress:</span> <span class="font-bold text-emerald-400" x-text="tooltip ? tooltip.progress : ''"></span></div>
            <div class="flex items-center gap-2 pt-1 border-t border-white/10">
                <span class="text-gray-400">Status:</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="tooltip ? tooltip.badge : ''" x-text="tooltip ? tooltip.state : ''"></span>
            </div>
        </div>

        {{-- Detail Modal --}}
        @if($selectedOrder)
            <div
                class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-xs flex items-center justify-center p-4"
                wire:click.self="closeOrderModal"
            >
                <div class="relative w-full max-w-3xl rounded-xl shadow-2xl border border-gray-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in-95 duration-200 bg-white dark:bg-gray-900">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.03]">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-950 dark:text-white">
                                    {{ $selectedOrder->name }}
                                </h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $selectedOrder->product?->name }}
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="closeOrderModal"
                            class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition"
                        >
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                        {{-- Metrics Cards --}}
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">Target Qty</div>
                                <div class="font-bold mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ (float) $selectedOrder->quantity }} {{ $selectedOrder->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">Produced Qty</div>
                                <div class="font-bold mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ (float) $selectedOrder->quantity_producing }} {{ $selectedOrder->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded {{ $selectedOrder->state instanceof \Webkul\Manufacturing\Enums\ManufacturingOrderState ? $selectedOrder->state->getColor() : 'gray' }}">
                                        {{ $selectedOrder->state instanceof \Webkul\Manufacturing\Enums\ManufacturingOrderState ? $selectedOrder->state->getLabel() : ucfirst($selectedOrder->state) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">Responsible</div>
                                <div class="font-bold mt-1 truncate text-gray-950 dark:text-white">
                                    {{ $selectedOrder->assignedUser?->name ?? 'Unassigned' }}
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Details --}}
                        <div class="grid grid-cols-2 gap-4 p-3.5 rounded-lg border gantt-tile">
                            <div>
                                <div class="text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Scheduled Start</div>
                                <div class="font-medium mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ $selectedOrder->started_at ? $selectedOrder->started_at->format('d M Y, H:i') : '—' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Deadline</div>
                                <div class="font-medium mt-1 text-sm {{ $selectedOrder->deadline_at && $selectedOrder->deadline_at->lt(now()) && !in_array($selectedOrder->state?->value, ['done', 'cancel']) ? 'text-rose-500 flex items-center gap-1.5' : 'text-gray-950 dark:text-white' }}">
                                    {{ $selectedOrder->deadline_at ? $selectedOrder->deadline_at->format('d M Y, H:i') : 'No deadline' }}
                                    @if($selectedOrder->deadline_at && $selectedOrder->deadline_at->lt(now()) && !in_array($selectedOrder->state?->value, ['done', 'cancel']))
                                        <span class="text-[10px] px-1.5 py-0.5 bg-rose-500/20 text-rose-400 rounded font-bold">OVERDUE</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Operations Table --}}
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                Work Orders & Operations ({{ $selectedOrder->workOrders->count() }})
                            </h4>

                            @if($selectedOrder->workOrders->isNotEmpty())
                                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-white/10">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-50 dark:bg-white/[0.03] border-b border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400">
                                            <tr>
                                                <th class="px-3.5 py-2 text-left font-semibold uppercase tracking-wider">Operation</th>
                                                <th class="px-3.5 py-2 text-left font-semibold uppercase tracking-wider">Work Center</th>
                                                <th class="px-3.5 py-2 text-right font-semibold uppercase tracking-wider">Expected</th>
                                                <th class="px-3.5 py-2 text-right font-semibold uppercase tracking-wider">Actual</th>
                                                <th class="px-3.5 py-2 text-center font-semibold uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                            @foreach($selectedOrder->workOrders as $wo)
                                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                                                    <td class="px-3.5 py-2.5 font-medium text-gray-950 dark:text-white">{{ $wo->name }}</td>
                                                    <td class="px-3.5 py-2.5 text-gray-600 dark:text-gray-300">{{ $wo->workCenter?->name ?? '—' }}</td>
                                                    <td class="px-3.5 py-2.5 text-right text-gray-600 dark:text-gray-400 font-mono">{{ (float) $wo->expected_duration }}m</td>
                                                    <td class="px-3.5 py-2.5 text-right text-gray-600 dark:text-gray-400 font-mono">{{ (float) $wo->duration }}m</td>
                                                    <td class="px-3.5 py-2.5 text-center">
                                                        <span class="inline-flex px-1.5 py-0.5 text-[10px] font-semibold rounded {{ $wo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $wo->state->getColor() : 'gray' }}">
                                                            {{ $wo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $wo->state->getLabel() : ucfirst($wo->state) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-xs text-gray-400 dark:text-gray-500 italic p-4 bg-gray-50 dark:bg-white/[0.02] rounded-lg text-center">
                                    No work orders generated for this manufacturing order.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end gap-2 px-6 py-3 border-t border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.03]">
                        <a
                            href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource::getUrl('view', ['record' => $selectedOrder->id]) }}"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-500 transition shadow-xs"
                        >
                            <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                            Open MO
                        </a>

                        <button
                            type="button"
                            wire:click="closeOrderModal"
                            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/20 transition"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
