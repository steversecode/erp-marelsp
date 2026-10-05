<x-filament-panels::page>
    @php
        $data = $this->timelineData;
        $stats = $data['stats'];
        $columns = $data['columns'];
        $rows = $data['rows'];
        $selectedWo = $this->selectedWorkOrder;
    @endphp

    <div class="space-y-6" x-data="{ tooltip: null }">
        {{-- KPI Stat Cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Total Work Orders</div>
                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Across {{ $stats['work_centers'] }} work centers</div>
            </div>

            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-amber-600 uppercase tracking-wider dark:text-amber-400">In Progress</div>
                <div class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400 flex items-center gap-2">
                    {{ $stats['in_progress'] }}
                    @if($stats['in_progress'] > 0)
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    @endif
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Currently running</div>
            </div>

            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-blue-600 uppercase tracking-wider dark:text-blue-400">Ready to Start</div>
                <div class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['ready'] }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Awaiting execution</div>
            </div>

            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-emerald-600 uppercase tracking-wider dark:text-emerald-400">Completed</div>
                <div class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['done'] }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Finished in period</div>
            </div>

            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-indigo-600 uppercase tracking-wider dark:text-indigo-400">Planned Hours</div>
                <div class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['planned_hours'] }}h</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total workload</div>
            </div>
        </div>

        {{-- Toolbar Controls --}}
        <div class="flex flex-col gap-4 p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10 lg:flex-row lg:items-center lg:justify-between">
            {{-- Date Navigation & Period --}}
            <div class="flex items-center gap-2">
                <div class="inline-flex rounded-lg shadow-2xs border border-gray-300 dark:border-white/10 overflow-hidden">
                    <button
                        type="button"
                        wire:click="previous"
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 transition"
                        title="Previous"
                    >
                        <x-filament::icon icon="heroicon-o-chevron-left" class="w-4 h-4" />
                    </button>
                    <button
                        type="button"
                        wire:click="today"
                        class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-gray-700 bg-white border-x border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-white/10 dark:hover:bg-gray-700 transition"
                    >
                        Today
                    </button>
                    <button
                        type="button"
                        wire:click="next"
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 transition"
                        title="Next"
                    >
                        <x-filament::icon icon="heroicon-o-chevron-right" class="w-4 h-4" />
                    </button>
                </div>

                <div class="text-base font-bold text-gray-900 dark:text-white px-2">
                    {{ $data['period_title'] }}
                </div>
            </div>

            {{-- Filter and Scale Controls --}}
            <div class="flex flex-wrap items-center gap-3">
                {{-- Scale switcher --}}
                <div class="inline-flex p-1 bg-gray-100 rounded-lg dark:bg-gray-800">
                    <button
                        type="button"
                        wire:click="setViewMode('day')"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ $viewMode === 'day' ? 'bg-white shadow-2xs text-primary-600 dark:bg-gray-700 dark:text-white font-semibold' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }}"
                    >
                        Day
                    </button>
                    <button
                        type="button"
                        wire:click="setViewMode('week')"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ $viewMode === 'week' ? 'bg-white shadow-2xs text-primary-600 dark:bg-gray-700 dark:text-white font-semibold' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }}"
                    >
                        Week
                    </button>
                    <button
                        type="button"
                        wire:click="setViewMode('month')"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition {{ $viewMode === 'month' ? 'bg-white shadow-2xs text-primary-600 dark:bg-gray-700 dark:text-white font-semibold' : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white' }}"
                    >
                        Month
                    </button>
                </div>

                {{-- Status Filter --}}
                <select
                    wire:model.live="statusFilter"
                    class="py-1.5 pl-3 pr-8 text-xs font-medium bg-white border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-white/10 dark:text-white focus:ring-2 focus:ring-primary-500"
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
                        placeholder="Search MO, WO, product..."
                        class="py-1.5 pl-8 pr-3 text-xs bg-white border border-gray-300 rounded-lg dark:bg-gray-800 dark:border-white/10 dark:text-white focus:ring-2 focus:ring-primary-500 w-48 sm:w-56"
                    />
                    <x-filament::icon icon="heroicon-o-magnifying-glass" class="absolute w-4 h-4 text-gray-400 left-2.5 top-2" />
                </div>
            </div>
        </div>

        {{-- Gantt Chart Container --}}
        <div class="overflow-hidden bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
            <div class="overflow-x-auto">
                <div class="min-w-[900px]">
                    {{-- Header Row --}}
                    <div class="flex border-b border-gray-200 bg-gray-50/80 dark:bg-gray-800/60 dark:border-white/10">
                        {{-- Work Center Column Header --}}
                        <div class="w-64 shrink-0 px-4 py-3 text-xs font-semibold tracking-wider text-gray-600 uppercase border-r border-gray-200 dark:text-gray-300 dark:border-white/10">
                            Work Center
                        </div>

                        {{-- Timeline Columns Header --}}
                        <div class="flex-1 flex">
                            @foreach($columns as $col)
                                <div class="flex-1 px-1 py-2 text-center border-r border-gray-200 last:border-r-0 dark:border-white/5 {{ $col['is_today'] ? 'bg-primary-50/70 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 font-bold' : 'text-gray-600 dark:text-gray-400' }}">
                                    <div class="text-xs font-semibold">{{ $col['label'] }}</div>
                                    @if(!empty($col['sublabel']))
                                        <div class="text-[10px] text-gray-400 dark:text-gray-500">{{ $col['sublabel'] }}</div>
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
                        <div class="flex border-b border-gray-200 last:border-b-0 hover:bg-gray-50/30 dark:border-white/5 dark:hover:bg-white/2 transition">
                            {{-- Work Center Info Card --}}
                            <div class="w-64 shrink-0 p-3 border-r border-gray-200 dark:border-white/10 flex flex-col justify-center">
                                <div class="flex items-center justify-between">
                                    <div class="font-semibold text-sm text-gray-900 dark:text-white truncate">
                                        {{ $wc->name }}
                                    </div>
                                    @if($wc->code)
                                        <span class="px-1.5 py-0.5 text-[10px] font-mono bg-gray-100 text-gray-600 rounded dark:bg-gray-800 dark:text-gray-300">
                                            {{ $wc->code }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center gap-1">
                                        <span class="w-2 h-2 rounded-full {{ $wc->working_state === \Webkul\Manufacturing\Enums\WorkCenterWorkingState::BLOCKED ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                        {{ $wc->working_state instanceof \Webkul\Manufacturing\Enums\WorkCenterWorkingState ? $wc->working_state->getLabel() : 'Normal' }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $row['planned_hours'] }}h planned</span>
                                    <span>•</span>
                                    <span>{{ $row['active_orders'] }} WO</span>
                                </div>
                            </div>

                            {{-- Timeline Canvas Row --}}
                            <div class="flex-1 relative min-h-[72px] py-2">
                                {{-- Background Grid Lines --}}
                                <div class="absolute inset-0 flex pointer-events-none">
                                    @foreach($columns as $col)
                                        <div class="flex-1 border-r border-gray-100 last:border-r-0 dark:border-white/5 {{ $col['is_today'] ? 'bg-primary-50/30 dark:bg-primary-950/20' : '' }}"></div>
                                    @endforeach
                                </div>

                                {{-- Work Order Gantt Bars --}}
                                <div class="relative h-full flex flex-col justify-center gap-1.5 px-1">
                                    @forelse($items as $item)
                                        <div
                                            wire:click="openWorkOrderModal({{ $item['id'] }})"
                                            class="cursor-pointer group relative rounded-lg px-2.5 py-1 text-xs font-medium shadow-xs border transition-all duration-150 hover:scale-[1.01] hover:shadow-md select-none {{ $item['color_theme']['bg'] }} {{ $item['color_theme']['border'] }} {{ $item['color_theme']['text'] }}"
                                            style="margin-left: {{ $item['left_percent'] }}%; width: {{ $item['width_percent'] }}%;"
                                            x-on:mouseenter="tooltip = {{ json_encode($item) }}"
                                            x-on:mouseleave="tooltip = null"
                                        >
                                            <div class="flex items-center justify-between gap-1 overflow-hidden">
                                                <span class="font-bold truncate">
                                                    {{ $item['mo_name'] }}: {{ $item['name'] }}
                                                </span>
                                                <span class="shrink-0 text-[10px] opacity-90">
                                                    {{ $item['duration_hours'] }}h
                                                </span>
                                            </div>
                                            <div class="text-[10px] opacity-80 truncate">
                                                {{ $item['product_name'] }}
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-xs text-gray-300 dark:text-gray-600 italic py-3 px-2">
                                            No orders scheduled in this period
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            No work centers found for current company.
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
            <div class="flex items-center gap-2 pt-1">
                <span class="text-gray-400">Status:</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold" :class="tooltip ? tooltip.color_theme.badge : ''" x-text="tooltip ? tooltip.state_label : ''"></span>
            </div>
        </div>

        {{-- Detail Slide-Over / Modal --}}
        @if($selectedWo)
            <div
                class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4"
                wire:click.self="closeWorkOrderModal"
            >
                <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-gray-200 dark:bg-gray-900 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
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
                    <div class="p-6 space-y-4 text-sm">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3 bg-gray-50 rounded-xl dark:bg-white/5">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Product</div>
                                <div class="font-semibold text-gray-900 dark:text-white mt-1">
                                    {{ $selectedWo->manufacturingOrder?->product?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="p-3 bg-gray-50 rounded-xl dark:bg-white/5">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Quantity to Produce</div>
                                <div class="font-semibold text-gray-900 dark:text-white mt-1">
                                    {{ (float) $selectedWo->manufacturingOrder?->quantity }} {{ $selectedWo->manufacturingOrder?->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3 bg-gray-50 rounded-xl dark:bg-white/5">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Work Center</div>
                                <div class="font-semibold text-gray-900 dark:text-white mt-1">
                                    {{ $selectedWo->workCenter?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="p-3 bg-gray-50 rounded-xl dark:bg-white/5">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-md {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getColor() : 'gray' }}">
                                        {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getLabel() : ucfirst($selectedWo->state) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Scheduled Start</div>
                                <div class="font-medium text-gray-900 dark:text-white mt-0.5">
                                    {{ $selectedWo->started_at ? $selectedWo->started_at->format('d M Y, H:i') : ($selectedWo->manufacturingOrder?->started_at ? $selectedWo->manufacturingOrder->started_at->format('d M Y, H:i') : 'Not scheduled') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Finished / Deadline</div>
                                <div class="font-medium text-gray-900 dark:text-white mt-0.5">
                                    {{ $selectedWo->finished_at ? $selectedWo->finished_at->format('d M Y, H:i') : ($selectedWo->manufacturingOrder?->deadline_at ? $selectedWo->manufacturingOrder->deadline_at->format('d M Y, H:i') : '—') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Expected Duration</div>
                                <div class="font-medium text-gray-900 dark:text-white mt-0.5">
                                    {{ (float) $selectedWo->expected_duration }} minutes ({{ round((float) $selectedWo->expected_duration / 60, 1) }}h)
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Actual Duration Recorded</div>
                                <div class="font-medium text-gray-900 dark:text-white mt-0.5">
                                    {{ (float) $selectedWo->duration }} minutes
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
                        <div class="flex items-center gap-2">
                            @if(in_array($selectedWo->state?->value ?? (string)$selectedWo->state, ['ready', 'waiting', 'pending']))
                                <button
                                    type="button"
                                    wire:click="startWorkOrder({{ $selectedWo->id }})"
                                    class="px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition"
                                >
                                    Start Work
                                </button>
                            @elseif(($selectedWo->state?->value ?? (string)$selectedWo->state) === 'progress')
                                <button
                                    type="button"
                                    wire:click="finishWorkOrder({{ $selectedWo->id }})"
                                    class="px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition"
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
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-white/10 dark:hover:bg-gray-700 transition"
                                >
                                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                                    View MO
                                </a>
                            @endif

                            <a
                                href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource::getUrl('view', ['record' => $selectedWo->id]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-white/10 dark:hover:bg-gray-700 transition"
                            >
                                <x-filament::icon icon="heroicon-o-eye" class="w-3.5 h-3.5" />
                                View WO
                            </a>

                            <button
                                type="button"
                                wire:click="closeWorkOrderModal"
                                class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white"
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
