<x-filament-panels::page>
    @php
        $data = $this->timelineData;
        $stats = $data['stats'];
        $columns = $data['columns'];
        $rows = $data['rows'];
        $workCenters = $data['work_centers'] ?? collect();
        $selectedOrder = $this->selectedOrder;
        $selectedWo = $this->selectedWorkOrder;
        $minWidth = $viewMode === 'month' ? 'min-w-[1250px]' : 'min-w-[950px]';
    @endphp

    <style>
        /* Modern Scoped Gantt Design System */
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

        .gantt-subrow {
            background-color: #fafbfc;
            border-bottom: 1px solid #f1f5f9;
            transition: background-color 0.12s ease;
        }
        :is(.dark, [data-theme="dark"]) .gantt-subrow {
            background-color: rgba(255, 255, 255, 0.015) !important;
            border-bottom-color: rgba(255, 255, 255, 0.03) !important;
        }
        .gantt-subrow:hover {
            background-color: #f1f5f9;
        }
        :is(.dark, [data-theme="dark"]) .gantt-subrow:hover {
            background-color: rgba(255, 255, 255, 0.035) !important;
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
        .gantt-bar-confirmed { background: linear-gradient(135deg, #3b82f6, #2563eb); }
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
        {{-- Top Control Bar --}}
        <div class="gantt-card overflow-hidden">
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

                    {{-- Expand / Collapse All Toggle --}}
                    <div class="hidden sm:inline-flex items-center rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.04] p-0.5">
                        <button
                            type="button"
                            wire:click="expandAll"
                            class="px-2.5 py-1 text-[11px] font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition"
                        >
                            Expand All
                        </button>
                        <span class="text-gray-300 dark:text-white/10">|</span>
                        <button
                            type="button"
                            wire:click="collapseAll"
                            class="px-2.5 py-1 text-[11px] font-medium text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition"
                        >
                            Collapse
                        </button>
                    </div>
                </div>

                {{-- Right: Filters, Scale Switcher, Search --}}
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Work Center Dropdown Filter --}}
                    <select
                        wire:model.live="workCenterFilter"
                        class="gantt-select max-w-[150px]"
                    >
                        <option value="">All Work Centers</option>
                        @foreach($workCenters as $wc)
                            <option value="{{ $wc->id }}">{{ $wc->name }}</option>
                        @endforeach
                    </select>

                    {{-- Scale Switcher (Week / Month) --}}
                    <div class="inline-flex p-0.5 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.04]">
                        <button
                            type="button"
                            wire:click="setViewMode('week')"
                            class="px-3 py-1 text-xs font-semibold rounded-md transition {{ $viewMode === 'week' ? 'bg-primary-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
                        >
                            Week
                        </button>
                        <button
                            type="button"
                            wire:click="setViewMode('month')"
                            class="px-3 py-1 text-xs font-semibold rounded-md transition {{ $viewMode === 'month' ? 'bg-primary-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
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
                            class="py-1 pl-8 pr-3 text-xs rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 w-32 sm:w-40"
                        />
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="absolute w-3.5 h-3.5 text-gray-400 left-2.5 top-2 pointer-events-none" />
                    </div>

                    {{-- Plan All Unplanned Orders Action --}}
                    @if(!empty($stats['unplanned']))
                        <button
                            type="button"
                            wire:click="planAllVisibleOrders"
                            class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg shadow-xs transition"
                            title="Auto-plan all confirmed orders into available slots"
                        >
                            <x-filament::icon icon="heroicon-m-bolt" class="w-3.5 h-3.5" />
                            <span>Plan ({{ $stats['unplanned'] }})</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Metrics Sub-Bar (Clean Chips, NO harsh black borders) --}}
            <div class="px-4 py-2 border-t border-gray-100 dark:border-white/5 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="gantt-badge gantt-badge-neutral">
                        Total: <strong class="text-gray-900 dark:text-white font-bold ml-0.5">{{ $stats['total_orders'] }}</strong>
                    </span>

                    <span class="gantt-badge gantt-badge-indigo">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                        Planned: <strong class="ml-0.5">{{ $stats['planned'] }}</strong>
                    </span>

                    @if(!empty($stats['unplanned']))
                        <span class="gantt-badge gantt-badge-amber">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Unplanned: <strong class="ml-0.5">{{ $stats['unplanned'] }}</strong>
                        </span>
                    @endif

                    <span class="gantt-badge gantt-badge-amber">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        In Progress: <strong class="ml-0.5">{{ $stats['in_progress'] }}</strong>
                    </span>

                    <span class="gantt-badge gantt-badge-green">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Done: <strong class="ml-0.5">{{ $stats['done'] }}</strong>
                    </span>

                    @if(!empty($stats['overdue']))
                        <span class="gantt-badge gantt-badge-red">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Overdue: <strong class="ml-0.5">{{ $stats['overdue'] }}</strong>
                        </span>
                    @endif
                </div>

                <div class="text-gray-500 dark:text-gray-400 text-xs">
                    Planned Output: <strong class="text-gray-900 dark:text-white font-semibold">{{ $stats['total_qty'] }} Units</strong>
                </div>
            </div>
        </div>

        {{-- Gantt Matrix Card --}}
        <div class="gantt-card overflow-hidden">
            <div class="overflow-x-auto">
                <div class="{{ $minWidth }}">
                    {{-- Timeline Columns Header --}}
                    <div class="flex gantt-header">
                        <div class="w-80 shrink-0 px-4 py-2 text-xs font-bold tracking-wider text-gray-500 uppercase dark:text-gray-400 gantt-sidebar-cell flex items-center justify-between">
                            <span>Manufacturing Order</span>
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

                    {{-- Rows --}}
                    @forelse($rows as $row)
                        @php
                            $order = $row['order'];
                            $barClass = $row['bar_class'] ?? 'gantt-bar-confirmed';
                            $isExpanded = $row['is_expanded'];
                            $operations = $row['operations'];
                            $stateBadgeClass = match($row['state']) {
                                'progress'  => 'gantt-badge-amber',
                                'done'      => 'gantt-badge-green',
                                'confirmed' => 'gantt-badge-blue',
                                default     => 'gantt-badge-neutral',
                            };
                            $isBarVeryShort = $row['width_percent'] < 8;
                        @endphp

                        {{-- Parent MO Row --}}
                        <div class="flex gantt-row">
                            {{-- Sidebar Item --}}
                            <div class="w-80 shrink-0 p-2.5 px-4 gantt-sidebar-cell flex flex-col justify-center">
                                <div class="flex items-center justify-between gap-1.5">
                                    <div class="flex items-center gap-1.5 truncate">
                                        @if(count($operations) > 0)
                                            <button
                                                type="button"
                                                wire:click="toggleExpandOrder({{ $order->id }})"
                                                class="p-0.5 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition"
                                                title="{{ $isExpanded ? 'Collapse operations' : 'Expand operations' }}"
                                            >
                                                <svg class="w-3.5 h-3.5 transition-transform duration-150 {{ $isExpanded ? 'rotate-90 text-primary-500' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        @else
                                            <span class="w-3.5 inline-block"></span>
                                        @endif

                                        <span
                                            wire:click="openOrderModal({{ $order->id }})"
                                            class="font-bold text-xs text-gray-900 dark:text-white truncate cursor-pointer hover:text-primary-600 dark:hover:text-primary-400"
                                        >
                                            {{ $order->name }}
                                        </span>
                                    </div>

                                    {{-- Single Clean Status Badge --}}
                                    <span class="gantt-badge {{ $stateBadgeClass }}">
                                        {{ $row['state_label'] }}
                                    </span>
                                </div>

                                <div class="text-[11px] text-gray-500 dark:text-gray-400 truncate mt-0.5 pl-5 font-medium">
                                    {{ $order->product?->name }}
                                </div>

                                <div class="flex items-center justify-between text-[10px] text-gray-400 dark:text-gray-500 mt-1 pl-5">
                                    <span>{{ (float) $order->quantity }} {{ $order->product?->uom?->name }}</span>
                                    <span>•</span>
                                    <span>{{ $row['done_wo_count'] }}/{{ $row['work_orders_count'] }} Ops</span>
                                    <span>•</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ $row['progress_percent'] }}%</span>
                                </div>
                            </div>

                            {{-- Timeline Canvas Track for MO Bar --}}
                            <div class="flex-1 relative h-14">
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
                                    class="absolute top-2.5 bottom-2.5"
                                    style="left: {{ $row['left_percent'] }}%; width: {{ max(3.0, $row['width_percent']) }}%;"
                                >
                                    <div
                                        wire:click="openOrderModal({{ $order->id }})"
                                        class="w-full h-full gantt-bar {{ $barClass }}"
                                        x-on:mouseenter="tooltip = {
                                            name: '{{ $order->name }}',
                                            product: '{{ addslashes($order->product?->name ?? '') }}',
                                            quantity: '{{ (float) $order->quantity }} {{ $order->product?->uom?->name }}',
                                            start: '{{ $row['start_formatted'] }}',
                                            deadline: '{{ $row['deadline_formatted'] }}',
                                            progress: '{{ $row['progress_percent'] }}%',
                                            state: '{{ $row['state_label'] }}',
                                            is_planned: {{ $row['is_planned'] ? 'true' : 'false' }},
                                            is_overdue: {{ $row['is_overdue'] ? 'true' : 'false' }}
                                        }"
                                        x-on:mouseleave="tooltip = null"
                                    >
                                        @if(! $isBarVeryShort)
                                            <span class="truncate pr-1">
                                                {{ $order->name }}: {{ $order->product?->name }}
                                            </span>
                                            <span class="shrink-0 text-[10px] font-mono opacity-90">
                                                {{ $row['progress_percent'] }}%
                                            </span>
                                        @else
                                            <span class="mx-auto text-[9px] font-mono">
                                                {{ $row['progress_percent'] }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Expanded Sub-Rows: Operations under this MO --}}
                        @if($isExpanded)
                            @foreach($operations as $op)
                                @php
                                    $isOpVeryShort = $op['width_percent'] < 8;
                                    $opStateBadge = match($op['state']) {
                                        'progress' => 'gantt-badge-amber',
                                        'done'     => 'gantt-badge-green',
                                        'ready'    => 'gantt-badge-blue',
                                        default    => 'gantt-badge-neutral',
                                    };
                                @endphp
                                <div class="flex gantt-subrow">
                                    {{-- Sub-Row Left Item --}}
                                    <div class="w-80 shrink-0 py-1.5 px-4 pl-9 gantt-sidebar-cell flex flex-col justify-center">
                                        <div class="flex items-center justify-between gap-1">
                                            <div class="flex items-center gap-1.5 truncate">
                                                <span class="text-gray-300 dark:text-gray-600 text-xs select-none">↳</span>
                                                <span
                                                    wire:click="openWorkOrderModal({{ $op['id'] }})"
                                                    class="font-medium text-xs text-gray-800 dark:text-gray-200 truncate cursor-pointer hover:text-primary-600"
                                                    title="{{ $op['name'] }}"
                                                >
                                                    {{ $op['name'] }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-1 shrink-0">
                                                @if($op['is_blocked'])
                                                    <span class="gantt-badge gantt-badge-red text-[10px]" title="Waiting on preceding operation">
                                                        <x-filament::icon icon="heroicon-m-lock-closed" class="w-2.5 h-2.5" />
                                                        Blocked
                                                    </span>
                                                @endif

                                                <span class="gantt-badge {{ $opStateBadge }} text-[10px]">
                                                    {{ $op['state_label'] }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 pl-3 truncate">
                                            📍 {{ $op['work_center_name'] }} • ⏱ {{ $op['duration_hours'] }}h
                                        </div>
                                    </div>

                                    {{-- Sub-Row Timeline Track --}}
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

                                        {{-- Operation Bar --}}
                                        <div
                                            class="absolute top-1.5 bottom-1.5"
                                            style="left: {{ $op['left_percent'] }}%; width: {{ max(2.5, $op['width_percent']) }}%;"
                                        >
                                            <div
                                                wire:click="openWorkOrderModal({{ $op['id'] }})"
                                                class="w-full h-full gantt-bar {{ $op['bar_class'] }}"
                                                title="{{ $op['name'] }} ({{ $op['start_formatted'] }} -> {{ $op['end_formatted'] }})"
                                            >
                                                @if(! $isOpVeryShort)
                                                    <span class="truncate pr-1 text-[10px]">
                                                        {{ $op['name'] }}
                                                    </span>
                                                    <span class="shrink-0 text-[9px] font-mono opacity-80">
                                                        {{ $op['duration_hours'] }}h
                                                    </span>
                                                @else
                                                    <span class="mx-auto text-[8px] font-mono">
                                                        {{ $op['duration_hours'] }}h
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @empty
                        <div class="p-12 text-center text-gray-400">
                            <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-8 h-8 mx-auto text-gray-300 mb-1 opacity-50" />
                            <div class="font-medium text-xs">No manufacturing orders scheduled in this period</div>
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
            class="fixed bottom-6 right-6 z-50 max-w-xs p-3 bg-gray-900/95 backdrop-blur-md text-white rounded-xl shadow-xl border border-white/10 pointer-events-none text-xs space-y-1"
        >
            <div class="font-bold text-xs text-primary-400 flex items-center justify-between">
                <span x-text="tooltip ? tooltip.name : ''"></span>
                <template x-if="tooltip && tooltip.is_overdue">
                    <span class="text-rose-400 font-bold text-[10px] bg-rose-500/20 px-1 py-0.2 rounded">OVERDUE</span>
                </template>
            </div>
            <div class="text-gray-300 text-[11px]"><span class="text-gray-400">Product:</span> <span class="font-medium" x-text="tooltip ? tooltip.product : ''"></span></div>
            <div class="text-gray-300 text-[11px]"><span class="text-gray-400">Qty:</span> <span class="font-medium" x-text="tooltip ? tooltip.quantity : ''"></span></div>
            <div class="text-gray-300 text-[11px]"><span class="text-gray-400">Schedule:</span> <span class="font-medium" x-text="tooltip ? tooltip.start + ' → ' + tooltip.deadline : ''"></span></div>
            <div class="text-gray-300 text-[11px]"><span class="text-gray-400">Progress:</span> <span class="font-bold text-emerald-400" x-text="tooltip ? tooltip.progress : ''"></span></div>
            <div class="flex items-center gap-2 pt-1 border-t border-white/10 text-[10px]">
                <span class="text-gray-400">Status:</span>
                <span class="font-semibold text-primary-300" x-text="tooltip ? tooltip.state : ''"></span>
                <template x-if="tooltip && tooltip.is_planned">
                    <span class="text-indigo-400 font-semibold">• Planned</span>
                </template>
            </div>
        </div>

        {{-- Manufacturing Order Detail Modal (Redesigned & Clean) --}}
        @if($selectedOrder)
            <div
                class="gantt-modal-backdrop"
                wire:click.self="closeOrderModal"
            >
                <div class="gantt-modal-dialog">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">
                                        {{ $selectedOrder->name }}
                                    </h3>
                                    {{-- Status Chips --}}
                                    @php
                                        $moModalStatusBadge = match($selectedOrder->state?->value ?? (string)$selectedOrder->state) {
                                            'progress'  => 'gantt-badge-amber',
                                            'done'      => 'gantt-badge-green',
                                            'confirmed' => 'gantt-badge-blue',
                                            default     => 'gantt-badge-neutral',
                                        };
                                    @endphp
                                    <span class="gantt-badge {{ $moModalStatusBadge }}">
                                        {{ $selectedOrder->state instanceof \Webkul\Manufacturing\Enums\ManufacturingOrderState ? $selectedOrder->state->getLabel() : ucfirst($selectedOrder->state) }}
                                    </span>
                                    @if($selectedOrder->is_planned)
                                        <span class="gantt-badge gantt-badge-indigo">Planned</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
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
                    <div class="p-6 space-y-4 text-xs max-h-[75vh] overflow-y-auto">
                        {{-- Metrics Cards (Clean 3-Box Grid) --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="gantt-tile">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Output Progress</div>
                                <div class="font-bold text-sm text-gray-900 dark:text-white mt-1">
                                    {{ (float) $selectedOrder->quantity_producing }} / {{ (float) $selectedOrder->quantity }} {{ $selectedOrder->product?->uom?->name }}
                                </div>
                                <div class="w-full h-1.5 bg-gray-200 dark:bg-white/10 rounded-full mt-2 overflow-hidden">
                                    @php
                                        $qtyPct = $selectedOrder->quantity > 0 ? min(100, round(($selectedOrder->quantity_producing / $selectedOrder->quantity) * 100)) : 0;
                                    @endphp
                                    <div class="h-full bg-primary-600 rounded-full" style="width: {{ $qtyPct }}%"></div>
                                </div>
                            </div>

                            <div class="gantt-tile">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Scheduled Period</div>
                                <div class="font-semibold text-xs text-gray-800 dark:text-gray-200 mt-1">
                                    {{ $selectedOrder->started_at ? $selectedOrder->started_at->format('d M Y, H:i') : 'Not scheduled' }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    Deadline: {{ $selectedOrder->deadline_at ? $selectedOrder->deadline_at->format('d M Y, H:i') : 'No deadline' }}
                                </div>
                            </div>

                            <div class="gantt-tile">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Responsible & Ops</div>
                                <div class="font-semibold text-xs text-gray-800 dark:text-gray-200 mt-1 truncate">
                                    {{ $selectedOrder->assignedUser?->name ?? 'Unassigned' }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    Total Operations: {{ $selectedOrder->workOrders->count() }}
                                </div>
                            </div>
                        </div>

                        {{-- Operations Table --}}
                        <div class="space-y-2">
                            <div class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase tracking-wider">
                                Operations & Routing Steps
                            </div>

                            @if($selectedOrder->workOrders->isNotEmpty())
                                <div class="rounded-xl border border-gray-100 dark:border-white/5 overflow-hidden">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-50/70 dark:bg-white/[0.02] text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-white/5">
                                            <tr>
                                                <th class="px-3.5 py-2 text-left font-semibold">Operation</th>
                                                <th class="px-3.5 py-2 text-left font-semibold">Work Center</th>
                                                <th class="px-3.5 py-2 text-left font-semibold">Dependency</th>
                                                <th class="px-3.5 py-2 text-right font-semibold">Expected</th>
                                                <th class="px-3.5 py-2 text-center font-semibold">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                            @foreach($selectedOrder->workOrders as $wo)
                                                @php
                                                    $woRowBadge = match($wo->state?->value ?? (string)$wo->state) {
                                                        'progress' => 'gantt-badge-amber',
                                                        'done'     => 'gantt-badge-green',
                                                        'ready'    => 'gantt-badge-blue',
                                                        default    => 'gantt-badge-neutral',
                                                    };
                                                @endphp
                                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.015]">
                                                    <td class="px-3.5 py-2.5 font-medium text-gray-900 dark:text-white">
                                                        <button
                                                            type="button"
                                                            wire:click="openWorkOrderModal({{ $wo->id }})"
                                                            class="text-primary-600 hover:underline dark:text-primary-400 font-semibold"
                                                        >
                                                            {{ $wo->name }}
                                                        </button>
                                                    </td>
                                                    <td class="px-3.5 py-2.5 text-gray-600 dark:text-gray-300">
                                                        {{ $wo->workCenter?->name ?? '—' }}
                                                    </td>
                                                    <td class="px-3.5 py-2.5 text-gray-500">
                                                        @if($wo->blockedByWorkOrders->isNotEmpty())
                                                            <div class="flex flex-wrap gap-1">
                                                                @foreach($wo->blockedByWorkOrders as $blocker)
                                                                    <span class="gantt-badge {{ in_array($blocker->state?->value, ['done', 'cancel']) ? 'gantt-badge-green' : 'gantt-badge-red' }} text-[10px]">
                                                                        {{ $blocker->name }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="text-gray-400">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-3.5 py-2.5 text-right font-mono text-gray-600 dark:text-gray-400">
                                                        {{ (float) $wo->expected_duration }}m
                                                    </td>
                                                    <td class="px-3.5 py-2.5 text-center">
                                                        <span class="gantt-badge {{ $woRowBadge }} text-[10px]">
                                                            {{ $wo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $wo->state->getLabel() : ucfirst($wo->state) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="gantt-tile text-center text-gray-400 py-4">
                                    No operations configured for this order.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer with High-Contrast Action Buttons --}}
                    <div class="flex items-center justify-between px-6 py-3.5 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div>
                            @if(! $selectedOrder->is_planned && in_array($selectedOrder->state?->value ?? (string)$selectedOrder->state, ['draft', 'confirmed']))
                                <button
                                    type="button"
                                    wire:click="planOrder({{ $selectedOrder->id }})"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-xs transition"
                                >
                                    <x-filament::icon icon="heroicon-m-bolt" class="w-3.5 h-3.5" />
                                    Plan Order
                                </button>
                            @elseif($selectedOrder->is_planned && !in_array($selectedOrder->state?->value ?? (string)$selectedOrder->state, ['done', 'cancel']))
                                <button
                                    type="button"
                                    wire:click="unplanOrder({{ $selectedOrder->id }})"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-amber-800 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800 rounded-lg transition"
                                >
                                    <x-filament::icon icon="heroicon-m-arrow-path" class="w-3.5 h-3.5" />
                                    Unplan Order
                                </button>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <a
                                href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource::getUrl('view', ['record' => $selectedOrder->id]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs transition"
                            >
                                <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5 text-white" />
                                <span>Open MO</span>
                            </a>

                            <button
                                type="button"
                                wire:click="closeOrderModal"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 rounded-lg transition"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Work Order Detail Sub-Modal --}}
        @if($selectedWo)
            <div
                class="gantt-modal-backdrop"
                wire:click.self="closeWorkOrderModal"
            >
                <div class="gantt-modal-dialog">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $selectedWo->manufacturingOrder?->name }}: {{ $selectedWo->name }}
                                </h3>
                                <div class="text-xs text-gray-500">
                                    Station: <strong class="text-gray-800 dark:text-gray-200">{{ $selectedWo->workCenter?->name }}</strong>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="closeWorkOrderModal"
                            class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition"
                        >
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                        </button>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div class="grid grid-cols-3 gap-3">
                            <div class="gantt-tile">
                                <div class="text-[10px] font-semibold text-gray-400 uppercase">Product</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1 truncate">
                                    {{ $selectedWo->manufacturingOrder?->product?->name }}
                                </div>
                            </div>
                            <div class="gantt-tile">
                                <div class="text-[10px] font-semibold text-gray-400 uppercase">Quantity</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1">
                                    {{ (float) $selectedWo->manufacturingOrder?->quantity }} {{ $selectedWo->manufacturingOrder?->product?->uom?->name }}
                                </div>
                            </div>
                            <div class="gantt-tile">
                                <div class="text-[10px] font-semibold text-gray-400 uppercase">Duration</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1 font-mono">
                                    {{ (float) $selectedWo->expected_duration }}m expected
                                </div>
                            </div>
                        </div>

                        <div class="gantt-tile space-y-2">
                            <div class="text-[10px] font-semibold text-gray-400 uppercase">Sequential Dependencies</div>
                            @if($selectedWo->blockedByWorkOrders->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($selectedWo->blockedByWorkOrders as $blocker)
                                        <span class="gantt-badge {{ in_array($blocker->state?->value, ['done', 'cancel']) ? 'gantt-badge-green' : 'gantt-badge-red' }}">
                                            <x-filament::icon icon="heroicon-m-lock-closed" class="w-3 h-3" />
                                            Blocked by: {{ $blocker->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-emerald-600 dark:text-emerald-400 font-medium">✓ No blocking operations</div>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 px-6 py-3 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
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
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 rounded-lg transition"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
