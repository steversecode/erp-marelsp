<x-filament-panels::page>
    @php
        $data = $this->shopFloorData;
        $workCenters = $data['work_centers'];
        $stats = $data['stats'];
        $cards = $data['cards'];
        $selectedWo = $this->selectedWorkOrder;
        $activeOp = $this->activeOperator;
        $availableOps = $this->availableOperators;
    @endphp

    <style>
        /* Scoped High-End Shop Floor Design System */
        .sf-root {
            font-family: inherit;
        }

        .sf-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition:
                transform 0.15s ease,
                box-shadow 0.15s ease,
                border-color 0.15s ease;
        }
        :is(.dark, [data-theme="dark"]) .sf-card {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        }
        .sf-card:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07);
        }
        :is(.dark, [data-theme="dark"]) .sf-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }

        /* Active State Border Highlights */
        .sf-card-progress {
            border-left: 4px solid #f59e0b !important;
        }
        .sf-card-ready {
            border-left: 4px solid #3b82f6 !important;
        }
        .sf-card-done {
            border-left: 4px solid #10b981 !important;
        }
        .sf-card-blocked {
            border-left: 4px solid #ef4444 !important;
        }

        /* Clean Status Chips (No Black Outlines) */
        .sf-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2.5px 9px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.3;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .sf-badge-neutral {
            background-color: #f1f5f9;
            color: #475569;
        }
        .sf-badge-blue {
            background-color: #eff6ff;
            color: #2563eb;
        }
        .sf-badge-amber {
            background-color: #fef3c7;
            color: #d97706;
        }
        .sf-badge-green {
            background-color: #ecfdf5;
            color: #059669;
        }
        .sf-badge-red {
            background-color: #fef2f2;
            color: #dc2626;
        }
        .sf-badge-indigo {
            background-color: #eef2ff;
            color: #4f46e5;
        }

        :is(.dark, [data-theme="dark"]) .sf-badge-neutral {
            background-color: rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
        }
        :is(.dark, [data-theme="dark"]) .sf-badge-blue {
            background-color: rgba(37, 99, 235, 0.15);
            color: #93c5fd;
        }
        :is(.dark, [data-theme="dark"]) .sf-badge-amber {
            background-color: rgba(217, 119, 6, 0.15);
            color: #fcd34d;
        }
        :is(.dark, [data-theme="dark"]) .sf-badge-green {
            background-color: rgba(5, 150, 105, 0.15);
            color: #6ee7b7;
        }
        :is(.dark, [data-theme="dark"]) .sf-badge-red {
            background-color: rgba(220, 38, 38, 0.15);
            color: #fca5a5;
        }
        :is(.dark, [data-theme="dark"]) .sf-badge-indigo {
            background-color: rgba(79, 70, 229, 0.15);
            color: #a5b4fc;
        }

        /* Work Center Tabs Pill Navigation */
        .sf-tab {
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid transparent;
            user-select: none;
            white-space: nowrap;
        }
        .sf-tab-inactive {
            background-color: #f8fafc;
            color: #64748b;
            border-color: #e2e8f0;
        }
        .sf-tab-inactive:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }
        :is(.dark, [data-theme="dark"]) .sf-tab-inactive {
            background-color: rgba(255, 255, 255, 0.03);
            border-color: #1e293b;
            color: #94a3b8;
        }
        :is(.dark, [data-theme="dark"]) .sf-tab-inactive:hover {
            background-color: rgba(255, 255, 255, 0.07);
            color: #f1f5f9;
        }
        .sf-tab-active {
            background-color: #2563eb;
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }

        /* Large Touch Buttons */
        .sf-btn-touch {
            min-height: 44px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.12s ease;
            cursor: pointer;
            user-select: none;
        }
        .sf-btn-touch:active {
            transform: scale(0.98);
        }

        /* Stepper buttons */
        .sf-stepper-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 15px;
            background-color: #f1f5f9;
            color: #334155;
            transition: all 0.1s ease;
        }
        .sf-stepper-btn:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }
        :is(.dark, [data-theme="dark"]) .sf-stepper-btn {
            background-color: rgba(255, 255, 255, 0.06);
            color: #cbd5e1;
        }
        :is(.dark, [data-theme="dark"]) .sf-stepper-btn:hover {
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        /* Tile */
        .sf-tile {
            background-color: #f8fafc;
            border: 1px solid #edf2f7;
            border-radius: 10px;
            padding: 10px 14px;
        }
        :is(.dark, [data-theme="dark"]) .sf-tile {
            background-color: rgba(255, 255, 255, 0.025);
            border-color: rgba(255, 255, 255, 0.06);
        }

        /* Modal Backdrop */
        .sf-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .sf-modal-dialog {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow:
                0 20px 25px -5px rgba(0, 0, 0, 0.15),
                0 8px 10px -6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 680px;
            overflow: hidden;
            animation: sfModalZoom 0.15s ease-out;
        }
        :is(.dark, [data-theme="dark"]) .sf-modal-dialog {
            background-color: #0f172a;
            border-color: #1e293b;
            box-shadow: 0 25px 30px -5px rgba(0, 0, 0, 0.5);
        }
        @keyframes sfModalZoom {
            from {
                opacity: 0;
                transform: scale(0.97);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <div class="space-y-4 sf-root" x-data="{ isFullscreen: false }">
        {{--Top Industrial Header Bar --}}
        <div class="sf-card p-4 space-y-3.5">
            {{--Top Controls: Title, Station Tabs, Operator, Fullscreen --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                {{--Left: Logo / Title & Clock --}}
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-primary-600 text-white shadow-xs">
                        <x-filament::icon icon="heroicon-o-computer-desktop" class="w-5 h-5"/>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-black tracking-tight text-gray-950 dark:text-white">
                                Shop Floor Terminal
                            </h2>
                            <span class="sf-badge sf-badge-green text-[10px]">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live Production
                            </span>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-data="{ time: new Date().toLocaleTimeString() }" x-init="setInterval(() => time = new Date().toLocaleTimeString(), 1000)">
                            Station Clock: <span class="font-mono font-bold text-gray-800 dark:text-gray-200" x-text="time"></span>
                        </div>
                        
                    </div>
                </div>

                {{--Right: Barcode Scanner, Active Operator, Fullscreen Button --}}
                <div class="flex flex-wrap items-center gap-2.5">
                    {{--Barcode / RFID Input --}}
                    <form wire:submit.prevent="handleBarcodeInput" class="relative">
                        <input
                            type="text"
                            wire:model="barcodeInput"
                            placeholder="Scan WO barcode / MO..."
                            class="py-1.5 pl-8 pr-3 text-xs rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 w-44 sm:w-56 font-mono"
                        />
                        <x-filament::icon icon="heroicon-m-qr-code" class="absolute w-4 h-4 text-primary-500 left-2.5 top-2.5 pointer-events-none"/>
                    </form>

                    {{--Operator Switcher Button --}}
                    <button
                        type="button"
                        wire:click="toggleOperatorModal"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.03] text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-white/10 transition text-xs font-semibold"
                        title="Click to switch terminal operator"
                    >
                        <div class="w-5 h-5 rounded-full bg-primary-100 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold text-[10px]">
                            {{ substr($activeOp?->name ?? 'OP', 0, 2) }}
                        </div>
                        <span>{{ $activeOp?->name ?? 'Administrator' }}</span>
                        <x-filament::icon icon="heroicon-m-arrows-right-left" class="w-3.5 h-3.5 text-gray-400"/>
                    </button>

                    {{--Fullscreen Toggle Button(Tablet Friendly) --}}
                    <button
                        type="button"
                        x-on:click="
                            if (!document.fullscreenElement) {
                                document.documentElement.requestFullscreen();
                                isFullscreen = true;
                            } else {
                                document.exitFullscreen();
                                isFullscreen = false;
                            }
                        "
                        class="p-2 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.03] text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-white/10 transition"
                        title="Toggle Kiosk Fullscreen Mode"
                    >
                        <x-filament::icon icon="heroicon-o-arrows-pointing-out" class="w-4 h-4"/>
                    </button>
                </div>
            </div>

            {{--Work Center Horizontal Filter Tabs(Odoo Style) --}}
            <div class="pt-2 border-t border-gray-100 dark:border-white/5 flex items-center gap-2 overflow-x-auto pb-1">
                <button
                    type="button"
                    wire:click="selectWorkCenter(null)"
                    class="sf-tab {{ $selectedWorkCenterId === null ? 'sf-tab-active' : 'sf-tab-inactive' }}"
                >
                    <span>All Stations</span>
                    <span class="px-1.5 py-0.2 text-[10px] rounded-full {{ $selectedWorkCenterId === null ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-white/10 text-gray-700 dark:text-gray-300' }}">
                        {{ $stats['total'] }}
                    </span>
                </button>

                @foreach($workCenters as $wc)
                    <button
                        type="button"
                        wire:click="selectWorkCenter({{ $wc['id'] }})"
                        class="sf-tab {{ $selectedWorkCenterId === $wc['id'] ? 'sf-tab-active' : 'sf-tab-inactive' }}"
                    >
                        @if($wc['is_blocked'])
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse" title="Station BLOCKED"></span>
                        @endif
                        <span>{{ $wc['name'] }}</span>
                        @if($wc['code'])
                            <span class="text-[9px] opacity-75 font-mono">({{ $wc['code'] }})</span>
                        @endif
                        @if($wc['total_active'] > 0)
                            <span class="px-1.5 py-0.2 text-[10px] rounded-full {{ $selectedWorkCenterId === $wc['id'] ? 'bg-white/20 text-white' : 'bg-primary-100 dark:bg-primary-950/60 text-primary-700 dark:text-primary-300 font-bold' }}">
                                {{ $wc['total_active'] }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>

            {{--Secondary Sub - Bar: Status Chips & Search --}}
            <div class="pt-2 border-t border-gray-100 dark:border-white/5 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex flex-wrap items-center gap-1.5">
                    <button
                        type="button"
                        wire:click="setStatusFilter('ready')"
                        class="sf-badge {{ $statusFilter === 'ready' ? 'sf-badge-blue ring-2 ring-blue-500/30' : 'sf-tab-inactive' }} cursor-pointer"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Ready: <strong>{{ $stats['ready'] }}</strong>
                    </button>

                    <button
                        type="button"
                        wire:click="setStatusFilter('progress')"
                        class="sf-badge {{ $statusFilter === 'progress' ? 'sf-badge-amber ring-2 ring-amber-500/30' : 'sf-tab-inactive' }} cursor-pointer"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        In Progress: <strong>{{ $stats['progress'] }}</strong>
                    </button>

                    <button
                        type="button"
                        wire:click="setStatusFilter('waiting')"
                        class="sf-badge {{ $statusFilter === 'waiting' ? 'sf-badge-red ring-2 ring-rose-500/30' : 'sf-tab-inactive' }} cursor-pointer"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                        Waiting / Blocked: <strong>{{ $stats['waiting'] }}</strong>
                    </button>

                    <button
                        type="button"
                        wire:click="setStatusFilter('done')"
                        class="sf-badge {{ $statusFilter === 'done' ? 'sf-badge-green ring-2 ring-emerald-500/30' : 'sf-tab-inactive' }} cursor-pointer"
                    >
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Done: <strong>{{ $stats['done'] }}</strong>
                    </button>

                    <button
                        type="button"
                        wire:click="setStatusFilter('all')"
                        class="sf-badge {{ $statusFilter === 'all' ? 'sf-badge-neutral ring-2 ring-gray-400/30' : 'sf-tab-inactive' }} cursor-pointer"
                    >
                        All: <strong>{{ $stats['total'] }}</strong>
                    </button>
                </div>

                {{--Search Filter Input --}}
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search card, product..."
                        class="py-1 pl-8 pr-3 text-xs rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-primary-500 w-40 sm:w-52"
                    />
                    <x-filament::icon icon="heroicon-m-magnifying-glass" class="absolute w-3.5 h-3.5 text-gray-400 left-2.5 top-2 pointer-events-none"/>
                </div>
            </div>
        </div>

        {{--Shop Floor Work Orders Grid Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($cards as $card)
                @php
                    $isProgress = $card['is_progress'];
                    $isReady = $card['is_ready'];
                    $isDone = $card['is_done'];
                    $isBlocked = $card['is_blocked'];

                    $cardClass = $isProgress
                        ? 'sf-card-progress ring-1 ring-amber-500/20'
                        : ($isReady ? 'sf-card-ready' : ($isDone ? 'sf-card-done' : 'sf-card-blocked'));

                    $stateBadge = match ($card['state']) {
                        'progress' => 'sf-badge-amber',
                        'ready' => 'sf-badge-blue',
                        'done' => 'sf-badge-green',
                        'cancel' => 'sf-badge-red',
                        default => 'sf-badge-neutral',
                    };
                @endphp

                <div class="sf-card p-4.5 flex flex-col justify-between gap-3.5 {{ $cardClass }}">
                    {{--Card Top: Header & Status --}}
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <div class="truncate">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-extrabold text-sm text-gray-950 dark:text-white">
                                        {{ $card['mo_name'] }}
                                    </span>
                                    <span class="text-gray-300 dark:text-gray-600">•</span>
                                    <span class="font-bold text-sm text-primary-600 dark:text-primary-400 truncate">
                                        {{ $card['name'] }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400">
                                        Station: <strong class="text-gray-800 dark:text-gray-200">{{ $card['work_center_name'] }}</strong>
                                    </span>
                                    @if($card['work_center_code'])
                                        <span class="px-1.5 py-0.2 text-[9px] font-mono font-bold bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 rounded">
                                            {{ $card['work_center_code'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <span class="sf-badge {{ $stateBadge }}">
                                    @if($isProgress)
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    @endif
                                    {{ $card['state_label'] }}
                                </span>

                                @if($card['started_at_formatted'])
                                    <span class="text-[10px] text-gray-400 font-mono">
                                        {{ $card['started_at_formatted'] }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{--Product & Variant Information --}}
                        <div class="mt-2.5 p-2.5 rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-gray-100 dark:border-white/5">
                            <div class="font-semibold text-xs text-gray-900 dark:text-white truncate">
                                {{ $card['product_name'] }}
                            </div>

                            {{--Target & Produced Stepper --}}
                            <div class="mt-2 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[11px] text-gray-500">Output:</span>
                                    <strong class="text-xs font-mono text-gray-900 dark:text-white">
                                        {{ (float) $card['quantity_produced'] }} / {{ (float) $card['quantity_target'] }} {{ $card['uom'] }}
                                    </strong>
                                </div>

                                {{--Touch Quantity Steppers --}}
                                @if(!$isDone)
                                    <div class="flex items-center gap-1">
                                        <button
                                            type="button"
                                            wire:click="decrementProducedQty({{ $card['id'] }})"
                                            class="sf-stepper-btn"
                                            title="Minus 1"
                                        >
                                            -
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="incrementProducedQty({{ $card['id'] }})"
                                            class="sf-stepper-btn"
                                            title="Plus 1"
                                        >
                                            +
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="quickFillQty({{ $card['id'] }})"
                                            class="px-2 py-1 text-[10px] font-bold rounded-lg bg-primary-50 text-primary-600 hover:bg-primary-100 dark:bg-primary-950/40 dark:text-primary-300 transition"
                                            title="Fill Target Quantity"
                                        >
                                            Full
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{--Visual Progress Bar --}}
                            <div class="w-full h-2 bg-gray-200 dark:bg-white/10 rounded-full mt-2 overflow-hidden">
                                <div
                                    class="h-full rounded-full transition-all duration-300 {{ $isDone ? 'bg-emerald-500' : ($isProgress ? 'bg-amber-500' : 'bg-primary-600') }}"
                                    style="width: {{ $card['progress_percent'] }}%"
                                ></div>
                            </div>
                        </div>

                        {{--Dependencies / Blockers Alert --}}
                        @if($isBlocked)
                            <div class="mt-2.5 p-2 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200/50 dark:border-rose-800/50 flex items-center gap-2 text-xs text-rose-700 dark:text-rose-300">
                                <x-filament::icon icon="heroicon-m-lock-closed" class="w-4 h-4 shrink-0 text-rose-500"/>
                                <div class="truncate">
                                    <strong>Blocked:</strong> Waiting on preceding operation
                                </div>
                            </div>
                        @endif

                        {{--Active Operator Indicator on this Card --}}
                        @if($isProgress && $card['active_worker_name'])
                            <div class="mt-2 flex items-center justify-between text-xs text-amber-700 dark:text-amber-400">
                                <span class="flex items-center gap-1 font-medium">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                    Operator: <strong>{{ $card['active_worker_name'] }}</strong>
                                </span>
                                <span class="font-mono text-[11px] font-bold">
                                    {{ $card['actual_duration'] }}m elapsed
                                </span>
                            </div>
                        @else
                            <div class="mt-2 flex items-center justify-between text-[11px] text-gray-400 font-mono">
                                <span>Planned: {{ $card['expected_duration'] }}m</span>
                                <span>Recorded: {{ $card['actual_duration'] }}m</span>
                            </div>
                        @endif
                    </div>

                    {{--Card Bottom: Touch Action Buttons --}}
                    <div class="pt-2.5 border-t border-gray-100 dark:border-white/5 flex items-center gap-2">
                        @if($isReady)
                            <button
                                type="button"
                                wire:click="startWorkOrder({{ $card['id'] }})"
                                class="sf-btn-touch flex-1 text-white bg-blue-600 hover:bg-blue-700 shadow-xs"
                            >
                                <x-filament::icon icon="heroicon-m-play" class="w-4 h-4"/>
                                <span>START OPERATION</span>
                            </button>
                        @elseif($isProgress)
                            <button
                                type="button"
                                wire:click="pauseWorkOrder({{ $card['id'] }})"
                                class="sf-btn-touch px-3.5 text-amber-800 bg-amber-100 hover:bg-amber-200 dark:bg-amber-950/40 dark:text-amber-300 border border-amber-200 dark:border-amber-800"
                                title="Pause Timer"
                            >
                                <x-filament::icon icon="heroicon-m-pause" class="w-4 h-4"/>
                                <span>Pause</span>
                            </button>

                            <button
                                type="button"
                                wire:click="finishWorkOrder({{ $card['id'] }})"
                                class="sf-btn-touch flex-1 text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs"
                            >
                                <x-filament::icon icon="heroicon-m-check" class="w-4 h-4"/>
                                <span>MARK AS DONE</span>
                            </button>
                        @elseif($isBlocked)
                            <button
                                type="button"
                                disabled
                                class="sf-btn-touch flex-1 text-gray-400 bg-gray-100 dark:bg-white/5 cursor-not-allowed opacity-60"
                            >
                                <x-filament::icon icon="heroicon-m-lock-closed" class="w-4 h-4"/>
                                <span>WAITING FOR PRECEDING OP</span>
                            </button>
                        @elseif($isDone)
                            <div class="flex-1 text-center py-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400 flex items-center justify-center gap-1.5">
                                <x-filament::icon icon="heroicon-m-check-badge" class="w-4 h-4 text-emerald-500"/>
                                <span>Completed Successfully</span>
                            </div>
                        @else
                            <button
                                type="button"
                                wire:click="startWorkOrder({{ $card['id'] }})"
                                class="sf-btn-touch flex-1 text-white bg-indigo-600 hover:bg-indigo-700 shadow-xs"
                            >
                                <x-filament::icon icon="heroicon-m-play" class="w-4 h-4"/>
                                <span>START</span>
                            </button>
                        @endif

                        {{--Details Inspection Button --}}
                        <button
                            type="button"
                            wire:click="openDetailModal({{ $card['id'] }})"
                            class="p-2.5 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/[0.03] text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-white/10 transition"
                            title="Inspect Details, Components & Reassign"
                        >
                            <x-filament::icon icon="heroicon-m-ellipsis-horizontal" class="w-5 h-5"/>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full sf-card p-12 text-center text-gray-500 dark:text-gray-400">
                    <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3"/>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">
                        No Work Orders in this View
                    </h3>
                    <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">
                        All operations for this filter are complete, or try selecting "All Stations" or changing the status filter.
                    </p>
                    <button
                        type="button"
                        wire:click="setStatusFilter('all')"
                        class="mt-4 px-4 py-2 text-xs font-semibold text-primary-600 bg-primary-50 dark:bg-primary-950/40 rounded-xl hover:bg-primary-100 transition"
                    >
                        Show All Work Orders
                    </button>
                </div>
            @endforelse
        </div>

        {{--Work Order Detail Inspection Modal --}}
        @if($selectedWo)
            <div
                class="sf-modal-backdrop"
                wire:click.self="closeDetailModal"
            >
                <div class="sf-modal-dialog">
                    {{--Modal Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50 dark:text-primary-400">
                                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-5 h-5"/>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">
                                        {{ $selectedWo->manufacturingOrder?->name }}: {{ $selectedWo->name }}
                                    </h3>
                                    @php
                                        $woState = $selectedWo->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $selectedWo->state->value : (string) $selectedWo->state;
                                        $modalBadge = match ($woState) {
                                            'progress' => 'sf-badge-amber',
                                            'ready' => 'sf-badge-blue',
                                            'done' => 'sf-badge-green',
                                            default => 'sf-badge-neutral',
                                        };
                                    @endphp
                                    <span class="sf-badge {{ $modalBadge }}">
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
                            wire:click="closeDetailModal"
                            class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition"
                        >
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5"/>
                        </button>
                    </div>

                    {{--Modal Body --}}
                    <div class="p-6 space-y-4 text-xs max-h-[75vh] overflow-y-auto">
                        {{--3 - Box Summary Grid --}}
                        <div class="grid grid-cols-3 gap-3">
                            <div class="sf-tile">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Product</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1 truncate">
                                    {{ $selectedWo->manufacturingOrder?->product?->name ?? '—' }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    {{ (float) $selectedWo->manufacturingOrder?->quantity }} {{ $selectedWo->manufacturingOrder?->product?->uom?->name }}
                                </div>
                            </div>

                            <div class="sf-tile">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Duration</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1 font-mono">
                                    {{ (float) $selectedWo->expected_duration }}m expected
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1 font-mono">
                                    Actual: {{ (float) $selectedWo->duration }}m
                                </div>
                            </div>

                            <div class="sf-tile">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Output Progress</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1">
                                    {{ (float) $selectedWo->quantity_produced }} / {{ (float) $selectedWo->manufacturingOrder?->quantity }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    Responsible: {{ $selectedWo->manufacturingOrder?->assignedUser?->name ?? '—' }}
                                </div>
                            </div>
                        </div>

                        {{--Components to Consume for this Operation --}}
                        @if($selectedWo->manufacturingOrder?->moveRaw?->isNotEmpty())
                            <div class="sf-tile space-y-2">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                                    Raw Materials & Components
                                </div>
                                <div class="divide-y divide-gray-100 dark:divide-white/5">
                                    @foreach($selectedWo->manufacturingOrder->moveRaw as $raw)
                                        <div class="py-1.5 flex items-center justify-between text-xs">
                                            <div class="font-medium text-gray-800 dark:text-gray-200">
                                                {{ $raw->product?->name }}
                                            </div>
                                            <div class="font-mono text-gray-600 dark:text-gray-400">
                                                {{ (float) $raw->product_uom_qty }} {{ $raw->product?->uom?->name }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{--Dependencies Section --}}
                        <div class="sf-tile space-y-2">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Sequential Dependencies</div>
                            @if($selectedWo->blockedByWorkOrders->isNotEmpty())
                                <div class="space-y-1">
                                    <div class="text-[11px] text-gray-500">Preceding Operations (Must finish first):</div>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($selectedWo->blockedByWorkOrders as $blocker)
                                            @php
                                                $isBlockerDone = in_array($blocker->state?->value ?? (string) $blocker->state, ['done', 'cancel']);
                                            @endphp
                                            <span class="sf-badge {{ $isBlockerDone ? 'sf-badge-green' : 'sf-badge-red' }}">
                                                <x-filament::icon icon="{{ $isBlockerDone ? 'heroicon-m-check-circle' : 'heroicon-m-lock-closed' }}" class="w-3 h-3"/>
                                                <span>{{ $blocker->name }}</span>
                                                <span class="opacity-75">({{ $blocker->state instanceof \Webkul\Manufacturing\Enums\WorkOrderState ? $blocker->state->getLabel() : $blocker->state }})</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="text-emerald-600 dark:text-emerald-400 font-medium">✓ No blocking operations (Ready to proceed)</div>
                            @endif
                        </div>

                        {{--Alternative Work Centers Reassignment --}}
                        @if($selectedWo->workCenter?->alternativeWorkCenters?->isNotEmpty() && !in_array($selectedWo->state?->value ?? (string) $selectedWo->state, ['done', 'cancel']))
                            <div class="sf-tile space-y-2">
                                <div class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                                    Alternative Work Centers (Workload Balancing)
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    Station is currently <strong class="text-gray-900 dark:text-white">{{ $selectedWo->workCenter->name }}</strong>. Reassign to another line:
                                </div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    @foreach($selectedWo->workCenter->alternativeWorkCenters as $altWc)
                                        <button
                                            type="button"
                                            wire:click="reassignWorkCenter({{ $selectedWo->id }}, {{ $altWc->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 dark:bg-indigo-950/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 transition"
                                        >
                                            <x-filament::icon icon="heroicon-m-arrows-right-left" class="w-3.5 h-3.5"/>
                                            <span>Move to {{ $altWc->name }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{--Modal Footer with Action Buttons --}}
                    <div class="flex items-center justify-between px-6 py-3.5 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-2">
                            @if(in_array($selectedWo->state?->value ?? (string) $selectedWo->state, ['ready', 'waiting', 'pending']))
                                <button
                                    type="button"
                                    wire:click="startWorkOrder({{ $selectedWo->id }})"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs transition"
                                >
                                    <x-filament::icon icon="heroicon-m-play" class="w-3.5 h-3.5"/>
                                    <span>Start Operation</span>
                                </button>
                            @elseif(($selectedWo->state?->value ?? (string) $selectedWo->state) === 'progress')
                                <button
                                    type="button"
                                    wire:click="finishWorkOrder({{ $selectedWo->id }})"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs transition"
                                >
                                    <x-filament::icon icon="heroicon-m-check" class="w-3.5 h-3.5"/>
                                    <span>Mark as Done</span>
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
                                    <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="w-3.5 h-3.5 text-white"/>
                                    <span>Open MO</span>
                                </a>
                            @endif

                            <button
                                type="button"
                                wire:click="closeDetailModal"
                                class="px-4 py-2 text-xs font-semibold text-gray-700 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 rounded-lg transition"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{--Switch Operator Modal --}}
        @if($showOperatorModal)
            <div
                class="sf-modal-backdrop"
                wire:click.self="toggleOperatorModal"
            >
                <div class="sf-modal-dialog max-w-md">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-2.5">
                            <div class="p-2 rounded-xl bg-primary-50 text-primary-600 dark:bg-primary-950/50">
                                <x-filament::icon icon="heroicon-o-user-group" class="w-5 h-5"/>
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                Switch Terminal Operator
                            </h3>
                        </div>
                        <button
                            type="button"
                            wire:click="toggleOperatorModal"
                            class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg transition"
                        >
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-5 h-5"/>
                        </button>
                    </div>

                    <div class="p-5 space-y-2 max-h-80 overflow-y-auto">
                        @foreach($availableOps as $op)
                            <button
                                type="button"
                                wire:click="switchOperator({{ $op->id }})"
                                class="w-full p-2.5 rounded-xl border flex items-center justify-between text-left transition {{ $activeOperatorId === $op->id ? 'bg-primary-50 border-primary-300 dark:bg-primary-950/40 dark:border-primary-800' : 'bg-white dark:bg-gray-800/50 border-gray-100 dark:border-white/5 hover:bg-gray-50 dark:hover:bg-white/[0.04]' }}"
                            >
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-200 flex items-center justify-center font-bold text-xs">
                                        {{ substr($op->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-xs text-gray-900 dark:text-white">{{ $op->name }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $op->email }}</div>
                                    </div>
                                </div>

                                @if($activeOperatorId === $op->id)
                                    <span class="sf-badge sf-badge-green text-[10px]">Active</span>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <div class="flex justify-end px-6 py-3 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <button
                            type="button"
                            wire:click="toggleOperatorModal"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
