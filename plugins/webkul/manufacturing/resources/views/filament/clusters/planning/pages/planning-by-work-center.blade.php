<x-filament-panels::page>
    @php
        $data = $this->timelineData;
        $stats = $data['stats'];
        $columns = $data['columns'];
        $rows = $data['rows'];
        $selectedWo = $this->selectedWorkOrder;
        $minWidth = match($viewMode) {
            'day'   => 'min-w-[1300px]',
            'month' => 'min-w-[1300px]',
            default => 'min-w-[950px]',
        };
    @endphp

    <style>
        /* Base & Dark mode color tokens for Gantt table */
        .gantt-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-header {
            background-color: #111827 !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .gantt-header-cell {
            border-right: 1px solid #f1f5f9;
        }
        :is(.dark, [data-theme="dark"]) .gantt-header-cell {
            border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        .gantt-sidebar-cell {
            border-right: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-sidebar-cell {
            border-right: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .gantt-grid-line {
            border-right: 1px solid #f1f5f9;
        }
        :is(.dark, [data-theme="dark"]) .gantt-grid-line {
            border-right: 1px solid rgba(255, 255, 255, 0.04) !important;
        }

        .gantt-wc-header-row {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-wc-header-row {
            background-color: rgba(255, 255, 255, 0.02) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        }

        .gantt-row {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.15s ease;
        }
        :is(.dark, [data-theme="dark"]) .gantt-row {
            border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
        }
        .gantt-row:hover {
            background-color: rgba(248, 250, 252, 0.8);
        }
        :is(.dark, [data-theme="dark"]) .gantt-row:hover {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        .gantt-today-col {
            background-color: rgba(59, 130, 246, 0.04) !important;
        }
        :is(.dark, [data-theme="dark"]) .gantt-today-col {
            background-color: rgba(59, 130, 246, 0.08) !important;
        }

        /* Modern, clean flat Gantt bars */
        .gantt-bar {
            border-radius: 6px;
            cursor: pointer;
            user-select: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }
        .gantt-bar:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 25;
        }

        .gantt-bar-progress {
            background-color: #d97706 !important;
            border: 1px solid #f59e0b !important;
            color: #ffffff !important;
        }
        .gantt-bar-ready, .gantt-bar-confirmed {
            background-color: #2563eb !important;
            border: 1px solid #3b82f6 !important;
            color: #ffffff !important;
        }
        .gantt-bar-done {
            background-color: #059669 !important;
            border: 1px solid #10b981 !important;
            color: #ffffff !important;
        }
        .gantt-bar-overdue {
            background-color: #e11d48 !important;
            border: 1px solid #f43f5e !important;
            color: #ffffff !important;
        }
        .gantt-bar-waiting, .gantt-bar-pending {
            background-color: #475569 !important;
            border: 1px solid #64748b !important;
            color: #ffffff !important;
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
        {{-- Unified Clean Control & KPI Bar --}}
        <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
            {{-- Main Navigation & Actions --}}
            <div class="p-3 sm:px-4 flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 dark:border-white/5">
                {{-- Left: Date Navigation & Title --}}
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50/70 dark:bg-white/[0.03] p-0.5">
                        <button
                            type="button"
                            wire:click="previous"
                            class="p-1 rounded-md text-gray-600 hover:text-gray-900 hover:bg-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/10 transition"
                            title="Previous"
                        >
                            <x-filament::icon icon="heroicon-m-chevron-left" class="w-4 h-4" />
                        </button>
                        <button
                            type="button"
                            wire:click="today"
                            class="px-2.5 py-1 text-xs font-semibold text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400 rounded-md transition"
                        >
                            Today
                        </button>
                        <button
                            type="button"
                            wire:click="next"
                            class="p-1 rounded-md text-gray-600 hover:text-gray-900 hover:bg-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/10 transition"
                            title="Next"
                        >
                            <x-filament::icon icon="heroicon-m-chevron-right" class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="flex items-center gap-2 text-sm sm:text-base font-bold text-gray-950 dark:text-white">
                        <x-filament::icon icon="heroicon-o-calendar" class="w-4 h-4 text-gray-400 dark:text-gray-500" />
                        <span>{{ $data['period_title'] }}</span>
                    </div>
                </div>

                {{-- Right: View Mode, Filter, Search --}}
                <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                    {{-- Scale Switcher --}}
                    <div class="inline-flex p-0.5 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50/70 dark:bg-white/[0.03]">
                        <button
                            type="button"
                            wire:click="setViewMode('day')"
                            class="px-2.5 py-1 text-xs font-medium rounded-md transition {{ $viewMode === 'day' ? 'bg-primary-600 text-white font-semibold shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Day
                        </button>
                        <button
                            type="button"
                            wire:click="setViewMode('week')"
                            class="px-2.5 py-1 text-xs font-medium rounded-md transition {{ $viewMode === 'week' ? 'bg-primary-600 text-white font-semibold shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Week
                        </button>
                        <button
                            type="button"
                            wire:click="setViewMode('month')"
                            class="px-2.5 py-1 text-xs font-medium rounded-md transition {{ $viewMode === 'month' ? 'bg-primary-600 text-white font-semibold shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Month
                        </button>
                    </div>

                    {{-- Status Filter --}}
                    <select
                        wire:model.live="statusFilter"
                        class="py-1 px-2.5 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-primary-500 focus:border-primary-500"
                    >
                        <option value="all">All Status</option>
                        <option value="progress">In Progress</option>
                        <option value="ready">Ready</option>
                        <option value="waiting">Waiting</option>
                        <option value="done">Done</option>
                        <option value="cancel">Cancelled</option>
                    </select>

                    {{-- Search Box --}}
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search WO, MO..."
                            class="py-1 pl-7 pr-2.5 text-xs rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 w-36 sm:w-48"
                        />
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="absolute w-3.5 h-3.5 text-gray-400 left-2 top-2 pointer-events-none" />
                    </div>
                </div>
            </div>

            {{-- Compact Metrics Strip --}}
            <div class="px-3.5 sm:px-4 py-2 flex flex-wrap items-center justify-between gap-3 text-xs bg-gray-50/50 dark:bg-white/[0.015]">
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Work Orders: <strong class="text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</strong></span>
                    <span class="text-gray-300 dark:text-gray-700">|</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Ready: <strong class="text-blue-600 dark:text-blue-400">{{ $stats['ready'] }}</strong>
                    </span>
                    <span class="text-gray-300 dark:text-gray-700">|</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        In Progress: <strong class="text-amber-600 dark:text-amber-400">{{ $stats['in_progress'] }}</strong>
                    </span>
                    <span class="text-gray-300 dark:text-gray-700">|</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Done: <strong class="text-emerald-600 dark:text-emerald-400">{{ $stats['done'] }}</strong>
                    </span>
                </div>

                <div class="text-gray-500 dark:text-gray-400 text-xs font-medium">
                    Planned Workload: <strong class="text-gray-900 dark:text-white font-semibold">{{ $stats['planned_hours'] }} Hours</strong>
                </div>
            </div>
        </div>

        {{-- Gantt Matrix Card --}}
        <div class="rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <div class="{{ $minWidth }}">
                    {{-- Header Row --}}
                    <div class="flex gantt-header">
                        {{-- Left Column Header --}}
                        <div class="w-72 sm:w-80 shrink-0 px-4 py-2.5 text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400 gantt-sidebar-cell flex items-center">
                            Work Center / Operations
                        </div>

                        {{-- Timeline Columns Header --}}
                        <div class="flex-1 flex">
                            @foreach($columns as $col)
                                <div class="flex-1 px-1 py-1.5 text-center gantt-header-cell last:border-r-0 {{ $col['is_today'] ? 'gantt-today-col' : '' }}">
                                    @if($col['is_today'])
                                        <div class="inline-block px-1.5 py-0.5 rounded-full bg-primary-600 text-white font-bold text-[11px] leading-tight">
                                            {{ $col['label'] }}
                                        </div>
                                    @else
                                        <div class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $col['label'] }}</div>
                                    @endif

                                    @if(!empty($col['sublabel']))
                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 font-medium leading-none mt-0.5">{{ $col['sublabel'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Work Centers & Scheduled Work Orders --}}
                    @forelse($rows as $row)
                        @php
                            $wc = $row['work_center'];
                            $items = $row['items'];
                        @endphp

                        {{-- Work Center Group Header Row --}}
                        <div class="flex gantt-wc-header-row">
                            <div class="w-72 sm:w-80 shrink-0 px-3.5 sm:px-4 py-2 gantt-sidebar-cell flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 truncate">
                                    <span class="w-2 h-2 rounded-full shrink-0 {{ $wc->working_state === \Webkul\Manufacturing\Enums\WorkCenterWorkingState::BLOCKED ? 'bg-rose-500' : 'bg-emerald-500' }}" title="{{ $wc->working_state instanceof \Webkul\Manufacturing\Enums\WorkCenterWorkingState ? $wc->working_state->getLabel() : 'Normal' }}"></span>
                                    <span class="font-bold text-xs text-gray-950 dark:text-white truncate">
                                        {{ $wc->name }}
                                    </span>
                                    @if($wc->code)
                                        <span class="px-1 py-0.2 text-[9px] font-mono font-bold bg-gray-200 text-gray-700 rounded dark:bg-white/10 dark:text-gray-300">
                                            {{ $wc->code }}
                                        </span>
                                    @endif
                                </div>

                                <span class="text-[11px] font-medium text-gray-500 dark:text-gray-400 shrink-0">
                                    {{ $row['planned_hours'] }}h • {{ $row['active_orders'] }} WO
                                </span>
                            </div>

                            {{-- Work Center Header Background Track --}}
                            <div class="flex-1 relative h-8">
                                <div class="absolute inset-0 flex pointer-events-none">
                                    @foreach($columns as $col)
                                        <div class="flex-1 gantt-grid-line last:border-r-0 {{ $col['is_today'] ? 'gantt-today-col' : '' }}"></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Scheduled Work Orders under this Work Center (Each has its OWN row to avoid overlapping) --}}
                        @forelse($items as $item)
                            @php
                                $barClass = match($item['state']) {
                                    'progress' => 'gantt-bar-progress',
                                    'ready'    => 'gantt-bar-ready',
                                    'done'     => 'gantt-bar-done',
                                    'cancel'   => 'gantt-bar-overdue',
                                    default    => 'gantt-bar-waiting',
                                };
                            @endphp
                            <div class="flex gantt-row">
                                {{-- Sub-Row Left Item --}}
                                <div class="w-72 sm:w-80 shrink-0 py-2 px-3.5 sm:px-4 pl-6 gantt-sidebar-cell flex flex-col justify-center">
                                    <div class="flex items-center justify-between gap-1.5">
                                        <div class="flex items-center gap-1.5 truncate">
                                            <span class="text-gray-400 dark:text-gray-600 font-mono text-xs select-none">↳</span>
                                            <span class="font-semibold text-xs text-gray-900 dark:text-gray-200 truncate" title="{{ $item['mo_name'] }}: {{ $item['name'] }}">
                                                {{ $item['mo_name'] }}: {{ $item['name'] }}
                                            </span>
                                        </div>

                                        <span class="px-1.5 py-0.5 text-[9px] font-semibold rounded shrink-0 {{ $item['color_theme']['badge'] ?? 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300' }}">
                                            {{ $item['state_label'] }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1.5 text-[10px] text-gray-400 dark:text-gray-500 pl-4 mt-0.5 truncate">
                                        <span class="font-medium text-gray-600 dark:text-gray-400">{{ $item['duration_hours'] }}h</span>
                                        <span>•</span>
                                        <span class="truncate">{{ $item['product_name'] }}</span>
                                    </div>
                                </div>

                                {{-- Sub-Row Dedicated Timeline Track --}}
                                <div class="flex-1 relative h-11">
                                    {{-- Background Grid Lines --}}
                                    <div class="absolute inset-0 flex pointer-events-none">
                                        @foreach($columns as $col)
                                            <div class="flex-1 gantt-grid-line last:border-r-0 {{ $col['is_today'] ? 'gantt-today-col' : '' }}"></div>
                                        @endforeach
                                    </div>

                                    {{-- Individual Work Order Bar --}}
                                    <div
                                        wire:click="openWorkOrderModal({{ $item['id'] }})"
                                        class="absolute top-2 bottom-2 px-2.5 py-0.5 flex items-center justify-between gap-1.5 overflow-hidden gantt-bar {{ $barClass }}"
                                        style="left: {{ $item['left_percent'] }}%; width: {{ max(3.5, $item['width_percent']) }}%;"
                                        x-on:mouseenter="tooltip = {{ json_encode($item) }}"
                                        x-on:mouseleave="tooltip = null"
                                    >
                                        <div class="flex items-center gap-1.5 truncate">
                                            @if($item['is_in_progress'])
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping shrink-0"></span>
                                            @elseif($item['is_done'])
                                                <svg class="w-3 h-3 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                            <span class="font-bold text-xs text-white truncate drop-shadow-xs">
                                                @if($item['width_percent'] >= 10)
                                                    {{ $item['mo_name'] }}: {{ $item['name'] }}
                                                @else
                                                    {{ $item['mo_name'] }}
                                                @endif
                                            </span>
                                        </div>

                                        <span class="shrink-0 text-[10px] font-bold bg-black/25 text-white rounded px-1 py-0.2 leading-none">
                                            {{ $item['duration_hours'] }}h
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex gantt-row opacity-60">
                                <div class="w-72 sm:w-80 shrink-0 py-2 px-3.5 sm:px-4 pl-6 gantt-sidebar-cell flex items-center text-xs text-gray-400 dark:text-gray-500 italic">
                                    No operations scheduled in this period
                                </div>
                                <div class="flex-1 relative h-9">
                                    <div class="absolute inset-0 flex pointer-events-none">
                                        @foreach($columns as $col)
                                            <div class="flex-1 gantt-grid-line last:border-r-0 {{ $col['is_today'] ? 'gantt-today-col' : '' }}"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    @empty
                        <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-10 h-10 mx-auto text-gray-400 mb-2 opacity-40" />
                            <div class="font-semibold text-sm">No work centers found for current company</div>
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
            class="fixed bottom-6 right-6 z-40 max-w-sm p-4 bg-gray-900/95 backdrop-blur-md text-white rounded-xl shadow-2xl border border-white/10 pointer-events-none text-xs space-y-1.5"
        >
            <div class="font-bold text-sm text-primary-400" x-text="tooltip ? tooltip.mo_name + ' — ' + tooltip.name : ''"></div>
            <div class="text-gray-300"><span class="text-gray-400">Product:</span> <span class="font-medium" x-text="tooltip ? tooltip.product_name : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Quantity:</span> <span class="font-medium" x-text="tooltip ? tooltip.quantity + ' ' + tooltip.uom : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Schedule:</span> <span class="font-medium" x-text="tooltip ? tooltip.start_formatted + ' -> ' + tooltip.end_formatted : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Expected Duration:</span> <span class="font-medium" x-text="tooltip ? tooltip.duration_hours + ' hours' : ''"></span></div>
            <div class="flex items-center gap-2 pt-1 border-t border-white/10">
                <span class="text-gray-400">Status:</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="tooltip ? tooltip.color_theme.badge : ''" x-text="tooltip ? tooltip.state_label : ''"></span>
            </div>
        </div>

        {{-- Detail Modal --}}
        @if($selectedWo)
            <div
                class="fixed inset-0 z-50 overflow-y-auto bg-black/70 backdrop-blur-xs flex items-center justify-center p-4"
                wire:click.self="closeWorkOrderModal"
            >
                <div class="relative w-full max-w-2xl rounded-xl shadow-2xl border border-gray-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in-95 duration-200 bg-white dark:bg-gray-900">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.03]">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-950 dark:text-white">
                                    {{ $selectedWo->manufacturingOrder?->name }}: {{ $selectedWo->name }}
                                </h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Operation ID #{{ $selectedWo->id }}
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="closeWorkOrderModal"
                            class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition"
                        >
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-4 text-sm">
                        {{-- Metrics Cards --}}
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">Product</div>
                                <div class="font-bold mt-1 text-sm truncate text-gray-950 dark:text-white">
                                    {{ $selectedWo->manufacturingOrder?->product?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">Quantity</div>
                                <div class="font-bold mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ (float) $selectedWo->manufacturingOrder?->quantity }} {{ $selectedWo->manufacturingOrder?->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">Work Center</div>
                                <div class="font-bold mt-1 text-sm truncate text-gray-950 dark:text-white">
                                    {{ $selectedWo->workCenter?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-400">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getColor() : 'gray' }}">
                                        {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getLabel() : ucfirst($selectedWo->state) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Details --}}
                        <div class="grid grid-cols-2 gap-4 p-3.5 rounded-lg border gantt-tile">
                            <div>
                                <div class="text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Scheduled Start</div>
                                <div class="font-medium mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ $selectedWo->started_at ? $selectedWo->started_at->format('d M Y, H:i') : ($selectedWo->manufacturingOrder?->started_at ? $selectedWo->manufacturingOrder->started_at->format('d M Y, H:i') : 'Not scheduled') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Finished / Deadline</div>
                                <div class="font-medium mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ $selectedWo->finished_at ? $selectedWo->finished_at->format('d M Y, H:i') : ($selectedWo->manufacturingOrder?->deadline_at ? $selectedWo->manufacturingOrder->deadline_at->format('d M Y, H:i') : '—') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Expected Duration</div>
                                <div class="font-medium mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ (float) $selectedWo->expected_duration }}m ({{ round((float) $selectedWo->expected_duration / 60, 1) }}h)
                                </div>
                            </div>

                            <div>
                                <div class="text-[11px] font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Actual Duration</div>
                                <div class="font-medium mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ (float) $selectedWo->duration }}m
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-between px-6 py-3 border-t border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.03]">
                        <div class="flex items-center gap-2">
                            @if(in_array($selectedWo->state?->value ?? (string)$selectedWo->state, ['ready', 'waiting', 'pending']))
                                <button
                                    type="button"
                                    wire:click="startWorkOrder({{ $selectedWo->id }})"
                                    class="px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-500 transition shadow-xs"
                                >
                                    Start Work
                                </button>
                            @elseif(($selectedWo->state?->value ?? (string)$selectedWo->state) === 'progress')
                                <button
                                    type="button"
                                    wire:click="finishWorkOrder({{ $selectedWo->id }})"
                                    class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-500 transition shadow-xs"
                                >
                                    Finish Work
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if($selectedWo->manufacturing_order_id)
                                <a
                                    href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource::getUrl('view', ['record' => $selectedWo->manufacturing_order_id]) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 dark:bg-white/10 dark:text-gray-200 dark:border-white/10 dark:hover:bg-white/20 transition"
                                >
                                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                                    View MO
                                </a>
                            @endif

                            <button
                                type="button"
                                wire:click="closeWorkOrderModal"
                                class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/20 transition"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
