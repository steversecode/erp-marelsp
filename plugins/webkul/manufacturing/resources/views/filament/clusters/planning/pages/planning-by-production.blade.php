<x-filament-panels::page>
    @php
        $data = $this->timelineData;
        $stats = $data['stats'];
        $columns = $data['columns'];
        $rows = $data['rows'];
        $selectedOrder = $this->selectedOrder;
    @endphp

    <div class="space-y-6" x-data="{ tooltip: null }">
        {{-- KPI Stat Cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Total Orders</div>
                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_orders'] }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">In current period</div>
            </div>

            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-blue-600 uppercase tracking-wider dark:text-blue-400">Confirmed</div>
                <div class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['confirmed'] }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ready to produce</div>
            </div>

            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-amber-600 uppercase tracking-wider dark:text-amber-400">In Progress</div>
                <div class="mt-2 text-2xl font-bold text-amber-600 dark:text-amber-400 flex items-center gap-2">
                    {{ $stats['in_progress'] }}
                    @if($stats['in_progress'] > 0)
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-amber-500 animate-ping"></span>
                    @endif
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Active production</div>
            </div>

            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-emerald-600 uppercase tracking-wider dark:text-emerald-400">Completed</div>
                <div class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['done'] }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Finished orders</div>
            </div>

            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-rose-600 uppercase tracking-wider dark:text-rose-400">Overdue</div>
                <div class="mt-2 text-2xl font-bold text-rose-600 dark:text-rose-400 flex items-center gap-1.5">
                    {{ $stats['overdue'] }}
                    @if($stats['overdue'] > 0)
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-5 h-5 text-rose-500" />
                    @endif
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Past deadline</div>
            </div>

            <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-xs dark:bg-gray-900 dark:border-white/10">
                <div class="text-xs font-medium text-indigo-600 uppercase tracking-wider dark:text-indigo-400">Total Output</div>
                <div class="mt-2 text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['total_qty'] }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Planned units</div>
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
                        {{-- MO Column Header --}}
                        <div class="w-72 shrink-0 px-4 py-3 text-xs font-semibold tracking-wider text-gray-600 uppercase border-r border-gray-200 dark:text-gray-300 dark:border-white/10">
                            Manufacturing Order
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
                            $order = $row['order'];
                            $theme = $row['color_theme'];
                        @endphp
                        <div class="flex border-b border-gray-200 last:border-b-0 hover:bg-gray-50/30 dark:border-white/5 dark:hover:bg-white/2 transition">
                            {{-- MO Info Column --}}
                            <div class="w-72 shrink-0 p-3 border-r border-gray-200 dark:border-white/10 flex flex-col justify-center">
                                <div class="flex items-center justify-between">
                                    <div class="font-bold text-sm text-gray-900 dark:text-white truncate">
                                        {{ $order->name }}
                                    </div>
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded {{ $theme['badge'] }}">
                                        {{ $row['state_label'] }}
                                    </span>
                                </div>

                                <div class="text-xs text-gray-600 dark:text-gray-300 truncate mt-1">
                                    {{ $order->product?->name }}
                                </div>

                                <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    <span>{{ (float) $order->quantity }} {{ $order->product?->uom?->name }}</span>
                                    <span>•</span>
                                    <span>{{ $row['done_wo_count'] }}/{{ $row['work_orders_count'] }} Ops</span>
                                    <span>•</span>
                                    <span class="font-medium text-gray-600 dark:text-gray-300">{{ $row['progress_percent'] }}%</span>
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

                                {{-- MO Gantt Bar --}}
                                <div class="relative h-full flex flex-col justify-center px-1">
                                    <div
                                        wire:click="openOrderModal({{ $order->id }})"
                                        class="cursor-pointer group relative rounded-xl p-2 text-xs shadow-xs border transition-all duration-150 hover:scale-[1.005] hover:shadow-md select-none {{ $theme['bg'] }} {{ $theme['border'] }} {{ $theme['text'] }}"
                                        style="margin-left: {{ $row['left_percent'] }}%; width: {{ $row['width_percent'] }}%;"
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
                                        <div class="flex items-center justify-between gap-2 overflow-hidden">
                                            <span class="font-bold truncate">
                                                {{ $order->name }}: {{ $order->product?->name }}
                                            </span>
                                            <span class="shrink-0 text-[10px] bg-black/20 rounded px-1.5 py-0.5">
                                                {{ $row['progress_percent'] }}%
                                            </span>
                                        </div>

                                        {{-- Mini Work Order Segments --}}
                                        @if($order->workOrders->isNotEmpty())
                                            <div class="flex items-center gap-1 mt-1.5 overflow-hidden">
                                                @foreach($order->workOrders as $wo)
                                                    @php
                                                        $woState = $wo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $wo->state->value : (string) $wo->state;
                                                        $woColor = match($woState) {
                                                            'done' => 'bg-emerald-300 dark:bg-emerald-400',
                                                            'progress' => 'bg-amber-300 dark:bg-amber-400 animate-pulse',
                                                            'ready' => 'bg-blue-300 dark:bg-blue-400',
                                                            default => 'bg-white/40',
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
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            No manufacturing orders scheduled in this period.
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
            <div class="font-bold text-sm text-primary-400 flex items-center justify-between">
                <span x-text="tooltip ? tooltip.name : ''"></span>
                <template x-if="tooltip && tooltip.is_overdue">
                    <span class="text-rose-400 font-semibold flex items-center gap-1 text-[11px]">
                        <x-filament::icon icon="heroicon-o-exclamation-triangle" class="w-3.5 h-3.5" />
                        OVERDUE
                    </span>
                </template>
            </div>
            <div class="text-gray-300"><span class="text-gray-400">Product:</span> <span class="font-medium" x-text="tooltip ? tooltip.product : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Quantity:</span> <span class="font-medium" x-text="tooltip ? tooltip.quantity : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Schedule:</span> <span class="font-medium" x-text="tooltip ? tooltip.start + ' -> ' + tooltip.deadline : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Operations:</span> <span class="font-medium" x-text="tooltip ? tooltip.wo_count : ''"></span></div>
            <div class="text-gray-300"><span class="text-gray-400">Progress:</span> <span class="font-medium" x-text="tooltip ? tooltip.progress : ''"></span></div>
            <div class="flex items-center gap-2 pt-1">
                <span class="text-gray-400">Status:</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-semibold" :class="tooltip ? tooltip.badge : ''" x-text="tooltip ? tooltip.state : ''"></span>
            </div>
        </div>

        {{-- Detail Slide-Over / Modal --}}
        @if($selectedOrder)
            <div
                class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4"
                wire:click.self="closeOrderModal"
            >
                <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-gray-200 dark:bg-gray-900 dark:border-white/10 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                    {{-- Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-950 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
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
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                            <div class="p-3 bg-gray-50 rounded-xl dark:bg-white/5">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Quantity</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ (float) $selectedOrder->quantity }} {{ $selectedOrder->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3 bg-gray-50 rounded-xl dark:bg-white/5">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Quantity Producing</div>
                                <div class="font-bold text-gray-900 dark:text-white mt-1">
                                    {{ (float) $selectedOrder->quantity_producing }} {{ $selectedOrder->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="p-3 bg-gray-50 rounded-xl dark:bg-white/5">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Status</div>
                                <div class="mt-1">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-md {{ $selectedOrder->state instanceof \Webkul\Manufacturing\Enums\ManufacturingOrderState ? $selectedOrder->state->getColor() : 'gray' }}">
                                        {{ $selectedOrder->state instanceof \Webkul\Manufacturing\Enums\ManufacturingOrderState ? $selectedOrder->state->getLabel() : ucfirst($selectedOrder->state) }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-3 bg-gray-50 rounded-xl dark:bg-white/5">
                                <div class="text-xs text-gray-500 dark:text-gray-400">Responsible</div>
                                <div class="font-medium text-gray-900 dark:text-white mt-1 truncate">
                                    {{ $selectedOrder->assignedUser?->name ?? 'Unassigned' }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-1">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Scheduled Start</div>
                                <div class="font-medium text-gray-900 dark:text-white mt-0.5">
                                    {{ $selectedOrder->started_at ? $selectedOrder->started_at->format('d M Y, H:i') : '—' }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Deadline</div>
                                <div class="font-medium text-gray-900 dark:text-white mt-0.5 {{ $selectedOrder->deadline_at && $selectedOrder->deadline_at->lt(now()) && !in_array($selectedOrder->state?->value, ['done', 'cancel']) ? 'text-rose-600 font-bold' : '' }}">
                                    {{ $selectedOrder->deadline_at ? $selectedOrder->deadline_at->format('d M Y, H:i') : '—' }}
                                    @if($selectedOrder->deadline_at && $selectedOrder->deadline_at->lt(now()) && !in_array($selectedOrder->state?->value, ['done', 'cancel']))
                                        (Overdue)
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Operations Table --}}
                        <div class="pt-2">
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider dark:text-white mb-2">
                                Work Orders & Operations ({{ $selectedOrder->workOrders->count() }})
                            </h4>

                            @if($selectedOrder->workOrders->isNotEmpty())
                                <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
                                    <table class="w-full text-xs">
                                        <thead class="bg-gray-50/80 dark:bg-white/5 border-b border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-semibold">Operation</th>
                                                <th class="px-3 py-2 text-left font-semibold">Work Center</th>
                                                <th class="px-3 py-2 text-right font-semibold">Expected</th>
                                                <th class="px-3 py-2 text-right font-semibold">Actual</th>
                                                <th class="px-3 py-2 text-center font-semibold">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                                            @foreach($selectedOrder->workOrders as $wo)
                                                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/2">
                                                    <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $wo->name }}</td>
                                                    <td class="px-3 py-2 text-gray-600 dark:text-gray-300">{{ $wo->workCenter?->name ?? '—' }}</td>
                                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">{{ (float) $wo->expected_duration }}m</td>
                                                    <td class="px-3 py-2 text-right text-gray-600 dark:text-gray-400">{{ (float) $wo->duration }}m</td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold rounded {{ $wo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $wo->state->getColor() : 'gray' }}">
                                                            {{ $wo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $wo->state->getLabel() : ucfirst($wo->state) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-xs text-gray-400 dark:text-gray-500 italic p-3 bg-gray-50 rounded-lg dark:bg-white/5">
                                    No work orders generated for this manufacturing order.
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5">
                        <div></div>

                        <div class="flex items-center gap-2">
                            <a
                                href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource::getUrl('view', ['record' => $selectedOrder->id]) }}"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition"
                            >
                                <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                                Open Manufacturing Order
                            </a>

                            <button
                                type="button"
                                wire:click="closeOrderModal"
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
