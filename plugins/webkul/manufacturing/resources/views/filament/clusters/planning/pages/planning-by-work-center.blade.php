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
        .gantt-bar-progress {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            border: 1px solid #f59e0b !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.4) !important;
        }
        .gantt-bar-ready, .gantt-bar-confirmed {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
            border: 1px solid #60a5fa !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4) !important;
        }
        .gantt-bar-done {
            background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
            border: 1px solid #34d399 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(5, 150, 105, 0.4) !important;
        }
        .gantt-bar-overdue {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%) !important;
            border: 1px solid #fb7185 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(225, 29, 72, 0.5) !important;
        }
        .gantt-bar-waiting, .gantt-bar-pending {
            background: linear-gradient(135deg, #475569 0%, #334155 100%) !important;
            border: 1px solid #64748b !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(71, 85, 105, 0.3) !important;
        }
        .dark .gantt-tile {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }
        .gantt-tile {
            background-color: #f8fafc;
            border-color: #e2e8f0;
        }
        .dark .gantt-text-white {
            color: #ffffff !important;
        }
        .dark .gantt-text-muted {
            color: #94a3b8 !important;
        }
        .gantt-track-bg {
            background-color: transparent !important;
        }
    </style>

    <div class="space-y-4" x-data="{ tooltip: null }">
        {{-- Compact Executive KPI Strip --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-white/[0.03] text-xs">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Work Orders:</span>
                    <span class="font-extrabold text-gray-950 dark:text-white text-sm">{{ $stats['total_orders'] }}</span>
                </div>
                <span class="text-gray-300 dark:text-gray-700">•</span>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Ready:</span>
                    <span class="font-extrabold text-blue-600 dark:text-blue-400 text-sm">{{ $stats['ready'] }}</span>
                </div>
                <span class="text-gray-300 dark:text-gray-700">•</span>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <span class="text-gray-500 dark:text-gray-400 font-medium">In Progress:</span>
                    <span class="font-extrabold text-amber-600 dark:text-amber-400 text-sm">{{ $stats['in_progress'] }}</span>
                </div>
                <span class="text-gray-300 dark:text-gray-700">•</span>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Done:</span>
                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm">{{ $stats['done'] }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-gray-500 dark:text-gray-400 font-medium">Planned Workload:</span>
                <span class="font-extrabold text-indigo-600 dark:text-indigo-400 text-sm">{{ $stats['planned_hours'] }} Hours</span>
            </div>
        </div>

        {{-- Toolbar Controls --}}
        <div class="flex flex-col gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-white/[0.03] lg:flex-row lg:items-center lg:justify-between">
            {{-- Date Navigation & Period --}}
            <div class="flex items-center gap-3">
                <div class="inline-flex rounded-lg border border-gray-300 dark:border-white/15 overflow-hidden shadow-2xs">
                    <button
                        type="button"
                        wire:click="previous"
                        class="px-2.5 py-1.5 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/10 transition"
                        title="Previous"
                    >
                        <x-filament::icon icon="heroicon-o-chevron-left" class="w-4 h-4" />
                    </button>
                    <button
                        type="button"
                        wire:click="today"
                        class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400 border-x border-gray-300 dark:border-white/15 hover:bg-gray-50 dark:hover:bg-white/10 transition"
                    >
                        Today
                    </button>
                    <button
                        type="button"
                        wire:click="next"
                        class="px-2.5 py-1.5 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/10 transition"
                        title="Next"
                    >
                        <x-filament::icon icon="heroicon-o-chevron-right" class="w-4 h-4" />
                    </button>
                </div>

                <div class="flex items-center gap-2 text-base font-extrabold text-gray-950 dark:text-white">
                    <x-filament::icon icon="heroicon-o-calendar" class="w-5 h-5 text-gray-400" />
                    {{ $data['period_title'] }}
                </div>
            </div>

            {{-- Scale & Filter Controls --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- View Scale Switcher --}}
                <div class="inline-flex p-1 bg-gray-100 rounded-lg dark:bg-white/5 border border-gray-200 dark:border-white/10">
                    <button
                        type="button"
                        wire:click="setViewMode('day')"
                        style="{{ $viewMode === 'day' ? 'background-color: #2563eb !important; color: #ffffff !important; font-weight: 700;' : '' }}"
                        class="px-3 py-1 text-xs rounded-md transition text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                    >
                        Day
                    </button>
                    <button
                        type="button"
                        wire:click="setViewMode('week')"
                        style="{{ $viewMode === 'week' ? 'background-color: #2563eb !important; color: #ffffff !important; font-weight: 700;' : '' }}"
                        class="px-3 py-1 text-xs rounded-md transition text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                    >
                        Week
                    </button>
                    <button
                        type="button"
                        wire:click="setViewMode('month')"
                        style="{{ $viewMode === 'month' ? 'background-color: #2563eb !important; color: #ffffff !important; font-weight: 700;' : '' }}"
                        class="px-3 py-1 text-xs rounded-md transition text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
                    >
                        Month
                    </button>
                </div>

                {{-- Status Filter --}}
                <select
                    wire:model.live="statusFilter"
                    class="py-1.5 pl-3 pr-8 text-xs font-medium border border-gray-300 rounded-lg bg-white dark:bg-gray-900 dark:border-white/15 dark:text-white focus:ring-2 focus:ring-primary-500"
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
                        placeholder="Search WO, MO, product..."
                        class="py-1.5 pl-8 pr-3 text-xs border border-gray-300 rounded-lg bg-white dark:bg-gray-900 dark:border-white/15 dark:text-white focus:ring-2 focus:ring-primary-500 w-44 sm:w-52"
                    />
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="absolute w-4 h-4 text-gray-400 left-2.5 top-2" />
                </div>
            </div>
        </div>

        {{-- Gantt Matrix Section --}}
        <x-filament::section class="overflow-hidden">
            <div class="overflow-x-auto -m-6">
                <div class="{{ $minWidth }}">
                    {{-- Header Row --}}
                    <div class="flex border-b border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.04]">
                        {{-- Work Center Column Header --}}
                        <div class="w-80 shrink-0 px-4 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase border-r border-gray-200 dark:text-gray-400 dark:border-white/10">
                            Work Center
                        </div>

                        {{-- Timeline Columns Header --}}
                        <div class="flex-1 flex">
                            @foreach($columns as $col)
                                <div class="flex-1 px-1 py-2 text-center border-r border-gray-200 last:border-r-0 dark:border-white/5 {{ $col['is_today'] ? 'bg-primary-500/10 text-primary-600 dark:text-primary-400 font-extrabold border-b-2 border-primary-500' : 'text-gray-500 dark:text-gray-400' }}">
                                    <div class="text-xs font-bold">{{ $col['label'] }}</div>
                                    @if(!empty($col['sublabel']))
                                        <div class="text-[10px] opacity-75">{{ $col['sublabel'] }}</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Rows --}}
                    @forelse($rows as $row)
                        @php
                            $wc = $row['work_center'];
                            $items = $row['items'];
                        @endphp
                        <div class="flex border-b border-gray-100 last:border-b-0 hover:bg-gray-50/40 dark:border-white/5 dark:hover:bg-white/[0.02] transition">
                            {{-- Work Center Info Card --}}
                            <div class="w-80 shrink-0 p-3.5 border-r border-gray-200 dark:border-white/10 flex flex-col justify-center">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="font-extrabold text-sm text-gray-950 dark:text-white truncate">
                                        {{ $wc->name }}
                                    </div>
                                    @if($wc->code)
                                        <span class="px-1.5 py-0.5 text-[10px] font-mono font-bold bg-gray-100 text-gray-700 rounded dark:bg-white/10 dark:text-gray-300 border border-gray-200 dark:border-white/10">
                                            {{ $wc->code }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-500 dark:text-gray-400 pt-1 border-t border-gray-100 dark:border-white/5">
                                    <span class="inline-flex items-center gap-1 font-medium">
                                        <span class="w-2 h-2 rounded-full {{ $wc->working_state === \Webkul\Manufacturing\Enums\WorkCenterWorkingState::BLOCKED ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                        {{ $wc->working_state instanceof \Webkul\Manufacturing\Enums\WorkCenterWorkingState ? $wc->working_state->getLabel() : 'Normal' }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $row['planned_hours'] }}h planned</span>
                                    <span>•</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ $row['active_orders'] }} WO</span>
                                </div>
                            </div>

                            {{-- Timeline Canvas Track (Transparent, NOT white!) --}}
                            <div class="flex-1 relative h-16 gantt-track-bg">
                                {{-- Background Grid Lines --}}
                                <div class="absolute inset-0 flex pointer-events-none">
                                    @foreach($columns as $col)
                                        <div class="flex-1 border-r border-gray-100 last:border-r-0 dark:border-white/[0.04] {{ $col['is_today'] ? 'bg-primary-500/[0.04]' : '' }}"></div>
                                    @endforeach
                                </div>

                                {{-- Work Order Gantt Bars --}}
                                @foreach($items as $item)
                                    @php
                                        $barClass = match($item['state']) {
                                            'progress' => 'gantt-bar-progress',
                                            'ready'    => 'gantt-bar-ready',
                                            'done'     => 'gantt-bar-done',
                                            'cancel'   => 'gantt-bar-overdue',
                                            default    => 'gantt-bar-waiting',
                                        };
                                    @endphp
                                    <div
                                        wire:click="openWorkOrderModal({{ $item['id'] }})"
                                        class="absolute top-2.5 bottom-2.5 rounded-xl px-2.5 py-1 cursor-pointer select-none transition-all duration-150 hover:scale-[1.01] hover:shadow-2xl hover:z-20 flex items-center justify-between gap-1.5 overflow-hidden {{ $barClass }}"
                                        style="left: {{ $item['left_percent'] }}%; width: {{ $item['width_percent'] }}%;"
                                        x-on:mouseenter="tooltip = {{ json_encode($item) }}"
                                        x-on:mouseleave="tooltip = null"
                                    >
                                        <div class="flex items-center gap-1.5 truncate">
                                            @if($item['is_in_progress'])
                                                <span class="w-2 h-2 rounded-full bg-white animate-ping shrink-0"></span>
                                            @elseif($item['is_done'])
                                                <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                            <span class="font-extrabold text-xs text-white truncate drop-shadow-xs">
                                                {{ $item['mo_name'] }}: {{ $item['name'] }}
                                            </span>
                                        </div>
                                        <span class="shrink-0 text-[10px] font-bold bg-black/30 text-white rounded px-1.5 py-0.5">
                                            {{ $item['duration_hours'] }}h
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-10 h-10 mx-auto text-gray-400 mb-2 opacity-40" />
                            <div class="font-semibold text-sm">No work centers found for current company</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </x-filament::section>

        {{-- Floating Tooltip --}}
        <div
            x-show="tooltip"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="fixed bottom-6 right-6 z-40 max-w-sm p-4 bg-gray-900/95 backdrop-blur-md text-white rounded-2xl shadow-2xl border border-white/10 pointer-events-none text-xs space-y-1.5"
        >
            <div class="font-extrabold text-sm text-primary-400" x-text="tooltip ? tooltip.mo_name + ' — ' + tooltip.name : ''"></div>
            <div class="text-gray-300"><span class="text-gray-400">Product:</span> <span class="font-medium" x-text="tooltip ? tooltip.product_name : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Quantity:</span> <span class="font-medium" x-text="tooltip ? tooltip.quantity + ' ' + tooltip.uom : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Schedule:</span> <span class="font-medium" x-text="tooltip ? tooltip.start_formatted + ' -> ' + tooltip.end_formatted : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Expected Duration:</span> <span class="font-medium" x-text="tooltip ? tooltip.duration_hours + ' hours' : ''"></span></div>
            <div class="flex items-center gap-2 pt-1 border-t border-white/10">
                <span class="text-gray-400">Status:</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold" :class="tooltip ? tooltip.color_theme.badge : ''" x-text="tooltip ? tooltip.state_label : ''"></span>
            </div>
        </div>

        {{-- Detail Slide-Over / Modal (Theme-immune dark slate cards) --}}
        @if($selectedWo)
            <div
                class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-xs flex items-center justify-center p-4"
                wire:click.self="closeWorkOrderModal"
            >
                <div class="relative w-full max-w-2xl rounded-2xl shadow-2xl border border-gray-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in-95 duration-200 bg-white dark:bg-gray-900">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.04]">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-gray-950 dark:text-white">
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
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                        >
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-5 text-sm">
                        {{-- 4 Metrics Cards (Pristine dark slate, immune to white-box bug) --}}
                        <div class="grid grid-cols-2 gap-3.5">
                            <div class="p-3.5 rounded-xl border gantt-tile">
                                <div class="text-xs font-semibold uppercase tracking-wider gantt-text-muted">Product</div>
                                <div class="font-extrabold mt-1 text-sm truncate gantt-text-white">
                                    {{ $selectedWo->manufacturingOrder?->product?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="p-3.5 rounded-xl border gantt-tile">
                                <div class="text-xs font-semibold uppercase tracking-wider gantt-text-muted">Quantity to Produce</div>
                                <div class="font-extrabold mt-1 text-sm gantt-text-white">
                                    {{ (float) $selectedWo->manufacturingOrder?->quantity }} {{ $selectedWo->manufacturingOrder?->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3.5 rounded-xl border gantt-tile">
                                <div class="text-xs font-semibold uppercase tracking-wider gantt-text-muted">Work Center</div>
                                <div class="font-extrabold mt-1 text-sm truncate gantt-text-white">
                                    {{ $selectedWo->workCenter?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="p-3.5 rounded-xl border gantt-tile">
                                <div class="text-xs font-semibold uppercase tracking-wider gantt-text-muted">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getColor() : 'gray' }}">
                                        {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getLabel() : ucfirst($selectedWo->state) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Details --}}
                        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl border gantt-tile">
                            <div>
                                <div class="text-xs font-semibold gantt-text-muted uppercase tracking-wider">Scheduled Start</div>
                                <div class="font-bold mt-1 gantt-text-white">
                                    {{ $selectedWo->started_at ? $selectedWo->started_at->format('d M Y, H:i') : ($selectedWo->manufacturingOrder?->started_at ? $selectedWo->manufacturingOrder->started_at->format('d M Y, H:i') : 'Not scheduled') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold gantt-text-muted uppercase tracking-wider">Finished / Deadline</div>
                                <div class="font-bold mt-1 gantt-text-white">
                                    {{ $selectedWo->finished_at ? $selectedWo->finished_at->format('d M Y, H:i') : ($selectedWo->manufacturingOrder?->deadline_at ? $selectedWo->manufacturingOrder->deadline_at->format('d M Y, H:i') : '—') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold gantt-text-muted uppercase tracking-wider">Expected Duration</div>
                                <div class="font-bold mt-1 gantt-text-white">
                                    {{ (float) $selectedWo->expected_duration }} minutes ({{ round((float) $selectedWo->expected_duration / 60, 1) }}h)
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold gantt-text-muted uppercase tracking-wider">Actual Duration</div>
                                <div class="font-bold mt-1 gantt-text-white">
                                    {{ (float) $selectedWo->duration }} minutes
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.04]">
                        <div class="flex items-center gap-2">
                            @if(in_array($selectedWo->state?->value ?? (string)$selectedWo->state, ['ready', 'waiting', 'pending']))
                                <button
                                    type="button"
                                    wire:click="startWorkOrder({{ $selectedWo->id }})"
                                    class="px-4 py-2 text-xs font-bold text-white bg-primary-600 rounded-xl hover:bg-primary-500 transition shadow-sm"
                                >
                                    Start Work
                                </button>
                            @elseif(($selectedWo->state?->value ?? (string)$selectedWo->state) === 'progress')
                                <button
                                    type="button"
                                    wire:click="finishWorkOrder({{ $selectedWo->id }})"
                                    class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-500 transition shadow-sm"
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
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-white/10 dark:text-gray-200 dark:border-white/10 dark:hover:bg-white/20 transition"
                                >
                                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                                    View MO
                                </a>
                            @endif

                            <a
                                href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource::getUrl('view', ['record' => $selectedWo->id]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-white/10 dark:text-gray-200 dark:border-white/10 dark:hover:bg-white/20 transition"
                            >
                                <x-filament::icon icon="heroicon-o-eye" class="w-3.5 h-3.5" />
                                View WO
                            </a>

                            <button
                                type="button"
                                wire:click="closeWorkOrderModal"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 dark:bg-white/10 dark:text-gray-200 dark:hover:bg-white/20 transition"
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
