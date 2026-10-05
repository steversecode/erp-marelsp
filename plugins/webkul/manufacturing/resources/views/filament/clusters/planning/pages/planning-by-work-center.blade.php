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

    <div class="space-y-6" x-data="{ tooltip: null }">
        {{-- Navigation Tabs between Work Center and Production --}}
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-800 pb-3">
            <a
                href="{{ \Webkul\Manufacturing\Filament\Clusters\Planning\Pages\PlanningByWorkCenter::getUrl() }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400 border border-primary-200 dark:border-primary-500/20"
            >
                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-4 h-4" />
                Planning by Work Center
            </a>
            <a
                href="{{ \Webkul\Manufacturing\Filament\Clusters\Planning\Pages\PlanningByProduction::getUrl() }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800 transition"
            >
                <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-4 h-4" />
                Planning by Production
            </a>
        </div>

        {{-- Executive KPI Metrics Cards --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            {{-- Total Work Orders --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Work Orders</span>
                    <span class="p-1.5 rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</div>
                <div class="mt-0.5 text-[11px] text-gray-400">{{ $stats['work_centers'] }} work centers</div>
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
                <div class="mt-0.5 text-[11px] text-gray-400">Active operations</div>
            </div>

            {{-- Ready --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400">Ready</span>
                    <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-blue-600 dark:text-blue-400">{{ $stats['ready'] }}</div>
                <div class="mt-0.5 text-[11px] text-gray-400">Waiting to start</div>
            </div>

            {{-- Done --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Done</span>
                    <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                        <x-filament::icon icon="heroicon-o-check-badge" class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $stats['done'] }}</div>
                <div class="mt-0.5 text-[11px] text-gray-400">Finished in period</div>
            </div>

            {{-- Planned Hours --}}
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl shadow-2xs dark:bg-gray-900 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Planned Hours</span>
                    <span class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <x-filament::icon icon="heroicon-o-clock" class="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $stats['planned_hours'] }}h</div>
                <div class="mt-0.5 text-[11px] text-gray-400">Total duration</div>
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
                        wire:click="setViewMode('day')"
                        class="px-3 py-1.5 text-xs rounded-md transition {{ $viewMode === 'day' ? 'bg-primary-600 text-white font-bold shadow-xs' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}"
                    >
                        Day
                    </button>
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
                        {{-- Work Center Column Header --}}
                        <div class="w-80 shrink-0 px-4 py-3 text-xs font-bold tracking-wider text-gray-600 uppercase border-r border-gray-200 dark:text-gray-300 dark:border-gray-800">
                            Work Center
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
                            $wc = $row['work_center'];
                            $items = $row['items'];
                        @endphp
                        <div class="flex border-b border-gray-100 last:border-b-0 hover:bg-gray-50/50 dark:border-gray-800/60 dark:hover:bg-white/2 transition">
                            {{-- Work Center Info Card --}}
                            <div class="w-80 shrink-0 p-3.5 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex flex-col justify-center">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="font-bold text-sm text-gray-900 dark:text-white truncate">
                                        {{ $wc->name }}
                                    </div>
                                    @if($wc->code)
                                        <span class="px-1.5 py-0.5 text-[10px] font-mono font-bold bg-gray-100 text-gray-700 rounded dark:bg-gray-800 dark:text-gray-300 border border-gray-200 dark:border-gray-700">
                                            {{ $wc->code }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-500 dark:text-gray-400 pt-1 border-t border-gray-100 dark:border-gray-800">
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

                            {{-- Timeline Canvas Row --}}
                            <div class="flex-1 relative h-16 bg-white dark:bg-gray-900/40">
                                {{-- Background Grid Lines --}}
                                <div class="absolute inset-0 flex pointer-events-none">
                                    @foreach($columns as $col)
                                        <div class="flex-1 border-r border-gray-100 last:border-r-0 dark:border-gray-800/40 {{ $col['is_today'] ? 'bg-primary-500/5' : '' }}"></div>
                                    @endforeach
                                </div>

                                {{-- Work Order Gantt Bars --}}
                                @foreach($items as $item)
                                    <div
                                        wire:click="openWorkOrderModal({{ $item['id'] }})"
                                        class="absolute top-2 bottom-2 rounded-xl px-2.5 py-1 text-xs shadow-md border cursor-pointer select-none transition-all duration-150 hover:scale-[1.01] hover:shadow-xl hover:z-20 flex flex-col justify-between overflow-hidden {{ $item['color_theme']['bg'] }} {{ $item['color_theme']['border'] }} {{ $item['color_theme']['text'] }}"
                                        style="left: {{ $item['left_percent'] }}%; width: {{ $item['width_percent'] }}%;"
                                        x-on:mouseenter="tooltip = {{ json_encode($item) }}"
                                        x-on:mouseleave="tooltip = null"
                                    >
                                        <div class="flex items-center justify-between gap-1 overflow-hidden">
                                            <span class="font-extrabold text-xs truncate">
                                                {{ $item['mo_name'] }}: {{ $item['name'] }}
                                            </span>
                                            <span class="shrink-0 text-[10px] font-bold bg-black/30 rounded px-1.5 py-0.5">
                                                {{ $item['duration_hours'] }}h
                                            </span>
                                        </div>
                                        <div class="text-[10px] opacity-90 truncate font-medium">
                                            {{ $item['product_name'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                            <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-10 h-10 mx-auto text-gray-400 mb-2 opacity-50" />
                            <div class="font-semibold">No work centers found for current company</div>
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

        {{-- Detail Slide-Over / Modal (Fixing dark mode contrast and layout) --}}
        @if($selectedWo)
            <div
                class="fixed inset-0 z-50 overflow-y-auto bg-black/75 backdrop-blur-xs flex items-center justify-center p-4"
                wire:click.self="closeWorkOrderModal"
            >
                <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-gray-200 dark:bg-gray-900 dark:border-gray-700 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-800/80">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-xl bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-gray-900 dark:text-white">
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
                        {{-- 4 Metrics Cards (Fixing the stark white box bug!) --}}
                        <div class="grid grid-cols-2 gap-3.5">
                            <div class="p-3.5 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Product</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ $selectedWo->manufacturingOrder?->product?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="p-3.5 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Quantity to Produce</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ (float) $selectedWo->manufacturingOrder?->quantity }} {{ $selectedWo->manufacturingOrder?->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3.5 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Work Center</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ $selectedWo->workCenter?->name ?? '—' }}
                                </div>
                            </div>

                            <div class="p-3.5 bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 rounded-xl">
                                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-lg {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getColor() : 'gray' }}">
                                        {{ $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->getLabel() : ucfirst($selectedWo->state) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Details --}}
                        <div class="grid grid-cols-2 gap-4 p-4 rounded-xl bg-gray-50/60 dark:bg-gray-800/40 border border-gray-200 dark:border-gray-700/60">
                            <div>
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Scheduled Start</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ $selectedWo->started_at ? $selectedWo->started_at->format('d M Y, H:i') : ($selectedWo->manufacturingOrder?->started_at ? $selectedWo->manufacturingOrder->started_at->format('d M Y, H:i') : 'Not scheduled') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Finished / Deadline</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ $selectedWo->finished_at ? $selectedWo->finished_at->format('d M Y, H:i') : ($selectedWo->manufacturingOrder?->deadline_at ? $selectedWo->manufacturingOrder->deadline_at->format('d M Y, H:i') : '—') }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expected Duration</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ (float) $selectedWo->expected_duration }} minutes ({{ round((float) $selectedWo->expected_duration / 60, 1) }}h)
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actual Duration</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ (float) $selectedWo->duration }} minutes
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50/80 dark:bg-gray-800/80">
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
                                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700 transition"
                                >
                                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                                    View MO
                                </a>
                            @endif

                            <a
                                href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\WorkOrderResource::getUrl('view', ['record' => $selectedWo->id]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700 transition"
                            >
                                <x-filament::icon icon="heroicon-o-eye" class="w-3.5 h-3.5" />
                                View WO
                            </a>

                            <button
                                type="button"
                                wire:click="closeWorkOrderModal"
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
