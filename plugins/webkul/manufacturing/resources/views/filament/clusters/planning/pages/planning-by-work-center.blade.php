<x-filament-panels::page>
    @php
        $data = $this->timelineData;
        $stats = $data['stats'];
        $columns = $data['columns'];
        $rows = $data['rows'];
        $selectedWo = $this->selectedWorkOrder;
        $allWorkCenters = $data['all_work_centers'] ?? [];
        $minWidth = match($viewMode) {
            'day'   => 'min-w-[1300px]',
            'month' => 'min-w-[1300px]',
            default => 'min-w-[950px]',
        };
    @endphp

    <style>
        /* Scoped Gantt Design Tokens */
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

        .gantt-wc-header-row {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-wc-header-row {
            background-color: rgba(255, 255, 255, 0.03) !important;
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
        .gantt-bar-ready, .gantt-bar-confirmed {
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

                {{-- Right: WorkCenter Filter, Scale Switcher, Status, Search --}}
                <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
                    {{-- Work Center Dropdown Filter --}}
                    <select
                        wire:model.live="workCenterFilter"
                        class="gantt-select py-1 pl-3 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800/80 text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 cursor-pointer max-w-[160px]"
                    >
                        <option value="">All Work Centers</option>
                        @foreach($allWorkCenters as $wcOpt)
                            <option value="{{ $wcOpt['id'] }}">{{ $wcOpt['name'] }}</option>
                        @endforeach
                    </select>

                    {{-- Scale Switcher --}}
                    <div class="inline-flex p-0.5 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.04]">
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
                        class="gantt-select py-1 pl-3 text-xs font-medium rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800/80 text-gray-800 dark:text-gray-200 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 cursor-pointer"
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
                            class="py-1 pl-8 pr-3 text-xs rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800/80 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 w-32 sm:w-44"
                        />
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="absolute w-3.5 h-3.5 text-gray-400 left-2.5 top-2 pointer-events-none" />
                    </div>
                </div>
            </div>

            {{-- Seamless Metrics Sub-Bar --}}
            <div class="px-4 py-2 border-t border-gray-100 dark:border-white/5 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-700 dark:bg-white/[0.05] dark:text-gray-300">
                        Work Orders: <strong class="ml-1 text-gray-950 dark:text-white">{{ $stats['total_orders'] }}</strong>
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Ready: <strong>{{ $stats['ready'] }}</strong>
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        In Progress: <strong>{{ $stats['in_progress'] }}</strong>
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Done: <strong>{{ $stats['done'] }}</strong>
                    </span>
                </div>

                <div class="text-gray-500 dark:text-gray-400 text-xs">
                    Planned Workload: <strong class="text-gray-950 dark:text-white font-semibold">{{ $stats['planned_hours'] }} Hours</strong>
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
                        <div class="w-80 sm:w-96 shrink-0 px-4 py-2.5 text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400 gantt-sidebar-cell flex items-center justify-between">
                            <span>Work Center / Operations</span>
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

                    {{-- Work Centers & Scheduled Work Orders --}}
                    @forelse($rows as $row)
                        @php
                            $wc = $row['work_center'];
                            $items = $row['items'];
                            $utilization = $row['utilization_percent'];
                            $isOverloaded = $row['is_overloaded'];
                            $utilBadgeColor = $isOverloaded
                                ? 'bg-rose-500/20 text-rose-500 border-rose-500/30'
                                : ($utilization > 75 ? 'bg-amber-500/20 text-amber-500 border-amber-500/30' : 'bg-emerald-500/20 text-emerald-500 border-emerald-500/30');
                        @endphp

                        {{-- Work Center Group Header Row with Capacity / Utilization Meter (Odoo Style) --}}
                        <div class="flex gantt-wc-header-row">
                            <div class="w-80 sm:w-96 shrink-0 px-3.5 sm:px-4 py-2 gantt-sidebar-cell flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 truncate">
                                    {{-- Work Center Working State Dot with Click-to-Toggle --}}
                                    <button
                                        type="button"
                                        wire:click="toggleWorkCenterBlocked({{ $wc->id }})"
                                        class="w-2.5 h-2.5 rounded-full shrink-0 {{ $wc->working_state === \Webkul\Manufacturing\Enums\WorkCenterWorkingState::BLOCKED ? 'bg-rose-500 animate-pulse' : 'bg-emerald-500' }}"
                                        title="{{ $wc->working_state === \Webkul\Manufacturing\Enums\WorkCenterWorkingState::BLOCKED ? 'BLOCKED (Click to unblock)' : 'Normal (Click to block)' }}"
                                    ></button>

                                    <span class="font-bold text-xs text-gray-950 dark:text-white truncate">
                                        {{ $wc->name }}
                                    </span>
                                    @if($wc->code)
                                        <span class="px-1.5 py-0.2 text-[9px] font-mono font-bold bg-gray-200 text-gray-700 rounded dark:bg-white/10 dark:text-gray-300 border border-gray-300 dark:border-white/10">
                                            {{ $wc->code }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Odoo Gantt Capacity Progress Bar Pill --}}
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $utilBadgeColor }}" title="Workload: {{ $row['planned_hours'] }}h planned / {{ $row['available_hours'] }}h capacity">
                                        @if($isOverloaded)
                                            <x-filament::icon icon="heroicon-m-exclamation-triangle" class="w-2.5 h-2.5 text-rose-500 shrink-0" />
                                        @endif
                                        <span>{{ $row['planned_hours'] }}h / {{ $row['available_hours'] }}h</span>
                                        <span class="opacity-80">({{ $utilization }}%)</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Work Center Header Background Track --}}
                            <div class="flex-1 relative h-9">
                                <div class="absolute inset-0 flex pointer-events-none">
                                    @foreach($columns as $col)
                                        @php
                                            $isWeekend = $col['is_weekend'] ?? false;
                                            $colBg = $col['is_today'] ? 'gantt-today-col' : ($isWeekend ? 'gantt-weekend-col' : '');
                                        @endphp
                                        <div class="flex-1 gantt-grid-line last:border-r-0 {{ $colBg }}"></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Scheduled Work Orders under this Work Center --}}
                        @forelse($items as $item)
                            @php
                                $barClass = match($item['state']) {
                                    'progress' => 'gantt-bar-progress',
                                    'ready'    => 'gantt-bar-ready',
                                    'done'     => 'gantt-bar-done',
                                    'cancel'   => 'gantt-bar-overdue',
                                    default    => 'gantt-bar-waiting',
                                };
                                $isShortBar = $item['width_percent'] < 14;
                            @endphp
                            <div class="flex gantt-row">
                                {{-- Sub-Row Left Item --}}
                                <div class="w-80 sm:w-96 shrink-0 py-2 px-3.5 sm:px-4 pl-6 gantt-sidebar-cell flex flex-col justify-center">
                                    <div class="flex items-center justify-between gap-1.5">
                                        <div class="flex items-center gap-1.5 truncate">
                                            <span class="text-gray-400 dark:text-gray-600 font-mono text-xs select-none">↳</span>
                                            <span
                                                wire:click="openWorkOrderModal({{ $item['id'] }})"
                                                class="font-semibold text-xs text-gray-900 dark:text-gray-200 truncate cursor-pointer hover:text-primary-600 dark:hover:text-primary-400"
                                                title="{{ $item['mo_name'] }}: {{ $item['name'] }}"
                                            >
                                                {{ $item['mo_name'] }}: {{ $item['name'] }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-1 shrink-0">
                                            {{-- Dependency lock badge if blocked --}}
                                            @if($item['is_blocked'])
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[9px] font-semibold bg-rose-500/10 text-rose-500 border border-rose-500/25" title="Waiting on preceding operation">
                                                    <x-filament::icon icon="heroicon-m-lock-closed" class="w-2.5 h-2.5" />
                                                    Blocked
                                                </span>
                                            @endif

                                            {{-- Status Pill --}}
                                            @if($item['state'] === 'progress')
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-full text-[9px] font-semibold bg-amber-500/10 text-amber-500 border border-amber-500/25">
                                                    <span class="w-1 h-1 rounded-full bg-amber-500 animate-pulse"></span>
                                                    In Progress
                                                </span>
                                            @elseif($item['state'] === 'done')
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-full text-[9px] font-semibold bg-emerald-500/10 text-emerald-500 border border-emerald-500/25">
                                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                    Done
                                                </span>
                                            @elseif($item['state'] === 'ready')
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-full text-[9px] font-semibold bg-blue-500/10 text-blue-500 border border-blue-500/25">
                                                    <span class="w-1 h-1 rounded-full bg-blue-500"></span>
                                                    Ready
                                                </span>
                                            @else
                                                <span class="px-1.5 py-0.2 text-[9px] font-semibold rounded-full {{ $item['color_theme']['badge'] ?? 'bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300' }}">
                                                    {{ $item['state_label'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5 pl-3.5 flex items-center justify-between">
                                        <span class="truncate">{{ $item['product_name'] }} • {{ (float) $item['quantity'] }} {{ $item['uom'] }}</span>
                                        <span class="font-mono text-gray-400 shrink-0">{{ $item['duration_hours'] }}h</span>
                                    </div>
                                </div>

                                {{-- Sub-Row Timeline Track --}}
                                <div class="flex-1 relative h-11">
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
                                        class="absolute top-1.5 bottom-1.5"
                                        style="left: {{ $item['left_percent'] }}%; width: {{ max(3.0, $item['width_percent']) }}%;"
                                    >
                                        {{-- Clickable Gantt Bar --}}
                                        <div
                                            wire:click="openWorkOrderModal({{ $item['id'] }})"
                                            class="w-full h-full px-2 flex items-center justify-between gap-1 overflow-hidden gantt-bar {{ $barClass }}"
                                            x-on:mouseenter="tooltip = {
                                                mo_name: '{{ $item['mo_name'] }}',
                                                name: '{{ addslashes($item['name']) }}',
                                                product_name: '{{ addslashes($item['product_name']) }}',
                                                quantity: '{{ $item['quantity'] }}',
                                                uom: '{{ $item['uom'] }}',
                                                start_formatted: '{{ $item['start_formatted'] }}',
                                                end_formatted: '{{ $item['end_formatted'] }}',
                                                duration_hours: '{{ $item['duration_hours'] }}',
                                                state_label: '{{ $item['state_label'] }}',
                                                is_blocked: {{ $item['is_blocked'] ? 'true' : 'false' }},
                                                color_theme: {{ json_encode($item['color_theme']) }}
                                            }"
                                            x-on:mouseleave="tooltip = null"
                                        >
                                            <div class="flex items-center gap-1 truncate">
                                                @if($item['is_blocked'])
                                                    <x-filament::icon icon="heroicon-m-lock-closed" class="w-2.5 h-2.5 text-white/80 shrink-0" />
                                                @endif
                                                <span class="font-bold text-[11px] text-white truncate drop-shadow-xs">
                                                    {{ $item['mo_name'] }}: {{ $item['name'] }}
                                                </span>
                                            </div>

                                            <span class="shrink-0 text-[9px] font-mono bg-black/25 text-white rounded px-1 py-0.2 leading-none">
                                                {{ $item['duration_hours'] }}h
                                            </span>
                                        </div>

                                        {{-- Exterior Label for Short Bars --}}
                                        @if($isShortBar)
                                            <div class="absolute left-full ml-2 top-0 bottom-0 flex items-center pointer-events-none whitespace-nowrap z-20">
                                                <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 drop-shadow-xs flex items-center gap-1">
                                                    <span>{{ $item['mo_name'] }}:</span>
                                                    <span class="text-gray-500 font-medium">{{ $item['name'] }}</span>
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex gantt-row">
                                <div class="w-80 sm:w-96 shrink-0 py-2 px-4 gantt-sidebar-cell text-xs text-gray-400 italic">
                                    No work orders scheduled on this station.
                                </div>
                                <div class="flex-1 relative h-8">
                                    <div class="absolute inset-0 flex pointer-events-none">
                                        @foreach($columns as $col)
                                            <div class="flex-1 gantt-grid-line last:border-r-0"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    @empty
                        <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-10 h-10 mx-auto text-gray-400 mb-2 opacity-40" />
                            <div class="font-semibold text-sm">No work centers found matching criteria</div>
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
            <div class="font-bold text-sm text-primary-400" x-text="tooltip ? tooltip.mo_name + ' — ' + tooltip.name : ''"></div>
            <div class="text-gray-300"><span class="text-gray-400">Product:</span> <span class="font-medium" x-text="tooltip ? tooltip.product_name : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Quantity:</span> <span class="font-medium" x-text="tooltip ? tooltip.quantity + ' ' + tooltip.uom : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Schedule:</span> <span class="font-medium" x-text="tooltip ? tooltip.start_formatted + ' -> ' + tooltip.end_formatted : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Expected Duration:</span> <span class="font-medium" x-text="tooltip ? tooltip.duration_hours + ' hours' : ''"></span></div>
            <template x-if="tooltip && tooltip.is_blocked">
                <div class="text-rose-400 font-bold flex items-center gap-1">
                    <x-filament::icon icon="heroicon-m-lock-closed" class="w-3 h-3" />
                    BLOCKED: Waiting on preceding operation
                </div>
            </template>
            <div class="flex items-center gap-2 pt-1 border-t border-white/10">
                <span class="text-gray-400">Status:</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="tooltip ? tooltip.color_theme.badge : ''" x-text="tooltip ? tooltip.state_label : ''"></span>
            </div>
        </div>

        {{-- Work Order Detail Modal with Direct Actions & Alternative Work Centers --}}
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
                                    Station: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $selectedWo->workCenter?->name }}</span>
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
                    <div class="p-6 space-y-4 text-sm max-h-[75vh] overflow-y-auto">
                        {{-- Metrics Cards --}}
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Product</div>
                                <div class="font-bold mt-1 text-sm truncate text-gray-950 dark:text-white">
                                    {{ $selectedWo->manufacturingOrder?->product?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Quantity</div>
                                <div class="font-bold mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ (float) $selectedWo->manufacturingOrder?->quantity }} {{ $selectedWo->manufacturingOrder?->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Expected</div>
                                <div class="font-bold mt-1 text-sm text-gray-950 dark:text-white font-mono">
                                    {{ (float) $selectedWo->expected_duration }}m ({{ round((float)$selectedWo->expected_duration / 60, 1) }}h)
                                </div>
                            </div>

                            <div class="p-3 rounded-lg border gantt-tile">
                                <div class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getColor() : 'gray' }}">
                                        {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getLabel() : ucfirst($selectedWo->state) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Dependencies Section --}}
                        <div class="p-3.5 rounded-lg border gantt-tile space-y-2">
                            <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Sequential Dependencies</div>
                            @if($selectedWo->blockedByWorkOrders->isNotEmpty())
                                <div class="space-y-1">
                                    <div class="text-xs text-gray-500">Preceding Operations (Must finish first):</div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($selectedWo->blockedByWorkOrders as $blocker)
                                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded text-xs {{ in_array($blocker->state?->value, ['done', 'cancel']) ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border border-rose-500/25 font-bold' }}">
                                                <x-filament::icon icon="{{ in_array($blocker->state?->value, ['done', 'cancel']) ? 'heroicon-m-check-circle' : 'heroicon-m-lock-closed' }}" class="w-3.5 h-3.5" />
                                                <span>{{ $blocker->name }}</span>
                                                <span class="text-[10px] opacity-80">({{ $blocker->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $blocker->state->getLabel() : $blocker->state }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">✓ No blocking operations (Ready to start)</div>
                            @endif

                            @if($selectedWo->dependentWorkOrders->isNotEmpty())
                                <div class="pt-2 border-t border-gray-100 dark:border-white/5 space-y-1">
                                    <div class="text-xs text-gray-500">Next Dependent Operations:</div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($selectedWo->dependentWorkOrders as $nextWo)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] bg-gray-100 text-gray-700 dark:bg-white/10 dark:text-gray-300">
                                                <span>{{ $nextWo->name }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Alternative Work Centers Reassignment (Odoo MRP Feature) --}}
                        @if($selectedWo->workCenter?->alternativeWorkCenters?->isNotEmpty() && !in_array($selectedWo->state?->value ?? (string)$selectedWo->state, ['done', 'cancel']))
                            <div class="p-3.5 rounded-lg border gantt-tile space-y-2">
                                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">
                                    Alternative Work Centers (Workload Balancing)
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    Current station is <strong class="text-gray-900 dark:text-white">{{ $selectedWo->workCenter->name }}</strong>. If this station is overloaded, move this operation to:
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    @foreach($selectedWo->workCenter->alternativeWorkCenters as $altWc)
                                        <button
                                            type="button"
                                            wire:click="reassignWorkCenter({{ $selectedWo->id }}, {{ $altWc->id }})"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 dark:bg-primary-950/40 dark:text-primary-300 border border-primary-200 dark:border-primary-800 transition"
                                        >
                                            <x-filament::icon icon="heroicon-m-arrows-right-left" class="w-3.5 h-3.5" />
                                            <span>Move to {{ $altWc->name }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Schedule Details --}}
                        <div class="grid grid-cols-2 gap-4 p-3.5 rounded-lg border gantt-tile">
                            <div>
                                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Scheduled Start</div>
                                <div class="font-medium mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ $selectedWo->started_at ? $selectedWo->started_at->format('d M Y, H:i') : 'Not scheduled' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Finished / Deadline</div>
                                <div class="font-medium mt-1 text-sm text-gray-950 dark:text-white">
                                    {{ $selectedWo->finished_at ? $selectedWo->finished_at->format('d M Y, H:i') : ($selectedWo->manufacturingOrder?->deadline_at ? $selectedWo->manufacturingOrder->deadline_at->format('d M Y, H:i') : '—') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer with Execution Controls --}}
                    <div class="flex items-center justify-between px-6 py-3 border-t border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.03]">
                        <div class="flex items-center gap-2">
                            @if(in_array($selectedWo->state?->value ?? (string)$selectedWo->state, ['ready', 'waiting', 'pending']))
                                <button
                                    type="button"
                                    wire:click="startWorkOrder({{ $selectedWo->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-500 transition shadow-xs"
                                >
                                    <x-filament::icon icon="heroicon-m-play" class="w-3.5 h-3.5" />
                                    Start Operation
                                </button>
                            @elseif(($selectedWo->state?->value ?? (string)$selectedWo->state) === 'progress')
                                <button
                                    type="button"
                                    wire:click="pauseWorkOrder({{ $selectedWo->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-amber-700 bg-amber-100 hover:bg-amber-200 dark:bg-amber-900/30 dark:text-amber-300 rounded-lg transition shadow-xs"
                                >
                                    <x-filament::icon icon="heroicon-m-pause" class="w-3.5 h-3.5" />
                                    Pause
                                </button>
                                <button
                                    type="button"
                                    wire:click="finishWorkOrder({{ $selectedWo->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-500 transition shadow-xs"
                                >
                                    <x-filament::icon icon="heroicon-m-check" class="w-3.5 h-3.5" />
                                    Mark as Finished
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
