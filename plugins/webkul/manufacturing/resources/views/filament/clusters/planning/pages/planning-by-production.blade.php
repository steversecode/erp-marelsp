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
        .gantt-bar-progress {
            background: linear-gradient(135deg, #d97706 0%, #b45309 100%) !important;
            border: 1px solid #f59e0b !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.4) !important;
        }
        .gantt-bar-confirmed, .gantt-bar-ready {
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
        {{-- Compact Executive KPI Strip (Replaces massive empty boxes) --}}
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-white/[0.03] text-xs">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-1.5">
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Total Orders:</span>
                    <span class="font-extrabold text-gray-900 dark:text-white text-sm">{{ $stats['total_orders'] }}</span>
                </div>
                <span class="text-gray-300 dark:text-gray-700">•</span>
                <div class="flex items-center gap-1.5">
                    <span class="inline-flex w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span class="text-gray-500 dark:text-gray-400 font-medium">Confirmed:</span>
                    <span class="font-extrabold text-blue-600 dark:text-blue-400 text-sm">{{ $stats['confirmed'] }}</span>
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
                @if($stats['overdue'] > 0)
                    <span class="text-gray-300 dark:text-gray-700">•</span>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        <span class="text-rose-500 font-bold uppercase tracking-wider text-[11px]">Overdue:</span>
                        <span class="font-extrabold text-rose-500 text-sm">{{ $stats['overdue'] }}</span>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <span class="text-gray-500 dark:text-gray-400 font-medium">Planned Output:</span>
                <span class="font-extrabold text-indigo-600 dark:text-indigo-400 text-sm">{{ $stats['total_qty'] }} Units</span>
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
                        {{-- MO Column Header --}}
                        <div class="w-80 shrink-0 px-4 py-3 text-xs font-bold tracking-wider text-gray-500 uppercase border-r border-gray-200 dark:text-gray-400 dark:border-white/10">
                            Manufacturing Order
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
                            $order = $row['order'];
                            $theme = $row['color_theme'];
                            $barClass = $row['bar_class'] ?? 'gantt-bar-confirmed';
                        @endphp
                        <div class="flex border-b border-gray-100 last:border-b-0 hover:bg-gray-50/40 dark:border-white/5 dark:hover:bg-white/[0.02] transition">
                            {{-- MO Left Card --}}
                            <div class="w-80 shrink-0 p-3.5 border-r border-gray-200 dark:border-white/10 flex flex-col justify-center">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="font-extrabold text-sm text-gray-950 dark:text-white truncate">
                                        {{ $order->name }}
                                    </div>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $theme['badge'] }}">
                                        {{ $row['state_label'] }}
                                    </span>
                                </div>

                                <div class="text-xs text-gray-600 dark:text-gray-300 truncate mt-1 font-medium">
                                    {{ $order->product?->name }}
                                </div>

                                <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500 mt-1.5 pt-1 border-t border-gray-100 dark:border-white/5">
                                    <span>{{ (float) $order->quantity }} {{ $order->product?->uom?->name }}</span>
                                    <span>•</span>
                                    <span>{{ $row['done_wo_count'] }}/{{ $row['work_orders_count'] }} Ops</span>
                                    <span>•</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ $row['progress_percent'] }}%</span>
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

                                {{-- Vibrant Gantt Bar --}}
                                <div
                                    wire:click="openOrderModal({{ $order->id }})"
                                    class="absolute top-2.5 bottom-2.5 rounded-xl px-3 py-1 cursor-pointer select-none transition-all duration-150 hover:scale-[1.008] hover:shadow-2xl hover:z-20 flex flex-col justify-between overflow-hidden {{ $barClass }}"
                                    style="left: {{ $row['left_percent'] }}%; width: {{ $row['width_percent'] }}%;"
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
                                    <div class="flex items-center justify-between gap-2 overflow-hidden h-full">
                                        <div class="flex items-center gap-1.5 truncate">
                                            @if($row['state'] === 'progress')
                                                <span class="w-2 h-2 rounded-full bg-white animate-ping shrink-0"></span>
                                            @elseif($row['state'] === 'done')
                                                <svg class="w-3.5 h-3.5 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                            <span class="font-extrabold text-xs text-white truncate drop-shadow-xs">
                                                {{ $order->name }}: {{ $order->product?->name }}
                                            </span>
                                        </div>

                                        <span class="shrink-0 text-[10px] font-bold bg-black/30 text-white rounded px-1.5 py-0.5">
                                            {{ $row['progress_percent'] }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-10 h-10 mx-auto text-gray-400 mb-2 opacity-40" />
                            <div class="font-semibold text-sm">No manufacturing orders scheduled in this period</div>
                            <div class="text-xs text-gray-400 mt-1">Try switching to another month or changing filters.</div>
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
            <div class="font-extrabold text-sm text-primary-400 flex items-center justify-between">
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

        {{-- Detail Slide-Over / Modal (Theme-immune dark slate cards) --}}
        @if($selectedOrder)
            <div
                class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-xs flex items-center justify-center p-4"
                wire:click.self="closeOrderModal"
            >
                <div class="relative w-full max-w-3xl rounded-2xl shadow-2xl border border-gray-200 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in-95 duration-200 bg-white dark:bg-gray-900">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.04]">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-gray-950 dark:text-white">
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
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                        >
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5" />
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="p-6 space-y-5 text-sm max-h-[75vh] overflow-y-auto">
                        {{-- 4 Metrics Cards (Pristine dark slate, immune to white-box bug) --}}
                        <div class="grid grid-cols-2 gap-3.5 sm:grid-cols-4">
                            <div class="p-3.5 rounded-xl border gantt-tile">
                                <div class="text-xs font-semibold uppercase tracking-wider gantt-text-muted">Target Qty</div>
                                <div class="font-extrabold mt-1 text-base gantt-text-white">
                                    {{ (float) $selectedOrder->quantity }} {{ $selectedOrder->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3.5 rounded-xl border gantt-tile">
                                <div class="text-xs font-semibold uppercase tracking-wider gantt-text-muted">Produced Qty</div>
                                <div class="font-extrabold mt-1 text-base gantt-text-white">
                                    {{ (float) $selectedOrder->quantity_producing }} {{ $selectedOrder->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3.5 rounded-xl border gantt-tile">
                                <div class="text-xs font-semibold uppercase tracking-wider gantt-text-muted">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg {{ $selectedOrder->state instanceof \Webkul\Manufacturing\Enums\ManufacturingOrderState ? $selectedOrder->state->getColor() : 'gray' }}">
                                        {{ $selectedOrder->state instanceof \Webkul\Manufacturing\Enums\ManufacturingOrderState ? $selectedOrder->state->getLabel() : ucfirst($selectedOrder->state) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-xl border gantt-tile">
                                <div class="text-xs font-semibold uppercase tracking-wider gantt-text-muted">Responsible</div>
                                <div class="font-bold mt-1 truncate gantt-text-white">
                                    {{ $selectedOrder->assignedUser?->name ?? 'Unassigned' }}
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Details --}}
                        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl border gantt-tile">
                            <div>
                                <div class="text-xs font-semibold gantt-text-muted uppercase tracking-wider">Scheduled Start</div>
                                <div class="font-bold mt-1 gantt-text-white">
                                    {{ $selectedOrder->started_at ? $selectedOrder->started_at->format('d M Y, H:i') : '—' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold gantt-text-muted uppercase tracking-wider">Deadline</div>
                                <div class="font-bold mt-1 {{ $selectedOrder->deadline_at && $selectedOrder->deadline_at->lt(now()) && !in_array($selectedOrder->state?->value, ['done', 'cancel']) ? 'text-rose-500 flex items-center gap-1.5' : 'gantt-text-white' }}">
                                    {{ $selectedOrder->deadline_at ? $selectedOrder->deadline_at->format('d M Y, H:i') : 'No deadline' }}
                                    @if($selectedOrder->deadline_at && $selectedOrder->deadline_at->lt(now()) && !in_array($selectedOrder->state?->value, ['done', 'cancel']))
                                        <span class="text-xs px-1.5 py-0.5 bg-rose-500/20 text-rose-400 rounded font-black">OVERDUE</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Operations Table --}}
                        <div>
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-gray-900 dark:text-white mb-2">
                                Work Orders & Operations ({{ $selectedOrder->workOrders->count() }})
                            </h4>

                            @if($selectedOrder->workOrders->isNotEmpty())
                                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left font-bold uppercase tracking-wider">Operation</th>
                                                <th class="px-4 py-2.5 text-left font-bold uppercase tracking-wider">Work Center</th>
                                                <th class="px-4 py-2.5 text-right font-bold uppercase tracking-wider">Expected</th>
                                                <th class="px-4 py-2.5 text-right font-bold uppercase tracking-wider">Actual</th>
                                                <th class="px-4 py-2.5 text-center font-bold uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                            @foreach($selectedOrder->workOrders as $wo)
                                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/2">
                                                    <td class="px-4 py-3 font-semibold text-gray-950 dark:text-white">{{ $wo->name }}</td>
                                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $wo->workCenter?->name ?? '—' }}</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 font-mono">{{ (float) $wo->expected_duration }}m</td>
                                                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400 font-mono">{{ (float) $wo->duration }}m</td>
                                                    <td class="px-4 py-3 text-center">
                                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded {{ $wo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $wo->state->getColor() : 'gray' }}">
                                                            {{ $wo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $wo->state->getLabel() : ucfirst($wo->state) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-xs text-gray-400 dark:text-gray-500 italic p-4 bg-gray-50 dark:bg-white/5 rounded-xl text-center">
                                    No work orders generated for this manufacturing order.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-white/10 bg-gray-50/80 dark:bg-white/[0.04]">
                        <div></div>

                        <div class="flex items-center gap-2">
                            <a
                                href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource::getUrl('view', ['record' => $selectedOrder->id]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-primary-600 rounded-xl hover:bg-primary-500 transition shadow-sm"
                            >
                                <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                                Open Manufacturing Order
                            </a>

                            <button
                                type="button"
                                wire:click="closeOrderModal"
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
