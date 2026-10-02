@extends('layouts.app')

@section('title', 'Manufacturing Orders')

@section('content')
<div class="space-y-3 animate-in fade-in duration-150 text-slate-800" x-data="{
    selectedIds: [],
    selectAll: false,
    toggleAll() {
        this.selectAll = !this.selectAll;
        if (this.selectAll) {
            this.selectedIds = Array.from(document.querySelectorAll('.mo-row-cb')).map(cb => cb.value);
        } else {
            this.selectedIds = [];
        }
    }
}">
    <!-- 1. Odoo Top Control Panel / Search & View Switcher Bar -->
    <div class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 flex flex-col lg:flex-row lg:items-center justify-between gap-3 shadow-xs">
        <!-- Left: New Button + Title -->
        <div class="flex items-center gap-3">
            <a href="{{ route('manufacturing.create') }}" class="px-3.5 py-1.5 bg-[#017e84] hover:bg-[#01656a] text-white text-xs font-semibold rounded shadow-xs transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">add</span>
                <span>New</span>
            </a>

            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-900">
                <span class="text-sm">Manufacturing Orders</span>
                <button type="button" class="text-slate-400 hover:text-slate-600 p-0.5 rounded cursor-pointer" title="Settings">
                    <span class="material-symbols-outlined text-[15px]">settings</span>
                </button>
            </div>
        </div>

        <!-- Middle: Odoo Search Bar with Filter Pills & 3-Column Mega Dropdown -->
        <div class="flex-1 max-w-2xl mx-auto w-full relative" x-data="{ openFilter: false }">
            <form method="GET" action="{{ route('manufacturing.index') }}" class="relative flex items-center bg-slate-50 border border-slate-300 rounded px-2.5 py-1 focus-within:border-[#017e84] focus-within:bg-white focus-within:ring-1 focus-within:ring-[#017e84]/30 shadow-2xs transition-all">
                <span class="material-symbols-outlined text-slate-400 text-[18px] mr-1.5 shrink-0">search</span>

                <!-- Active Filter Pill (Odoo Style) -->
                @if(($filter ?? 'todo') && ($filter ?? 'todo') !== 'all')
                <div class="inline-flex items-center gap-1 px-2 py-0.5 bg-slate-200/80 text-slate-800 rounded text-[11px] font-semibold mr-1.5 shrink-0">
                    <span class="material-symbols-outlined text-[13px] text-slate-600">filter_alt</span>
                    <span>
                        @php
                            $labels = [
                                'todo' => 'To Do',
                                'unbuilt' => 'Unbuilt',
                                'done' => 'Done',
                                'cancelled' => 'Cancelled',
                                'starred' => 'Starred',
                                'draft' => 'Draft',
                                'confirmed' => 'Confirmed',
                                'planned' => 'Planned',
                                'in_progress' => 'In Progress',
                                'to_close' => 'To Close',
                                'mo_pending' => 'MO Pending',
                                'mo_ready' => 'MO Ready',
                                'late' => 'Late'
                            ];
                        @endphp
                        {{ $labels[$filter] ?? ucfirst($filter) }}
                    </span>
                    <a href="{{ route('manufacturing.index', ['search' => $search, 'filter' => 'all', 'group_by' => $groupBy]) }}" class="text-slate-500 hover:text-slate-900 ml-0.5" title="Remove filter">✕</a>
                </div>
                @endif

                <!-- Active Group By Pill -->
                @if(!empty($groupBy))
                <div class="inline-flex items-center gap-1 px-2 py-0.5 bg-[#017e84]/10 text-[#017e84] border border-[#017e84]/20 rounded text-[11px] font-semibold mr-1.5 shrink-0">
                    <span class="material-symbols-outlined text-[13px] text-[#017e84]">layers</span>
                    <span>Group: {{ ucfirst(str_replace('_', ' ', $groupBy)) }}</span>
                    <a href="{{ route('manufacturing.index', ['search' => $search, 'filter' => $filter, 'group_by' => null]) }}" class="text-[#017e84] hover:text-[#01656a] ml-0.5" title="Remove grouping">✕</a>
                </div>
                @endif

                <input type="hidden" name="filter" value="{{ $filter ?? 'todo' }}">
                @if(!empty($groupBy))
                <input type="hidden" name="group_by" value="{{ $groupBy }}">
                @endif

                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search..." class="w-full text-xs text-slate-800 outline-none bg-transparent">

                <!-- Filter Dropdown Trigger Icon (Odoo Caret) -->
                <button type="button" @click="openFilter = !openFilter" class="p-1 hover:bg-slate-200 rounded text-slate-500 hover:text-slate-800 cursor-pointer shrink-0 ml-1 transition-colors" title="Filters & Group By">
                    <span class="material-symbols-outlined text-[18px]">arrow_drop_down</span>
                </button>
            </form>

            <!-- 3-Column Odoo MRP Filter & Group By Mega Dropdown -->
            <div x-show="openFilter" 
                 @click.outside="openFilter = false" 
                 x-cloak 
                 style="display: none;" 
                 class="absolute left-0 right-0 lg:left-auto lg:right-0 mt-1.5 w-full lg:w-[680px] bg-white border border-slate-300 rounded-md shadow-2xl z-50 overflow-hidden text-xs max-h-[520px] flex flex-col">
                
                <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200 overflow-y-auto p-2 bg-white">
                    
                    <!-- COLUMN 1: FILTERS -->
                    <div class="space-y-0.5 pr-1">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 font-bold text-slate-800 text-[12px]">
                            <span class="material-symbols-outlined text-[16px] text-slate-700">filter_alt</span>
                            <span>Filters</span>
                        </div>

                        <!-- Sub-group 1: Basic status -->
                        <div class="space-y-0.5">
                            <a href="{{ route('manufacturing.index', ['filter' => 'todo', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ ($filter ?? 'todo') === 'todo' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ ($filter ?? 'todo') === 'todo' ? '✓' : '' }}</span>
                                <span>To Do</span>
                            </a>
                            <a href="{{ route('manufacturing.index', ['filter' => 'unbuilt', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'unbuilt' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'unbuilt' ? '✓' : '' }}</span>
                                <span>Unbuilt</span>
                            </a>
                            <a href="{{ route('manufacturing.index', ['filter' => 'done', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'done' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'done' ? '✓' : '' }}</span>
                                <span>Done</span>
                            </a>
                            <a href="{{ route('manufacturing.index', ['filter' => 'cancelled', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'cancelled' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'cancelled' ? '✓' : '' }}</span>
                                <span>Cancelled</span>
                            </a>
                        </div>

                        <hr class="my-1.5 border-slate-200">

                        <!-- Sub-group 2: Starred -->
                        <div class="space-y-0.5">
                            <a href="{{ route('manufacturing.index', ['filter' => 'starred', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'starred' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'starred' ? '✓' : '' }}</span>
                                <span>Starred</span>
                            </a>
                        </div>

                        <hr class="my-1.5 border-slate-200">

                        <!-- Sub-group 3: MO Pipeline States -->
                        <div class="space-y-0.5">
                            <a href="{{ route('manufacturing.index', ['filter' => 'draft', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'draft' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'draft' ? '✓' : '' }}</span>
                                <span>Draft</span>
                            </a>
                            <a href="{{ route('manufacturing.index', ['filter' => 'confirmed', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'confirmed' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'confirmed' ? '✓' : '' }}</span>
                                <span>Confirmed</span>
                            </a>
                            <a href="{{ route('manufacturing.index', ['filter' => 'planned', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'planned' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'planned' ? '✓' : '' }}</span>
                                <span>Planned</span>
                            </a>
                            <a href="{{ route('manufacturing.index', ['filter' => 'in_progress', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'in_progress' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'in_progress' ? '✓' : '' }}</span>
                                <span>In Progress</span>
                            </a>
                            <a href="{{ route('manufacturing.index', ['filter' => 'to_close', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'to_close' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'to_close' ? '✓' : '' }}</span>
                                <span>To Close</span>
                            </a>
                        </div>

                        <hr class="my-1.5 border-slate-200">

                        <!-- Sub-group 4: Material Availability -->
                        <div class="space-y-0.5">
                            <a href="{{ route('manufacturing.index', ['filter' => 'mo_pending', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'mo_pending' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'mo_pending' ? '✓' : '' }}</span>
                                <span>MO Pending</span>
                            </a>
                            <a href="{{ route('manufacturing.index', ['filter' => 'mo_ready', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'mo_ready' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'mo_ready' ? '✓' : '' }}</span>
                                <span>MO Ready</span>
                            </a>
                        </div>

                        <hr class="my-1.5 border-slate-200">

                        <!-- Sub-group 5: Schedule Alerts -->
                        <div class="space-y-0.5">
                            <a href="{{ route('manufacturing.index', ['filter' => 'late', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 {{ $filter === 'late' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <span class="w-4 font-bold text-xs">{{ $filter === 'late' ? '✓' : '' }}</span>
                                <span>Late</span>
                            </a>
                            <a href="{{ route('manufacturing.index', ['filter' => 'late', 'search' => $search, 'group_by' => $groupBy]) }}" 
                               class="flex items-center px-3 py-1 rounded hover:bg-slate-100 text-slate-700">
                                <span class="w-4 font-bold text-xs"></span>
                                <span>Delayed Productions</span>
                            </a>
                        </div>
                    </div>

                    <!-- COLUMN 2: GROUP BY -->
                    <div class="space-y-0.5 px-1">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 font-bold text-slate-800 text-[12px]">
                            <span class="material-symbols-outlined text-[16px] text-slate-700">layers</span>
                            <span>Group By</span>
                        </div>

                        <div class="space-y-0.5">
                            <a href="{{ route('manufacturing.index', ['group_by' => $groupBy === 'product' ? null : 'product', 'filter' => $filter, 'search' => $search]) }}" 
                               class="flex items-center justify-between px-3 py-1 rounded hover:bg-slate-100 {{ $groupBy === 'product' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <div class="flex items-center">
                                    <span class="w-4 font-bold text-xs">{{ $groupBy === 'product' ? '✓' : '' }}</span>
                                    <span>Product</span>
                                </div>
                            </a>
                            <a href="{{ route('manufacturing.index', ['group_by' => $groupBy === 'status' ? null : 'status', 'filter' => $filter, 'search' => $search]) }}" 
                               class="flex items-center justify-between px-3 py-1 rounded hover:bg-slate-100 {{ $groupBy === 'status' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <div class="flex items-center">
                                    <span class="w-4 font-bold text-xs">{{ $groupBy === 'status' ? '✓' : '' }}</span>
                                    <span>Status</span>
                                </div>
                            </a>
                            <a href="{{ route('manufacturing.index', ['group_by' => $groupBy === 'material' ? null : 'material', 'filter' => $filter, 'search' => $search]) }}" 
                               class="flex items-center justify-between px-3 py-1 rounded hover:bg-slate-100 {{ $groupBy === 'material' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <div class="flex items-center">
                                    <span class="w-4 font-bold text-xs">{{ $groupBy === 'material' ? '✓' : '' }}</span>
                                    <span>Material Availability</span>
                                </div>
                            </a>
                            <a href="{{ route('manufacturing.index', ['group_by' => $groupBy === 'procurement' ? null : 'procurement', 'filter' => $filter, 'search' => $search]) }}" 
                               class="flex items-center justify-between px-3 py-1 rounded hover:bg-slate-100 {{ $groupBy === 'procurement' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <div class="flex items-center">
                                    <span class="w-4 font-bold text-xs">{{ $groupBy === 'procurement' ? '✓' : '' }}</span>
                                    <span>Procurement Group</span>
                                </div>
                            </a>
                            <a href="{{ route('manufacturing.index', ['group_by' => $groupBy === 'date' ? null : 'date', 'filter' => $filter, 'search' => $search]) }}" 
                               class="flex items-center justify-between px-3 py-1 rounded hover:bg-slate-100 {{ $groupBy === 'date' ? 'font-bold text-[#017e84]' : 'text-slate-700' }}">
                                <div class="flex items-center">
                                    <span class="w-4 font-bold text-xs">{{ $groupBy === 'date' ? '✓' : '' }}</span>
                                    <span>Date</span>
                                </div>
                                <span class="material-symbols-outlined text-[15px] text-slate-400">arrow_drop_down</span>
                            </a>
                        </div>

                        <hr class="my-1.5 border-slate-200">

                        <div class="px-3 py-1 text-slate-600 hover:text-slate-900 cursor-pointer flex items-center justify-between">
                            <span>Add Custom Group</span>
                            <span class="material-symbols-outlined text-[15px] text-slate-400">arrow_drop_down</span>
                        </div>
                    </div>

                    <!-- COLUMN 3: FAVORITES -->
                    <div class="space-y-0.5 pl-1">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 font-bold text-slate-800 text-[12px]">
                            <span class="material-symbols-outlined text-[16px] text-amber-500 fill-amber-500">star</span>
                            <span>Favorites</span>
                        </div>

                        <div class="space-y-0.5">
                            <div class="px-3 py-1 text-slate-600 hover:text-slate-900 cursor-pointer flex items-center justify-between">
                                <span>Save current search</span>
                                <span class="material-symbols-outlined text-[15px] text-slate-400">arrow_drop_down</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Bottom Filter Action Bar -->
                <div class="bg-slate-50 border-t border-slate-200 px-3 py-1.5 flex items-center justify-between text-[11px] text-slate-500">
                    <a href="{{ route('manufacturing.index', ['filter' => 'all']) }}" class="text-[#017e84] hover:underline font-medium">
                        Clear All Filters
                    </a>
                    <button type="button" @click="openFilter = false" class="px-2.5 py-0.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded font-medium cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Record Pager & View Switchers -->
        <div class="flex items-center gap-3 justify-between lg:justify-end">
            <!-- Pager -->
            <div class="flex items-center gap-2 text-xs text-slate-500 font-mono">
                <span>{{ $orders->firstItem() ?? 1 }} - {{ $orders->lastItem() ?? $orders->count() }} / {{ $orders->total() }}</span>
                <div class="flex items-center border border-slate-200 rounded overflow-hidden bg-white">
                    @if($orders->onFirstPage())
                        <span class="p-1 text-slate-300"><span class="material-symbols-outlined text-[14px]">chevron_left</span></span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" class="p-1 hover:bg-slate-100 text-slate-600 transition-colors"><span class="material-symbols-outlined text-[14px]">chevron_left</span></a>
                    @endif

                    @if($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" class="p-1 hover:bg-slate-100 text-slate-600 transition-colors"><span class="material-symbols-outlined text-[14px]">chevron_right</span></a>
                    @else
                        <span class="p-1 text-slate-300"><span class="material-symbols-outlined text-[14px]">chevron_right</span></span>
                    @endif
                </div>
            </div>

            <!-- Odoo View Switcher Icons -->
            <div class="flex items-center border border-slate-200 rounded overflow-hidden bg-white shadow-2xs">
                <!-- List View (Active) -->
                <button type="button" class="p-1.5 bg-slate-100 text-slate-800 border-r border-slate-200 cursor-pointer" title="List View">
                    <span class="material-symbols-outlined text-[16px]">view_list</span>
                </button>
                <!-- Kanban View -->
                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 border-r border-slate-200 cursor-pointer" title="Kanban View">
                    <span class="material-symbols-outlined text-[16px]">view_module</span>
                </button>
                <!-- Calendar View -->
                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 border-r border-slate-200 cursor-pointer" title="Calendar View">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                </button>
                <!-- Pivot View -->
                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 border-r border-slate-200 cursor-pointer" title="Pivot View">
                    <span class="material-symbols-outlined text-[16px]">table_chart</span>
                </button>
                <!-- Graph View -->
                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 border-r border-slate-200 cursor-pointer" title="Graph View">
                    <span class="material-symbols-outlined text-[16px]">bar_chart</span>
                </button>
                <!-- Activity View -->
                <button type="button" class="p-1.5 hover:bg-slate-50 text-slate-500 cursor-pointer" title="Activity View">
                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 2. Odoo List View Table -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-slate-600 text-[11px] font-semibold border-b border-slate-200 sticky top-0 z-10 select-none">
                    <tr>
                        <!-- Checkbox -->
                        <th class="py-2.5 px-3 w-8 text-center">
                            <input type="checkbox" @click="toggleAll()" class="rounded text-[#017e84] focus:ring-[#017e84] w-3.5 h-3.5 cursor-pointer">
                        </th>
                        <!-- Star -->
                        <th class="py-2.5 px-2 w-7 text-center"></th>
                        <!-- Reference -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 whitespace-nowrap">Reference</th>
                        <!-- Start Date -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 whitespace-nowrap">Start</th>
                        <!-- Sale Order -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 whitespace-nowrap">Sale order</th>
                        <!-- Customer -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 whitespace-nowrap">Customer</th>
                        <!-- Product -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 min-w-[200px]">Product</th>
                        <!-- Next Activity -->
                        <th class="py-2.5 px-2 w-8 text-center" title="Next Activity">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span>
                        </th>
                        <!-- Source -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 whitespace-nowrap">Source</th>
                        <!-- Sales Order -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 whitespace-nowrap">Sales Order</th>
                        <!-- Component Status -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 whitespace-nowrap">Component Status</th>
                        <!-- Quantity -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 text-right whitespace-nowrap">Quantity</th>
                        <!-- UoM -->
                        <th class="py-2.5 px-2 font-semibold text-slate-700 whitespace-nowrap">UoM</th>
                        <!-- State -->
                        <th class="py-2.5 px-3 font-semibold text-slate-700 text-center whitespace-nowrap">State</th>
                        <!-- Column Selector Icon -->
                        <th class="py-2.5 px-2 w-7 text-center text-slate-400">
                            <span class="material-symbols-outlined text-[16px]">swap_horiz</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $mo)
                    <tr class="hover:bg-slate-50/80 transition-colors" :class="selectedIds.includes('{{ $mo->id }}') ? 'bg-sky-50/50' : ''">
                        <!-- Checkbox -->
                        <td class="py-2 px-3 text-center">
                            <input type="checkbox" value="{{ $mo->id }}" x-model="selectedIds" class="mo-row-cb rounded text-[#017e84] focus:ring-[#017e84] w-3.5 h-3.5 cursor-pointer">
                        </td>

                        <!-- Star -->
                        <td class="py-2 px-2 text-center">
                            <button type="button" class="text-slate-300 hover:text-amber-400 cursor-pointer" title="Favorite">
                                <span class="material-symbols-outlined text-[16px]">star_border</span>
                            </button>
                        </td>

                        <!-- Reference (Bold Link) -->
                        <td class="py-2 px-3 font-mono font-bold text-slate-900 whitespace-nowrap">
                            <a href="{{ route('manufacturing.show', $mo->id) }}" class="text-slate-900 hover:text-[#017e84] hover:underline">
                                {{ $mo->mo_number }}
                            </a>
                        </td>

                        <!-- Start Date (Red Font) -->
                        <td class="py-2 px-3 whitespace-nowrap text-rose-700 font-medium">
                            {{ $mo->start_date ? $mo->start_date->format('d F Y') : '-' }}
                        </td>

                        <!-- Sale Order -->
                        <td class="py-2 px-3 whitespace-nowrap text-slate-600 font-mono">
                            {{ $mo->notes ? \Illuminate\Support\Str::limit($mo->notes, 15) : '-' }}
                        </td>

                        <!-- Customer -->
                        <td class="py-2 px-3 whitespace-nowrap text-slate-600">
                            {{ $mo->creator->name ?? '-' }}
                        </td>

                        <!-- Product Name -->
                        <td class="py-2 px-3">
                            <a href="{{ route('manufacturing.show', $mo->id) }}" class="font-medium text-slate-900 hover:text-[#017e84] hover:underline truncate block max-w-xs" title="{{ $mo->product->name }}">
                                {{ $mo->product->name }}
                            </a>
                        </td>

                        <!-- Next Activity (Clock) -->
                        <td class="py-2 px-2 text-center text-slate-400">
                            <span class="material-symbols-outlined text-[16px] hover:text-slate-600 cursor-pointer" title="Activity">schedule</span>
                        </td>

                        <!-- Source Location / Source Doc -->
                        <td class="py-2 px-3 whitespace-nowrap text-slate-600 font-mono text-[11px]">
                            {{ $mo->sourceLocation->name ?? 'WH/Stock' }}
                        </td>

                        <!-- Sales Order -->
                        <td class="py-2 px-3 whitespace-nowrap text-slate-500 font-mono text-[11px]">
                            -
                        </td>

                        <!-- Component Status (Green / Red text) -->
                        <td class="py-2 px-3 whitespace-nowrap">
                            @if(!empty($mo->all_components_available))
                                <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                    <span>Available</span>
                                </span>
                            @else
                                <span class="text-rose-600 font-semibold flex items-center gap-1">
                                    <span>Not Available</span>
                                </span>
                            @endif
                        </td>

                        <!-- Quantity -->
                        <td class="py-2 px-3 text-right font-mono font-bold text-slate-800 whitespace-nowrap tabular-nums">
                            {{ number_format($mo->planned_qty, 4) }}
                        </td>

                        <!-- UoM -->
                        <td class="py-2 px-2 whitespace-nowrap text-slate-600 font-medium">
                            {{ $mo->uom }}
                        </td>

                        <!-- State (Odoo Pill Badge) -->
                        <td class="py-2 px-3 text-center whitespace-nowrap">
                            @if($mo->status === 'draft')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    Draft
                                </span>
                            @elseif($mo->status === 'confirmed')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-100 text-teal-800">
                                    Confirmed
                                </span>
                            @elseif($mo->status === 'in_progress')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#fb7800] text-white shadow-2xs">
                                    In Progress
                                </span>
                            @elseif($mo->status === 'done')
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-700 text-white">
                                    Done
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 text-slate-800">
                                    {{ ucfirst($mo->status) }}
                                </span>
                            @endif
                        </td>

                        <!-- Column Action -->
                        <td class="py-2 px-2 text-center text-slate-300">
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" class="py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-[36px] text-slate-300">precision_manufacturing</span>
                                <span class="font-medium text-xs">Belum ada Manufacturing Orders yang cocok dengan kriteria pencarian.</span>
                                <a href="{{ route('manufacturing.create') }}" class="mt-1 px-3 py-1.5 bg-[#017e84] text-white rounded text-xs font-semibold hover:bg-[#01656a]">
                                    + Buat MO Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
