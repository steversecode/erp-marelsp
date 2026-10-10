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
        /* Scoped Odoo MRP II Shop Floor Card Grid Design */
        .sf-root {
            font-family: inherit;
        }

        /* 6 Cards per Row Grid (Exact Odoo Style) */
        .sf-grid-6 {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 10px;
        }
        @media (max-width: 1180px) {
            .sf-grid-6 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
        @media (max-width: 720px) {
            .sf-grid-6 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 480px) {
            .sf-grid-6 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }

        /* Top Bar Container */
        .sf-topbar {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 14px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }
        :is(.dark, [data-theme="dark"]) .sf-topbar {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        }

        .sf-topbar-select {
            appearance: none;
            -webkit-appearance: none;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 7px;
            padding: 4px 24px 4px 9px;
            font-size: 12px;
            font-weight: 500;
            color: #1e293b;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 6px center;
            background-repeat: no-repeat;
            background-size: 13px 13px;
            cursor: pointer;
        }
        :is(.dark, [data-theme="dark"]) .sf-topbar-select {
            background-color: #1e293b;
            border-color: #334155;
            color: #f1f5f9;
        }

        /* Odoo Shop Floor Card */
        .sf-odoo-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 11px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: box-shadow 0.12s ease, transform 0.12s ease;
            position: relative;
        }
        :is(.dark, [data-theme="dark"]) .sf-odoo-card {
            background-color: #0f172a !important;
            border-color: #1e293b !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        }
        .sf-odoo-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }
        :is(.dark, [data-theme="dark"]) .sf-odoo-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
        }

        /* Left Accent Borders (Odoo MRP style) */
        .sf-border-progress { border-left: 4px solid #10b981 !important; }
        .sf-border-ready    { border-left: 4px solid #3b82f6 !important; }
        .sf-border-blocked  { border-left: 4px solid #ef4444 !important; }
        .sf-border-done     { border-left: 4px solid #059669 !important; }
        .sf-border-waiting  { border-left: 4px solid #94a3b8 !important; }

        /* Odoo Status Pill */
        .sf-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 1.5px 7px;
            border-radius: 9999px;
            font-size: 10px;
            font-weight: 600;
            line-height: 1.3;
            border: 1px solid transparent;
            white-space: nowrap;
        }
        .sf-pill-progress { background-color: #dcfce7; color: #15803d; }
        .sf-pill-ready    { background-color: #dbeafe; color: #1d4ed8; }
        .sf-pill-blocked  { background-color: #fee2e2; color: #b91c1c; }
        .sf-pill-done     { background-color: #dcfce7; color: #15803d; }
        .sf-pill-waiting  { background-color: #f1f5f9; color: #64748b; }

        :is(.dark, [data-theme="dark"]) .sf-pill-progress { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
        :is(.dark, [data-theme="dark"]) .sf-pill-ready    { background-color: rgba(59, 130, 246, 0.15); color: #93c5fd; }
        :is(.dark, [data-theme="dark"]) .sf-pill-blocked  { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }
        :is(.dark, [data-theme="dark"]) .sf-pill-done     { background-color: rgba(5, 150, 105, 0.15); color: #6ee7b7; }
        :is(.dark, [data-theme="dark"]) .sf-pill-waiting  { background-color: rgba(255, 255, 255, 0.08); color: #cbd5e1; }

        /* Stepper & Record Input */
        .sf-qty-input {
            width: 48px;
            padding: 3px 4px;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            border-radius: 5px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            color: #0f172a;
        }
        :is(.dark, [data-theme="dark"]) .sf-qty-input {
            background-color: #1e293b;
            border-color: #334155;
            color: #f8fafc;
        }

        .sf-btn-record {
            padding: 3.5px 7px;
            font-size: 10px;
            font-weight: 600;
            border-radius: 5px;
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.1s ease;
        }
        .sf-btn-record:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }
        :is(.dark, [data-theme="dark"]) .sf-btn-record {
            background-color: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }
        :is(.dark, [data-theme="dark"]) .sf-btn-record:hover {
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        /* Bottom Action Buttons (Odoo style) */
        .sf-btn-action-left {
            flex: 1;
            padding: 6px 4px;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.12s ease;
            white-space: nowrap;
        }
        .sf-btn-action-right {
            flex: 1;
            padding: 6px 4px;
            font-size: 11px;
            font-weight: 700;
            text-align: center;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.12s ease;
            background-color: #0f172a;
            color: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
        }
        .sf-btn-action-right:hover {
            background-color: #1e293b;
        }
        :is(.dark, [data-theme="dark"]) .sf-btn-action-right {
            background-color: #f8fafc;
            color: #0f172a;
        }
        :is(.dark, [data-theme="dark"]) .sf-btn-action-right:hover {
            background-color: #e2e8f0;
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
            border-radius: 14px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 650px;
            overflow: hidden;
            animation: sfModalZoom 0.15s ease-out;
        }
        :is(.dark, [data-theme="dark"]) .sf-modal-dialog {
            background-color: #0f172a;
            border-color: #1e293b;
        }
        @keyframes sfModalZoom {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    <div class="space-y-3.5 sf-root" x-data="{ isFullscreen: false }">
        {{-- Top Bar (Matching Image 2: Search, Station, Show, My Work, Refresh, Shown Count, Operator) --}}
        <div class="sf-topbar">
            {{-- Left Side: Search & Filters --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Search or scan work order input --}}
                <div class="relative">
                    <form wire:submit.prevent="handleBarcodeInput" class="relative">
                        <input
                            type="text"
                            wire:model="barcodeInput"
                            placeholder="Search or scan work order, order, product"
                            class="py-1.5 pl-8 pr-3 text-xs rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-1 focus:ring-primary-500 w-56 sm:w-72"
                        />
                        <x-filament::icon icon="heroicon-m-magnifying-glass" class="absolute w-3.5 h-3.5 text-gray-400 left-2.5 top-2.5 pointer-events-none" />
                    </form>
                </div>

                {{-- Station Dropdown Filter --}}
                <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Station:</span>
                    <select
                        wire:model.live="selectedWorkCenterId"
                        class="sf-topbar-select"
                    >
                        <option value="">All stations</option>
                        @foreach($workCenters as $wc)
                            <option value="{{ $wc['id'] }}">{{ $wc['name'] }} {{ $wc['code'] ? '('.$wc['code'].')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Show Status Dropdown Filter --}}
                <div class="flex items-center gap-1.5 text-xs text-gray-600 dark:text-gray-400">
                    <span class="font-medium">Show:</span>
                    <select
                        wire:model.live="statusFilter"
                        class="sf-topbar-select"
                    >
                        <option value="ready_and_running">Ready & running</option>
                        <option value="ready">Ready only</option>
                        <option value="progress">In Progress only</option>
                        <option value="waiting">Waiting / Blocked</option>
                        <option value="done">Done only</option>
                        <option value="all">All</option>
                    </select>
                </div>

                {{-- My Work Toggle Button --}}
                <button
                    type="button"
                    wire:click="toggleMyWork"
                    class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition {{ $onlyMyWork ? 'bg-primary-600 text-white border-primary-600' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100 dark:bg-white/[0.04] dark:text-gray-300 dark:border-white/10' }}"
                >
                    My work
                </button>

                {{-- Refresh Button --}}
                <button
                    type="button"
                    wire:click="$refresh"
                    class="p-1.5 text-gray-500 hover:text-gray-900 rounded-lg border border-gray-200 hover:bg-gray-100 dark:border-white/10 dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/10 transition"
                    title="Refresh"
                >
                    <x-filament::icon icon="heroicon-m-arrow-path" class="w-3.5 h-3.5" />
                </button>
            </div>

            {{-- Right Side: Shown Count & Operator Sign in --}}
            <div class="flex items-center gap-3 text-xs">
                {{-- Shown Count Text (e.g. 6 of 6 shown) --}}
                <span class="text-gray-400 font-medium">
                    {{ count($cards) }} of {{ $stats['total'] }} shown
                </span>

                {{-- Operator / Sign In --}}
                <div class="flex items-center gap-1.5 border-l border-gray-200 dark:border-white/10 pl-3">
                    <span class="text-gray-500">{{ $activeOp ? $activeOp->name : 'Not signed in' }}</span>
                    <button
                        type="button"
                        wire:click="toggleOperatorModal"
                        class="px-2.5 py-1 font-semibold text-xs rounded-md bg-gray-900 text-white hover:bg-black dark:bg-white dark:text-gray-900 transition"
                    >
                        {{ $activeOp ? 'Switch' : 'Sign in' }}
                    </button>
                </div>

                {{-- Fullscreen Toggle --}}
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
                    class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded-md transition"
                    title="Toggle Fullscreen"
                >
                    <x-filament::icon icon="heroicon-o-arrows-pointing-out" class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- 6 Cards per Row Grid (Exact Replica of Image 2) --}}
        <div class="sf-grid-6">
            @forelse($cards as $card)
                @php
                    $isProgress = $card['is_progress'];
                    $isReady = $card['is_ready'];
                    $isDone = $card['is_done'];
                    $isBlocked = $card['is_blocked'];

                    $accentBorder = $isProgress
                        ? 'sf-border-progress'
                        : ($isReady ? 'sf-border-ready' : ($isDone ? 'sf-border-done' : ($isBlocked ? 'sf-border-blocked' : 'sf-border-waiting')));

                    $statusPillClass = $isProgress
                        ? 'sf-pill-progress'
                        : ($isReady ? 'sf-pill-ready' : ($isDone ? 'sf-pill-done' : ($isBlocked ? 'sf-pill-blocked' : 'sf-pill-waiting')));
                @endphp

                <div class="flex flex-col">
                    {{-- Title line above the card (e.g. WH/MO/01388-002  2) --}}
                    <div class="flex items-center justify-between text-xs font-bold text-gray-800 dark:text-gray-200 px-1 pb-1">
                        <span class="truncate">{{ $card['mo_name'] }}</span>
                        <span class="text-gray-400 font-mono text-[11px]">{{ $loop->iteration }}</span>
                    </div>

                    {{-- Main Card Container --}}
                    <div class="sf-odoo-card flex-1 {{ $accentBorder }}">
                        <div>
                            {{-- Header inside card (Star, Operation Name, Status Badge) --}}
                            <div class="flex items-center justify-between gap-1 pb-2">
                                <div class="flex items-center gap-1.5 truncate">
                                    <button
                                        type="button"
                                        class="text-gray-300 hover:text-amber-400 transition"
                                        title="Priority"
                                    >
                                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                    <span class="font-bold text-xs text-gray-900 dark:text-white truncate">
                                        {{ $card['name'] }}
                                    </span>
                                </div>

                                <span class="sf-status-pill {{ $statusPillClass }}">
                                    {{ $card['state_label'] }}
                                </span>
                            </div>

                            {{-- Metadata Table (Order, Product, Station, Source) --}}
                            <div class="text-[11px] leading-relaxed text-gray-600 dark:text-gray-400 border-b border-gray-100 dark:border-white/5 pb-2 space-y-0.5">
                                <div class="grid grid-cols-3 gap-1">
                                    <span class="text-gray-400">Order</span>
                                    <span class="col-span-2 font-medium text-gray-800 dark:text-gray-200 truncate">{{ $card['mo_name'] }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-1">
                                    <span class="text-gray-400">Product</span>
                                    <span class="col-span-2 font-medium text-gray-800 dark:text-gray-200 truncate" title="{{ $card['product_name'] }}">{{ $card['product_name'] }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-1">
                                    <span class="text-gray-400">Station</span>
                                    <span class="col-span-2 font-medium text-gray-800 dark:text-gray-200 truncate">{{ $card['work_center_name'] }}</span>
                                </div>
                                <div class="grid grid-cols-3 gap-1">
                                    <span class="text-gray-400">Source</span>
                                    <span class="col-span-2 font-medium text-gray-800 dark:text-gray-200 truncate">{{ $card['source'] }}</span>
                                </div>
                            </div>

                            {{-- Big Quantity Section --}}
                            <div class="pt-2">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-2xl font-black text-gray-950 dark:text-white tracking-tight">
                                        {{ (int)$card['quantity_produced'] }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-normal">
                                        / {{ (int)$card['quantity_target'] }} {{ $card['uom'] }}
                                    </span>
                                </div>

                                {{-- Solid green progress bar --}}
                                <div class="w-full h-1.5 bg-gray-100 dark:bg-white/10 rounded-full mt-1.5 overflow-hidden">
                                    <div
                                        class="h-full bg-emerald-500 rounded-full transition-all duration-300"
                                        style="width: {{ $card['progress_percent'] }}%"
                                    ></div>
                                </div>

                                {{-- Time / Duration line (0 of 9000 min) --}}
                                <div class="text-[10px] text-gray-400 font-mono mt-1">
                                    {{ $card['actual_duration'] }} of {{ (int)$card['expected_duration'] }} min
                                </div>
                            </div>

                            {{-- Quantity Stepper / Record Row --}}
                            @if(! $isDone)
                                <div class="pt-2 flex items-center gap-1.5">
                                    <input
                                        type="number"
                                        wire:model="recordQtys.{{ $card['id'] }}"
                                        class="sf-qty-input"
                                    />
                                    <button
                                        type="button"
                                        wire:click="saveRecordedQty({{ $card['id'] }})"
                                        class="sf-btn-record flex-1"
                                    >
                                        Record
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="quickFillQty({{ $card['id'] }})"
                                        class="sf-btn-record"
                                    >
                                        All {{ (int)$card['quantity_target'] }}
                                    </button>
                                </div>
                            @endif
                        </div>

                        {{-- Action Buttons at bottom of card --}}
                        <div class="pt-3 mt-3 border-t border-gray-100 dark:border-white/5 flex items-center gap-1.5">
                            @if($isProgress)
                                <button
                                    type="button"
                                    wire:click="pauseWorkOrder({{ $card['id'] }})"
                                    class="sf-btn-action-left bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300"
                                    title="Click to Pause"
                                >
                                    In progress
                                </button>
                                <button
                                    type="button"
                                    wire:click="finishWorkOrder({{ $card['id'] }})"
                                    class="sf-btn-action-right"
                                >
                                    Finish step
                                </button>
                            @elseif($isReady)
                                <button
                                    type="button"
                                    wire:click="startWorkOrder({{ $card['id'] }})"
                                    class="sf-btn-action-left bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-950/60 dark:text-blue-300"
                                >
                                    Ready
                                </button>
                                <button
                                    type="button"
                                    wire:click="startWorkOrder({{ $card['id'] }})"
                                    class="sf-btn-action-right"
                                >
                                    Start step
                                </button>
                            @elseif($isBlocked)
                                <button
                                    type="button"
                                    disabled
                                    class="sf-btn-action-left bg-rose-50 text-rose-600 dark:bg-rose-950/40 opacity-70 cursor-not-allowed"
                                >
                                    Blocked
                                </button>
                                <button
                                    type="button"
                                    disabled
                                    class="sf-btn-action-right opacity-40 cursor-not-allowed bg-gray-400"
                                >
                                    Locked
                                </button>
                            @elseif($isDone)
                                <div class="w-full py-1.5 text-center font-bold text-xs rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40">
                                    Finished ✓
                                </div>
                            @else
                                <button
                                    type="button"
                                    wire:click="startWorkOrder({{ $card['id'] }})"
                                    class="sf-btn-action-right w-full"
                                >
                                    Start
                                </button>
                            @endif

                            {{-- Optional Details Modal Icon --}}
                            <button
                                type="button"
                                wire:click="openDetailModal({{ $card['id'] }})"
                                class="p-1.5 text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 rounded transition"
                                title="Inspect details"
                            >
                                <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl p-12 text-center text-gray-500">
                    <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" />
                    <div class="font-bold text-sm text-gray-900 dark:text-white">No Work Orders Matching Filter</div>
                    <div class="text-xs text-gray-400 mt-1">Try switching to "All" stations or changing the status filter.</div>
                </div>
            @endforelse
        </div>

        {{-- Work Order Detail Inspection Modal --}}
        @if($selectedWo)
            <div
                class="sf-modal-backdrop"
                wire:click.self="closeDetailModal"
            >
                <div class="sf-modal-dialog">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div class="flex items-center gap-2.5">
                            <div class="p-1.5 rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-950/50">
                                <x-filament::icon icon="heroicon-o-wrench-screwdriver" class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $selectedWo->manufacturingOrder?->name }}: {{ $selectedWo->name }}
                            </h3>
                        </div>
                        <button
                            type="button"
                            wire:click="closeDetailModal"
                            class="p-1 text-gray-400 hover:text-gray-600 rounded transition"
                        >
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-4 h-4" />
                        </button>
                    </div>

                    <div class="p-5 space-y-3.5 text-xs max-h-[75vh] overflow-y-auto">
                        <div class="grid grid-cols-3 gap-2.5">
                            <div class="p-2.5 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-white/[0.02]">
                                <div class="text-[10px] text-gray-400 uppercase font-semibold">Product</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1 truncate">
                                    {{ $selectedWo->manufacturingOrder?->product?->name }}
                                </div>
                            </div>
                            <div class="p-2.5 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-white/[0.02]">
                                <div class="text-[10px] text-gray-400 uppercase font-semibold">Duration</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1 font-mono">
                                    {{ (float)$selectedWo->expected_duration }}m expected
                                </div>
                            </div>
                            <div class="p-2.5 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-white/[0.02]">
                                <div class="text-[10px] text-gray-400 uppercase font-semibold">Station</div>
                                <div class="font-bold text-xs text-gray-900 dark:text-white mt-1">
                                    {{ $selectedWo->workCenter?->name }}
                                </div>
                            </div>
                        </div>

                        {{-- Dependencies --}}
                        <div class="p-3 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-white/[0.02] space-y-1.5">
                            <div class="text-[10px] font-semibold uppercase text-gray-400">Sequential Dependencies</div>
                            @if($selectedWo->blockedByWorkOrders->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($selectedWo->blockedByWorkOrders as $blocker)
                                        @php
                                            $isDone = in_array($blocker->state?->value ?? (string)$blocker->state, ['done', 'cancel']);
                                        @endphp
                                        <span class="sf-status-pill {{ $isDone ? 'sf-pill-done' : 'sf-pill-blocked' }}">
                                            {{ $blocker->name }} ({{ $blocker->state?->value }})
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-emerald-600 dark:text-emerald-400 font-medium">✓ No blocking operations</div>
                            @endif
                        </div>

                        {{-- Alternative Work Centers --}}
                        @if($selectedWo->workCenter?->alternativeWorkCenters?->isNotEmpty())
                            <div class="p-3 rounded-lg border border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-white/[0.02] space-y-1.5">
                                <div class="text-[10px] font-semibold uppercase text-gray-400">Alternative Work Centers</div>
                                <div class="flex flex-wrap gap-2 pt-1">
                                    @foreach($selectedWo->workCenter->alternativeWorkCenters as $altWc)
                                        <button
                                            type="button"
                                            wire:click="reassignWorkCenter({{ $selectedWo->id }}, {{ $altWc->id }})"
                                            class="px-2.5 py-1 text-xs font-semibold rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-white/10 hover:bg-gray-50 transition"
                                        >
                                            Move to {{ $altWc->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/[0.02]">
                        <div>
                            @if($selectedWo->manufacturing_order_id)
                                <a
                                    href="{{ \Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource::getUrl('view', ['record' => $selectedWo->manufacturing_order_id]) }}"
                                    target="_blank"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-md transition"
                                >
                                    <span>Open MO</span>
                                </a>
                            @endif
                        </div>
                        <button
                            type="button"
                            wire:click="closeDetailModal"
                            class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 rounded-md transition"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Switch Operator Modal --}}
        @if($showOperatorModal)
            <div
                class="sf-modal-backdrop"
                wire:click.self="toggleOperatorModal"
            >
                <div class="sf-modal-dialog max-w-sm">
                    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 dark:border-white/5">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">Switch Operator</h3>
                        <button type="button" wire:click="toggleOperatorModal" class="p-1 text-gray-400 hover:text-gray-600">
                            <x-filament::icon icon="heroicon-o-x-mark" class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="p-4 space-y-2 max-h-72 overflow-y-auto">
                        @foreach($availableOps as $op)
                            <button
                                type="button"
                                wire:click="switchOperator({{ $op->id }})"
                                class="w-full p-2 rounded-lg border flex items-center justify-between text-left text-xs transition {{ $activeOperatorId === $op->id ? 'bg-primary-50 border-primary-300 dark:bg-primary-950/40' : 'border-gray-100 hover:bg-gray-50' }}"
                            >
                                <span class="font-bold text-gray-900 dark:text-white">{{ $op->name }}</span>
                                @if($activeOperatorId === $op->id)
                                    <span class="sf-status-pill sf-pill-progress">Active</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <div class="flex justify-end px-5 py-2.5 border-t border-gray-100 dark:border-white/5">
                        <button type="button" wire:click="toggleOperatorModal" class="px-3 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
