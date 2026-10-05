<x-filament-panels::page>
    @php
        $data = $this->timelineData;
        $stats = $data['stats'];
        $columns = $data['columns'];
        $rows = $data['rows'];
        $selectedOrder = $this->selectedOrder;
        $minWidth = $viewMode === 'month' ? 'min-w-[1300px]' : 'min-w-[900px]';
    @endphp

    <div class="space-y-6" x-data="{ tooltip: null }">
        {{-- Navigation Tabs between Work Center and Production --}}
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800 pb-3">
            <a
                href="{{ \Webkul\Manufacturing\Filament\Clusters\Planning\Pages\PlanningByWorkCenter::getUrl() }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 transition"
            >
                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-4 h-4" />
                Planning by Work Center
            </a>
            <a
                href="{{ \Webkul\Manufacturing\Filament\Clusters\Planning\Pages\PlanningByProduction::getUrl() }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400 border border-primary-200 dark:border-primary-500/20"
            >
                <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-4 h-4" />
                Planning by Production
            </a>
        </div>

        {{-- Executive KPI Metrics Cards --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            {{-- Total Orders --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total MO</span>
                    <span class="p-1.5 rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</div>
                <div class="mt-0.5 text-[11px] text-gray-400">In current period</div>
            </div>

            {{-- Confirmed --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Confirmed</span>
                    <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-blue-600 dark:text-blue-400">{{ $stats['confirmed'] }}</div>
                <div class="mt-0.5 text-[11px] text-gray-400">Scheduled / ready</div>
            </div>

            {{-- In Progress --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">In Progress</span>
                    <span class="p-1.5 rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400 relative">
                        <x-filament::icon icon="heroicon-o-play-circle" class="w-4 h-4" />
                        @if($stats['in_progress'] > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        @endif
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-amber-600 dark:text-amber-400">{{ $stats['in_progress'] }}</div>
                <div class="mt-0.5 text-[11px] text-gray-400">Active on shop floor</div>
            </div>

            {{-- Completed --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Done</span>
                    <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <x-filament::icon icon="heroicon-o-check-badge" class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['done'] }}</div>
                <div class="mt-0.5 text-[11px] text-gray-400">Finished orders</div>
            </div>

            {{-- Overdue --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">Overdue</span>
                    <span class="p-1.5 rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-rose-600 dark:text-rose-400">{{ $stats['overdue'] }}</div>
                <div class="mt-0.5 text-[11px] text-gray-400">Past deadline</div>
            </div>

            {{-- Total Output Units --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Total Units</span>
                    <span class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <x-filament::icon icon="heroicon-o-cube" class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $stats['total_qty'] }}</div>
                <div class="mt-0.5 text-[11px] text-gray-400">Target quantity</div>
            </div>
        </div>

        {{-- Toolbar Controls --}}
        <div class="flex flex-col gap-4 p-4 bg-white border border-gray-200 rounded-2xl shadow-2xs dark:bg-gray-900 dark:border-gray-800 lg:flex-row lg:items-center lg:justify-between">
            {{-- Date Navigation & Period Title --}}
            <div class="flex items-center gap-3">
                <div class="inline-flex rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden shadow-2xs">
                    <button
                        type="button"
                        wire:click="previous"
                        class="px-2.5 py-1.5 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 transition"
                        title="Previous"
                    >
                        <x-filament::icon icon="heroicon-o-chevron-left" class="w-4 h-4" />
                    </button>
                    <button
                        type="button"
                        wire:click="today"
                        class="px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-primary-600 dark:text-primary-400 border-x border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                    >
                        Today
                    </button>
                    <button
                        type="button"
                        wire:click="next"
                        class="px-2.5 py-1.5 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 transition"
                        title="Next"
                    >
                        <x-filament::icon icon="heroicon-o-chevron-right" class="w-4 h-4" />
                    </button>
                </div>

                <div class="flex items-center gap-2 text-base font-extrabold text-gray-900 dark:text-white">
                    <x-filament::icon icon="heroicon-o-calendar" class="w-5 h-5 text-gray-400" />
                    {{ $data['period_title'] }}
                </div>
            </div>

            {{-- Scale & Filter Controls --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- View Scale Switcher --}}
                <div class="inline-flex p-1 bg-gray-100 rounded-lg dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <button
                        type="button"
                        wire:click="setViewMode('week')"
                        class="px-3 py-1.5 text-xs rounded-md transition {{ $viewMode === 'week' ? 'bg-primary-600 text-white font-bold shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}"
                    >
                        Week
                    </button>
                    <button
                        type="button"
                        wire:click="setViewMode('month')"
                        class="px-3 py-1.5 text-xs rounded-md transition {{ $viewMode === 'month' ? 'bg-primary-600 text-white font-bold shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}"
                    >
                        Month
                    </button>
                </div>

                {{-- Status Filter --}}
                <select
                    wire:model.live="statusFilter"
                    class="py-1.5 pl-3 pr-8 text-xs font-medium bg-white border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500"
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
                        class="py-1.5 pl-8 pr-3 text-xs bg-white border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-white focus:ring-2 focus:ring-primary-500 w-48 sm:w-56"
                    />
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="absolute w-4 h-4 text-gray-400 left-2.5 top-2" />
                </div>
            </div>
        </div>

        {{-- Gantt Matrix Container --}}
        <div class="overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-xs dark:bg-gray-900 dark:border-gray-800">
            <div class="overflow-x-auto">
                <div class="{{ $minWidth }}">
                    {{-- Header Row --}}
                    <div class="flex border-b border-gray-200 bg-gray-50/90 dark:bg-gray-800/90 dark:border-gray-800">
                        {{-- MO Column Header --}}
                        <div class="w-80 shrink-0 px-4 py-3 text-xs font-bold tracking-wider text-gray-600 uppercase border-r border-gray-200 dark:text-gray-300 dark:border-gray-800">
                            Manufacturing Order
                        </div>

                        {{-- Timeline Columns Header --}}
                        <div class="flex-1 flex">
                            @foreach($columns as $col)
                                <div class="flex-1 px-1 py-2 text-center border-r border-gray-200 last:border-r-0 dark:border-gray-800/60 {{ $col['is_today'] ? 'bg-primary-500/10 text-primary-600 dark:text-primary-400 font-extrabold border-b-2 border-primary-500' : 'text-gray-500 dark:text-gray-400' }}">
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
                        @endphp
                        <div class="flex border-b border-gray-100 last:border-b-0 hover:bg-gray-50/50 dark:border-gray-800/60 dark:hover:bg-white/2 transition">
                            {{-- MO Left Card --}}
                            <div class="w-80 shrink-0 p-3.5 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex flex-col justify-center">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="font-bold text-sm text-gray-900 dark:text-white truncate">
                                        {{ $order->name }}
                                    </div>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $theme['badge'] }}">
                                        {{ $row['state_label'] }}
                                    </span>
                                </div>

                                <div class="text-xs text-gray-600 dark:text-gray-300 truncate mt-1 font-medium">
                                    {{ $order->product?->name }}
                                </div>

                                <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500 mt-1.5 pt-1 border-t border-gray-100 dark:border-gray-800">
                                    <span>{{ (float) $order->quantity }} {{ $order->product?->uom?->name }}</span>
                                    <span>•</span>
                                    <span>{{ $row['done_wo_count'] }}/{{ $row['work_orders_count'] }} Ops</span>
                                    <span>•</span>
                                    <span class="font-bold text-gray-700 dark:text-gray-300">{{ $row['progress_percent'] }}%</span>
                                </div>
                            </div>

                            {{-- Timeline Canvas Row --}}
                            <div class="flex-1 relative h-16 bg-white dark:bg-gray-900/40">
                                {{-- Background Grid Lines --}}
                                <div class="absolute inset-0 flex pointer-events-none">
                                    @foreach($columns as $col)
                                        <div class="flex-1 border-r border-gray-100 last:border-r-0 dark:border-gray-800/40 {{ $col['is_today'] ? 'bg-primary-500/5' : '' }}"></div>
                                    @endforeach
                                </div>

                                {{-- Gantt Bar --}}
                                <div
                                    wire:click="openOrderModal({{ $order->id }})"
                                    class="absolute top-2 bottom-2 rounded-xl px-3 py-1.5 shadow-md border cursor-pointer select-none transition-all duration-150 hover:scale-[1.008] hover:shadow-xl hover:z-20 flex flex-col justify-between overflow-hidden {{ $theme['bg'] }} {{ $theme['border'] }} {{ $theme['text'] }}"
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
                                        wo_count: '{{ $row['work_orders_count'] }} operations ({{ $row['done_wo_count'] }} completed)',
                                        badge: '{{ $theme['badge'] }}'
                                    }"
                                    x-on:mouseleave="tooltip = null"
                                >
                                    <div class="flex items-center justify-between gap-1 overflow-hidden">
                                        <span class="font-extrabold text-xs truncate">
                                            {{ $order->name }}: {{ $order->product?->name }}
                                        </span>
                                        <span class="shrink-0 text-[10px] font-bold bg-black/30 rounded px-1.5 py-0.5">
                                            {{ $row['progress_percent'] }}%
                                        </span>
                                    </div>

                                    {{-- Work Order Progress Bar / Mini segments inside MO bar --}}
                                    @if($order->workOrders->isNotEmpty())
                                        <div class="flex items-center gap-1 overflow-hidden mt-0.5">
                                            @foreach($order->workOrders as $wo)
                                                @php
                                                    $woState = $wo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $wo->state->value : (string) $wo->state;
                                                    $woColor = match($woState) {
                                                        'done'     => 'bg-emerald-300 dark:bg-emerald-400',
                                                        'progress' => 'bg-amber-300 dark:bg-amber-400 animate-pulse',
                                                        'ready'    => 'bg-blue-300 dark:bg-blue-400',
                                                        default    => 'bg-white/40',
                                                    };
                                                @endphp
                                                <div
                                                    class="h-1.5 flex-1 rounded-full {{ $woColor }}"
                                                    title="{{ $wo->name }} ({{ ucfirst($woState) }})"
                                                ></div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-10 h-10 mx-auto text-gray-400 mb-2 opacity-50" />
                            <div class="font-semibold">No manufacturing orders scheduled in this period</div>
                            <div class="text-xs text-gray-400 mt-1">Try switching to another month/week or adjusting filters.</div>
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

        {{-- Detail Slide-Over / Modal (Fixing dark mode contrast and layout) --}}
        @if($selectedOrder)
            <div
                class="fixed inset-0 z-50 overflow-y-auto bg-black/75 backdrop-blur-xs flex items-center justify-center p-4"
                wire:click.self="closeOrderModal"
            >
                <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-gray-200 dark:bg-gray-900 dark:border-gray-700 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-800/80">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-gray-900 dark:text-white">
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
                        {{-- 4 Metrics Cards (Fixing the stark white box bug!) --}}
                        <div class="grid grid-cols-2 gap-3.5 sm:grid-cols-4">
                            <div class="p-3.5 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Target Qty</div>
                                <div class="font-extrabold text-gray-900 dark:text-white mt-1 text-base">
                                    {{ (float) $selectedOrder->quantity }} {{ $selectedOrder->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3.5 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Produced Qty</div>
                                <div class="font-extrabold text-gray-900 dark:text-white mt-1 text-base">
                                    {{ (float) $selectedOrder->quantity_producing }} {{ $selectedOrder->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3.5 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg {{ $selectedOrder->state instanceof \Webkul\Manufacturing\Enums\ManufacturingOrderState ? $selectedOrder->state->getColor() : 'gray' }}">
                                        {{ $selectedOrder->state instanceof \Webkul\Manufacturing\Enums\ManufacturingOrderState ? $selectedOrder->state->getLabel() : ucfirst($selectedOrder->state) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-3.5 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Responsible</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1 truncate">
                                    {{ $selectedOrder->assignedUser?->name ?? 'Unassigned' }}
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Details --}}
                        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-gray-50/60 dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700/60">
                            <div>
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Scheduled Start</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ $selectedOrder->started_at ? $selectedOrder->started_at->format('d M Y, H:i') : '—' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deadline</div>
                                <div class="font-bold mt-1 {{ $selectedOrder->deadline_at && $selectedOrder->deadline_at->lt(now()) && !in_array($selectedOrder->state?->value, ['done', 'cancel']) ? 'text-rose-500 flex items-center gap-1.5' : 'text-gray-900 dark:text-white' }}">
                                    {{ $selectedOrder->deadline_at ? $selectedOrder->deadline_at->format('d M Y, H:i') : 'No deadline' }}
                                    @if($selectedOrder->deadline_at && $selectedOrder->deadline_at->lt(now()) && !in_array($selectedOrder->state?->value, ['done', 'cancel']))
                                        <span class="text-xs px-1.5 py-0.5 bg-rose-500/10 text-rose-500 rounded font-black">OVERDUE</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Operations Table --}}
                        <div>
                            <h4 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider dark:text-white mb-2">
                                Work Orders & Operations ({{ $selectedOrder->workOrders->count() }})
                            </h4>

                            @if($selectedOrder->workOrders->isNotEmpty())
                                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300">
                                            <tr>
                                                <th class="px-4 py-2.5 text-left font-bold uppercase tracking-wider">Operation</th>
                                                <th class="px-4 py-2.5 text-left font-bold uppercase tracking-wider">Work Center</th>
                                                <th class="px-4 py-2.5 text-right font-bold uppercase tracking-wider">Expected</th>
                                                <th class="px-4 py-2.5 text-right font-bold uppercase tracking-wider">Actual</th>
                                                <th class="px-4 py-2.5 text-center font-bold uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                            @foreach($selectedOrder->workOrders as $wo)
                                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/2">
                                                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $wo->name }}</td>
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
                                <div class="text-xs text-gray-400 dark:text-gray-500 italic p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl text-center">
                                    No work orders generated for this manufacturing order.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-800/80">
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
                                class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition"
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
