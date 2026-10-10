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
        /* Scoped High-End Gantt Design System */
        .gantt-root {
            font-family: inherit;
        }

        .gantt-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }
        :is(.dark, [data-theme="dark"]) .gantt-card {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .gantt-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-header {
            background-color: #1e293b !important;
            border-bottom-color: #334155 !important;
        }

        .gantt-header-cell {
            border-right: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-header-cell {
            border-right-color: #1e293b !important;
        }

        .gantt-sidebar-cell {
            border-right: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-sidebar-cell {
            border-right-color: #1e293b !important;
        }

        .gantt-wc-header-row {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        :is(.dark, [data-theme="dark"]) .gantt-wc-header-row {
            background-color: rgba(255, 255, 255, 0.025) !important;
            border-bottom-color: #1e293b !important;
        }

        .gantt-row {
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.12s ease;
        }
        :is(.dark, [data-theme="dark"]) .gantt-row {
            border-bottom-color: rgba(255, 255, 255, 0.04) !important;
        }
        .gantt-row:hover {
            background-color: #f8fafc;
        }
        :is(.dark, [data-theme="dark"]) .gantt-row:hover {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }

        .gantt-grid-line {
            border-right: 1px solid #f1f5f9;
        }
        :is(.dark, [data-theme="dark"]) .gantt-grid-line {
            border-right-color: rgba(255, 255, 255, 0.04) !important;
        }

        .gantt-weekend-col {
            background-color: rgba(241, 245, 249, 0.55);
        }
        :is(.dark, [data-theme="dark"]) .gantt-weekend-col {
            background-color: rgba(255, 255, 255, 0.015) !important;
        }

        .gantt-today-col {
            background-color: rgba(59, 130, 246, 0.04) !important;
        }
        :is(.dark, [data-theme="dark"]) .gantt-today-col {
            background-color: rgba(59, 130, 246, 0.06) !important;
        }

        /* Clean High-End Status Chips (No Black Outlines) */
        .gantt-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.3;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .gantt-badge-neutral { background-color: #f1f5f9; color: #475569; }
        .gantt-badge-blue    { background-color: #eff6ff; color: #2563eb; }
        .gantt-badge-amber   { background-color: #fef3c7; color: #d97706; }
        .gantt-badge-green   { background-color: #ecfdf5; color: #059669; }
        .gantt-badge-red     { background-color: #fef2f2; color: #dc2626; }
        .gantt-badge-indigo  { background-color: #eef2ff; color: #4f46e5; }

        :is(.dark, [data-theme="dark"]) .gantt-badge-neutral { background-color: rgba(255,255,255,0.06); color: #cbd5e1; }
        :is(.dark, [data-theme="dark"]) .gantt-badge-blue    { background-color: rgba(37,99,235,0.15); color: #93c5fd; }
        :is(.dark, [data-theme="dark"]) .gantt-badge-amber   { background-color: rgba(217,119,6,0.15); color: #fcd34d; }
        :is(.dark, [data-theme="dark"]) .gantt-badge-green   { background-color: rgba(5,150,105,0.15); color: #6ee7b7; }
        :is(.dark, [data-theme="dark"]) .gantt-badge-red     { background-color: rgba(220,38,38,0.15); color: #fca5a5; }
        :is(.dark, [data-theme="dark"]) .gantt-badge-indigo  { background-color: rgba(79,70,229,0.15); color: #a5b4fc; }

        /* Work Center Workload Capacity Pill */
        .gantt-capacity-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.3;
            border: 1px solid transparent;
        }
        .gantt-capacity-ok {
            background-color: #ecfdf5;
            color: #059669;
        }
        .gantt-capacity-warn {
            background-color: #fef3c7;
            color: #d97706;
        }
        .gantt-capacity-danger {
            background-color: #fef2f2;
            color: #dc2626;
        }
        :is(.dark, [data-theme="dark"]) .gantt-capacity-ok {
            background-color: rgba(5, 150, 105, 0.15);
            color: #6ee7b7;
        }
        :is(.dark, [data-theme="dark"]) .gantt-capacity-warn {
            background-color: rgba(217, 119, 6, 0.15);
            color: #fcd34d;
        }
        :is(.dark, [data-theme="dark"]) .gantt-capacity-danger {
            background-color: rgba(220, 38, 38, 0.15);
            color: #fca5a5;
        }

        /* Sleek Timeline Bars */
        .gantt-bar {
            height: 24px;
            border-radius: 6px;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8px;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
            transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
        }
        .gantt-bar:hover {
            transform: translateY(-1px);
            filter: brightness(1.06);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
            z-index: 30;
        }

        .gantt-bar-progress  { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .gantt-bar-ready     { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .gantt-bar-done      { background: linear-gradient(135deg, #10b981, #059669); }
        .gantt-bar-overdue   { background: linear-gradient(135deg, #f43f5e, #e11d48); }
        .gantt-bar-waiting   { background: linear-gradient(135deg, #64748b, #475569); }

        /* Custom Dropdown Styling */
        .gantt-select {
            appearance: none;
            -webkit-appearance: none;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 28px 5px 10px;
            font-size: 12px;
            font-weight: 500;
            color: #1e293b;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 8px center;
            background-repeat: no-repeat;
            background-size: 14px 14px;
            cursor: pointer;
            transition: border-color 0.15s ease;
        }
        .gantt-select:hover {
            border-color: #cbd5e1;
        }
        :is(.dark, [data-theme="dark"]) .gantt-select {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        }

        /* Modal Overlay & Card Styling */
        .gantt-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background-color: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .gantt-modal-dialog {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 680px;
            overflow: hidden;
            animation: ganttModalZoom 0.15s ease-out;
        }
        :is(.dark, [data-theme="dark"]) .gantt-modal-dialog {
            background-color: #0f172a;
            border-color: #1e293b;
            box-shadow: 0 25px 30px -5px rgba(0, 0, 0, 0.5);
        }
        @keyframes ganttModalZoom {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }

        .gantt-tile {
            background-color: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 10px;
            padding: 10px 14px;
        }
        :is(.dark, [data-theme="dark"]) .gantt-tile {
            background-color: rgba(255, 255, 255, 0.025);
            border-color: rgba(255, 255, 255, 0.06);
        }
    </style>

    <div class="space-y-4 gantt-root" x-data="{ tooltip: null }">
        {{-- Unified Control Bar --}}
        <div class="gantt-card overflow-hidden">
            {{-- Top Controls Row --}}
            <div class="p-3 sm:px-4 flex flex-wrap items-center justify-between gap-3">
                {{-- Left: Navigation & Period Title --}}
                <div class="flex items-center gap-3">
                    <div class="inline-flex items-center rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.04] p-0.5">
                        <button
                            type="button"
                            wire:click="previous"
                            class="p-1.5 rounded-md text-gray-500 hover:text-gray-900 hover:bg-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/10 transition"
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
                            class="p-1.5 rounded-md text-gray-500 hover:text-gray-900 hover:bg-white dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/10 transition"
                            title="Next"
                        >
                            <x-filament::icon icon="heroicon-m-chevron-right" class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white">
                        <x-filament::icon icon="heroicon-o-calendar" class="w-4 h-4 text-primary-500" />
                        <span>{{ $data['period_title'] }}</span>
                    </div>
                </div>

                {{-- Right: Filters & Controls --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Work Center Dropdown Filter --}}
                    <select
                        wire:model.live="workCenterFilter"
                        class="gantt-select max-w-[160px]"
                    >
                        <option value="">All Work Centers</option>
                        @foreach($allWorkCenters as $wcOpt)
                            <option value="{{ $wcOpt['id'] }}">{{ $wcOpt['name'] }}</option>
                        @endforeach
                    </select>

                    {{-- Scale Switcher (Day / Week / Month) --}}
                    <div class="inline-flex p-0.5 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.04]">
                        <button
                            type="button"
                            wire:click="setViewMode('day')"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition {{ $viewMode === 'day' ? 'bg-primary-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Day
                        </button>
                        <button
                            type="button"
                            wire:click="setViewMode('week')"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition {{ $viewMode === 'week' ? 'bg-primary-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Week
                        </button>
                        <button
                            type="button"
                            wire:click="setViewMode('month')"
                            class="px-2.5 py-1 text-xs font-semibold rounded-md transition {{ $viewMode === 'month' ? 'bg-primary-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Month
                        </button>
                    </div>

                    {{-- Status Filter --}}
                    <select
                        wire:model.live="statusFilter"
                        class="gantt-select max-w-[120px]"
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
                            class="py-1 pl-8 pr-3 text-xs rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 w-32 sm:w-40"
                        />
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="absolute w-3.5 h-3.5 text-gray-400 left-2.5 top-2 pointer-events-none" />
                    </div>
                </div>
            </div>

            {{-- Metrics Sub-Bar (Clean Chips, NO harsh black borders) --}}
            <div class="px-4 py-2 border-t border-gray-100 dark:border-white/5 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="gantt-badge gantt-badge-neutral">
                        Work Orders: <strong class="text-gray-900 dark:text-white ml-0.5">{{ $stats['total_orders'] }}</strong>
                    </span>

                    <span class="gantt-badge gantt-badge-blue">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Ready: <strong class="ml-0.5">{{ $stats['ready'] }}</strong>
                    </span>

                    <span class="gantt-badge gantt-badge-amber">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        In Progress: <strong class="ml-0.5">{{ $stats['in_progress'] }}</strong>
                    </span>

                    <span class="gantt-badge gantt-badge-green">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Done: <strong class="ml-0.5">{{ $stats['done'] }}</strong>
                    </span>
                </div>

                <div class="text-gray-500 dark:text-gray-400 text-xs">
                    Planned Workload: <strong class="text-gray-900 dark:text-white font-semibold">{{ $stats['planned_hours'] }} Hours</strong>
                </div>
            </div>
        </div>

        {{-- Gantt Matrix Card --}}
        <div class="gantt-card overflow-hidden">
            <div class="overflow-x-auto">
                <div class="{{ $minWidth }}">
                    {{-- Header Row --}}
                    <div class="flex gantt-header">
                        <div class="w-80 sm:w-96 shrink-0 px-4 py-2 text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400 gantt-sidebar-cell flex items-center justify-between">
                            <span>Work Center / Operations</span>
                            <span class="text-[10px] text-gray-400 font-normal lowercase">({{ count($rows) }})</span>
                        </div>

                        <div class="flex-1 flex">
                            @foreach($columns as $col)
                                @php
                                    $isWeekend = $col['is_weekend'] ?? false;
                                    $colBg = $col['is_today'] ? 'gantt-today-col' : ($isWeekend ? 'gantt-weekend-col' : '');
                                @endphp
                                <div class="flex-1 px-1 py-1 text-center gantt-header-cell last:border-r-0 {{ $colBg }}">
                                    @if($col['is_today'])
                                        <div class="w-5 h-5 mx-auto rounded-full bg-primary-600 text-white font-bold text-[10px] flex items-center justify-center shadow-xs">
                                            {{ $col['label'] }}
                                        </div>
                                    @else
                                        <div class="text-xs font-bold {{ $isWeekend ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $col['label'] }}
                                        </div>
                                    @endif

                                    @if(!empty($col['sublabel']))
                                        <div class="text-[9px] font-medium leading-none mt-0.5 {{ $col['is_today'] ? 'text-primary-600 font-bold' : ($isWeekend ? 'text-gray-400 dark:text-gray-600' : 'text-gray-400 dark:text-gray-500') }}">
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
                            $capacityClass = $isOverloaded
                                ? 'gantt-capacity-danger'
                                : ($utilization > 75 ? 'gantt-capacity-warn' : 'gantt-capacity-ok');
                        @endphp

                        {{-- Work Center Header Row with Capacity Pill --}}
                        <div class="flex gantt-wc-header-row">
                            <div class="w-80 sm:w-96 shrink-0 px-4 py-2 gantt-sidebar-cell flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2 truncate">
                                    <button
                                        type="button"
                                        wire:click="toggleWorkCenterBlocked({{ $wc->id }})"
                                        class="w-2.5 h-2.5 rounded-full shrink-0 {{ $wc->working_state === \Webkul\Manufacturing\Enums\WorkCenterWorkingState::BLOCKED ? 'bg-rose-500 animate-pulse' : 'bg-emerald-500' }}"
                                        title="{{ $wc->working_state === \Webkul\Manufacturing\Enums\WorkCenterWorkingState::BLOCKED ? 'Blocked (Click to unblock)' : 'Normal (Click to block)' }}"
                                    ></button>

                                    <span class="font-bold text-xs text-gray-900 dark:text-white truncate">
                                        {{ $wc->name }}
                                    </span>

                                    @if($wc->code)
                                        <span class="px-1.5 py-0.5 text-[10px] font-mono font-semibold bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 rounded">
                                            {{ $wc->code }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Soft Capacity Pill (NO black border) --}}
                                <div class="gantt-capacity-pill {{ $capacityClass }}" title="Workload: {{ $row['planned_hours'] }}h planned / {{ $row['available_hours'] }}h capacity">
                                    @if($isOverloaded)
                                        <x-filament::icon icon="heroicon-m-exclamation-triangle" class="w-3 h-3 shrink-0" />
                                    @endif
                                    <span>{{ $row['planned_hours'] }}h / {{ $row['available_hours'] }}h</span>
                                    <span class="opacity-80">({{ $utilization }}%)</span>
                                </div>
                            </div>

                            {{-- Work Center Background Track --}}
                            <div class="flex-1 relative h-8">
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

                        {{-- Work Order Rows under this Work Center --}}
                        @forelse($items as $item)
                            @php
                                $barClass = match($item['state']) {
                                    'progress' => 'gantt-bar-progress',
                                    'ready'    => 'gantt-bar-ready',
                                    'done'     => 'gantt-bar-done',
                                    'cancel'   => 'gantt-bar-overdue',
                                    default    => 'gantt-bar-waiting',
                                };
                                $isShortBar = $item['width_percent'] < 12;
                                $itemStateBadge = match($item['state']) {
                                    'progress' => 'gantt-badge-amber',
                                    'ready'    => 'gantt-badge-blue',
                                    'done'     => 'gantt-badge-green',
                                    'cancel'   => 'gantt-badge-red',
                                    default    => 'gantt-badge-neutral',
                                };
                                // Concise state label for sidebar
                                $shortStateLabel = match($item['state']) {
                                    'progress' => 'In Progress',
                                    'ready'    => 'Ready',
                                    'done'     => 'Done',
                                    'cancel'   => 'Cancelled',
                                    'pending'  => 'Pending',
                                    'waiting'  => 'Waiting',
                                    default    => ucfirst($item['state']),
                                };
                            @endphp
                            <div class="flex gantt-row">
                                {{-- Sidebar Item --}}
                                <div class="w-80 sm:w-96 shrink-0 py-2 px-4 pl-7 gantt-sidebar-cell flex flex-col justify-center">
                                    <div class="flex items-center justify-between gap-1.5">
                                        <div class="flex items-center gap-1.5 truncate">
                                            <span class="text-gray-300 dark:text-gray-600 text-xs select-none">↳</span>
                                            <span
                                                wire:click="openWorkOrderModal({{ $item['id'] }})"
                                                class="font-semibold text-xs text-gray-900 dark:text-white truncate cursor-pointer hover:text-primary-600 dark:hover:text-primary-400"
                                                title="{{ $item['mo_name'] }}: {{ $item['name'] }}"
                                            >
                                                {{ $item['mo_name'] }}: {{ $item['name'] }}
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-1 shrink-0">
                                            @if($item['is_blocked'])
                                                <span class="gantt-badge gantt-badge-red text-[10px]" title="Waiting on preceding operation">
                                                    <x-filament::icon icon="heroicon-m-lock-closed" class="w-2.5 h-2.5" />
                                                    Blocked
                                                </span>
                                            @endif

                                            <span class="gantt-badge {{ $itemStateBadge }}">
                                                {{ $shortStateLabel }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5 pl-3.5 flex items-center justify-between">
                                        <span class="truncate">{{ $item['product_name'] }} • {{ (float) $item['quantity'] }} {{ $item['uom'] }}</span>
                                        <span class="font-mono text-gray-400 dark:text-gray-500 shrink-0 ml-2">{{ $item['duration_hours'] }}h</span>
                                    </div>
                                </div>

                                {{-- Timeline Track --}}
                                <div class="flex-1 relative h-12">
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

                                    {{-- Gantt Bar --}}
                                    <div
                                        class="absolute top-2 bottom-2"
                                        style="left: {{ $item['left_percent'] }}%; width: {{ max(3.0, $item['width_percent']) }}%;"
                                    >
                                        <div
                                            wire:click="openWorkOrderModal({{ $item['id'] }})"
                                            class="w-full h-full gantt-bar {{ $barClass }}"
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
                                                is_blocked: {{ $item['is_blocked'] ? 'true' : 'false' }}
                                            }"
                                            x-on:mouseleave="tooltip = null"
                                        >
                                            @if(! $isShortBar)
                                                <div class="flex items-center gap-1 truncate pr-1">
                                                    @if($item['is_blocked'])
                                                        <x-filament::icon icon="heroicon-m-lock-closed" class="w-3 h-3 text-white/80 shrink-0" />
                                                    @endif
                                                    <span class="truncate">
                                                        {{ $item['mo_name'] }}: {{ $item['name'] }}
                                                    </span>
                                                </div>
                                                <span class="shrink-0 text-[10px] font-mono opacity-90">
                                                    {{ $item['duration_hours'] }}h
                                                </span>
                                            @else
                                                <span class="mx-auto text-[10px] font-mono">
                                                    {{ $item['duration_hours'] }}h
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex gantt-row">
                                <div class="w-80 sm:w-96 shrink-0 py-2.5 px-4 gantt-sidebar-cell text-xs text-gray-400 italic">
                                    No work orders scheduled on this station.
                                </div>
                                <div class="flex-1 relative h-9">
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
            <div class="text-gray-300"><span class="text-gray-400">Schedule:</span> <span class="font-medium" x-text="tooltip ? tooltip.start_formatted + ' → ' + tooltip.end_formatted : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Expected Duration:</span> <span class="font-medium" x-text="tooltip ? tooltip.duration_hours + ' hours' : ''"></span></div>
            <template x-if="tooltip && tooltip.is_blocked">
                <div class="text-rose-400 font-bold flex items-center gap-1">
                    <x-filament::icon icon="heroicon-m-lock-closed" class="w-3.5 h-3.5" />
                    BLOCKED: Waiting on preceding operation
                </div>
            </template>
            <div class="flex items-center gap-2 pt-1 border-t border-white/10">
                <span class="text-gray-400">Status:</span>
                <span class="font-semibold text-gray-200" x-text="tooltip ? tooltip.state_label : ''"></span>
            </div>
        </div>

        {{-- Work Order Detail Modal (Redesigned & Clean) --}}
        @if($selectedWo)
            <div
                class="gantt-modal-backdrop"
                wire:click.self="closeWorkOrderModal"
            >
                <div class="gantt-modal-dialog">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">
                                        {{ $selectedWo->manufacturingOrder?->name }}: {{ $selectedWo->name }}
                                    </h3>
                                    @php
                                        $woModalBadge = match($selectedWo->state?->value ?? (string)$selectedWo->state) {
                                            'progress' => 'gantt-badge-amber',
                                            'ready'    => 'gantt-badge-blue',
                                            'done'     => 'gantt-badge-green',
                                            'cancel'   => 'gantt-badge-red',
                                            default    => 'gantt-badge-neutral',
                                        };
                                    @endphp
                                    <span class="gantt-badge {{ $woModalBadge }}">
                                        {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getLabel() : ucfirst($selectedWo->state) }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Station: <strong class="text-gray-800 dark:text-gray-200">{{ $selectedWo->workCenter?->name }}</strong>
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
                    <div class="p-6 space-y-4 text-xs max-h-[75vh] overflow-y-auto">
                        {{-- Clean 3-Box Summary Grid --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="gantt-tile">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Product</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1 truncate">
                                    {{ $selectedWo->manufacturingOrder?->product?->name ?? '—' }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    {{ (float) $selectedWo->manufacturingOrder?->quantity }} {{ $selectedWo->manufacturingOrder?->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="gantt-tile">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Duration</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1 font-mono">
                                    {{ (float) $selectedWo->expected_duration }}m expected
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1 font-mono">
                                    Actual: {{ (float) $selectedWo->duration }}m
                                </div>
                            </div>

                            <div class="gantt-tile">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Scheduled Time</div>
                                <div class="font-semibold text-xs text-gray-800 dark:text-gray-200 mt-1">
                                    {{ $selectedWo->started_at ? $selectedWo->started_at->format('d M Y, H:i') : 'Not scheduled' }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    Deadline: {{ $selectedWo->manufacturingOrder?->deadline_at ? $selectedWo->manufacturingOrder->deadline_at->format('d M Y, H:i') : '—' }}
                                </div>
                            </div>
                        </div>

                        {{-- Dependencies Section --}}
                        <div class="gantt-tile space-y-2">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Sequential Dependencies</div>
                            @if($selectedWo->blockedByWorkOrders->isNotEmpty())
                                <div class="space-y-1">
                                    <div class="text-[11px] text-gray-500">Preceding Operations (Must finish first):</div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($selectedWo->blockedByWorkOrders as $blocker)
                                            @php
                                                $isBlockerDone = in_array($blocker->state?->value, ['done', 'cancel']);
                                            @endphp
                                            <span class="gantt-badge {{ $isBlockerDone ? 'gantt-badge-green' : 'gantt-badge-red' }}">
                                                <x-filament::icon icon="{{ $isBlockerDone ? 'heroicon-m-check-circle' : 'heroicon-m-lock-closed' }}" class="w-3 h-3" />
                                                <span>{{ $blocker->name }}</span>
                                                <span class="opacity-75">({{ $blocker->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $blocker->state->getLabel() : $blocker->state }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-emerald-600 dark:text-emerald-400 font-medium">✓ No blocking operations (Ready to start)</div>
                            @endif

                            @if($selectedWo->dependentWorkOrders->isNotEmpty())
                                <div class="pt-2 border-t border-gray-100 dark:border-white/5 space-y-1">
                                    <div class="text-[11px] text-gray-500">Next Dependent Operations:</div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($selectedWo->dependentWorkOrders as $nextWo)
                                            <span class="gantt-badge gantt-badge-neutral">
                                                <span>{{ $nextWo->name }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Alternative Work Centers Reassignment --}}
                        @if($selectedWo->workCenter?->alternativeWorkCenters?->isNotEmpty() && !in_array($selectedWo->state?->value ?? (string)$selectedWo->state, ['done', 'cancel']))
                            <div class="gantt-tile space-y-2">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                                    Alternative Work Centers (Workload Balancing)
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    Station is currently <strong class="text-gray-900 dark:text-white">{{ $selectedWo->workCenter->name }}</strong>. Reassign to balance workload:
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    @foreach($selectedWo->workCenter->alternativeWorkCenters as $altWc)
                                        <button
                                            type="button"
                                            wire:click="reassignWorkCenter({{ $selectedWo->id }}, {{ $altWc->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition"
                                        >
                                            <x-filament::icon icon="heroicon-m-arrows-right-left" class="w-3.5 h-3.5" />
                                            <span>Move to {{ $altWc->name }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer with High-Contrast Action Buttons --}}
                    <div class="flex items-center justify-between px-6 py-3.5 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-2">
                            @if(in_array($selectedWo->state?->value ?? (string)$selectedWo->state, ['ready', 'waiting', 'pending']))
                                <button
                                    type="button"
                                    wire:click="startWorkOrder({{ $selectedWo->id }})"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs transition"
                                >
                                    <x-filament::icon icon="heroicon-m-play" class="w-3.5 h-3.5" />
                                    <span>Start Operation</span>
                                </button>
                            @elseif(($selectedWo->state?->value ?? (string)$selectedWo->state) === 'progress')
                                <button
                                    type="button"
                                    wire:click="pauseWorkOrder({{ $selectedWo->id }})"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-amber-800 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-lg transition"
                                >
                                    <x-filament::icon icon="heroicon-m-pause" class="w-3.5 h-3.5" />
                                    <span>Pause</span>
                                </button>
                                <button
                                    type="button"
                                    wire:click="finishWorkOrder({{ $selectedWo->id }})"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs transition"
                                >
                                    <x-filament::icon icon="heroicon-m-check" class="w-3.5 h-3.5" />
                                    <span>Mark as Finished</span>
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            @if($selectedWo->manufacturing_order_id)
                                <a
                                    href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource::getUrl('view', ['record' => $selectedWo->manufacturing_order_id]) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs transition"
                                >
                                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5 text-white" />
                                    <span>Open MO</span>
                                </a>
                            @endif

                            <button
                                type="button"
                                wire:click="closeWorkOrderModal"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition"
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
