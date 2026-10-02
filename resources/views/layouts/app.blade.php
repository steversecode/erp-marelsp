<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f8f9ff] text-[#0b1c30]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PT Marel ERP Portal') - Manajemen Inventaris & Manufaktur</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Google Material Symbols Outlined -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Tailwind CSS CDN with full reference theme -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#001849',
                            container: '#0d2c6c',
                            fixed: '#dae1ff',
                            'fixed-dim': '#b3c5ff',
                        },
                        secondary: {
                            DEFAULT: '#994700',
                            container: '#fb7800',
                            fixed: '#ffdbc8',
                            'fixed-dim': '#ffb68b',
                        },
                        tertiary: {
                            DEFAULT: '#081844',
                            container: '#202e5a',
                        },
                        surface: {
                            DEFAULT: '#f8f9ff',
                            container: '#e5eeff',
                            'container-low': '#eff4ff',
                            'container-high': '#dce9ff',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
        .hidden {
            display: none !important;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #0b1c30;
            background-color: #f8f9ff;
        }
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
            vertical-align: middle;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #eff4ff;
        }
        ::-webkit-scrollbar-thumb {
            background: #c5c6d2;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #757681;
        }
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #001849;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #202e5a;
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #7e96dc;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-[#f8f9ff] text-[#0b1c30] flex flex-col font-sans antialiased selection:bg-[#fb7800] selection:text-white">

    <!-- Mobile Navigation Backdrop -->
    <div id="mobileBackdrop" class="fixed inset-0 bg-[#001849]/50 z-40 lg:hidden backdrop-blur-sm hidden" onclick="toggleMobileNav(false)"></div>

    <!-- SIDEBAR -->
    <aside id="mainSidebar" class="fixed left-0 top-0 h-screen w-64 bg-[#001849] z-50 flex flex-col shadow-2xl transition-transform duration-200 select-none -translate-x-full lg:translate-x-0">
        <!-- Brand Header (Fixed at Top) -->
        <div class="h-16 flex items-center px-5 gap-3 bg-[#081844] border-b border-[#202e5a] shrink-0">
            <div class="w-8 h-8 rounded-lg bg-[#fb7800] flex items-center justify-center text-white font-display font-bold text-base shadow-sm">
                M
            </div>
            <div class="flex flex-col min-w-0">
                <span class="font-display font-bold text-white tracking-tight truncate text-sm">
                    PT MAREL
                </span>
                <span class="text-[10px] text-[#7e96dc] truncate uppercase tracking-wider font-semibold">
                    Sukses Pratama ERP
                </span>
            </div>
        </div>

        <!-- Scrollable Navigation Body -->
        <div class="flex-1 min-h-0 overflow-y-auto sidebar-scroll py-2">
            <!-- Section Category -->
            <div class="px-5 py-2.5">
                <span class="text-[11px] uppercase tracking-wider text-[#c5c6d2] font-semibold whitespace-nowrap block">
                    Manajemen Logistik &amp; Manufaktur
                </span>
            </div>

            <!-- Nav Items -->
            <nav class="flex flex-col gap-1 px-3" x-data="{ 
                openInventory: {{ (request()->routeIs('inventory.*') || request()->routeIs('products.*') || request()->routeIs('stock-in.*') || request()->routeIs('stock-out.*') || request()->routeIs('configuration.*')) ? 'true' : 'false' }},
                openManufacturing: {{ request()->routeIs('manufacturing.*') ? 'true' : 'false' }}
            }">
                <!-- 1. Dashboard -->
                <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all text-xs font-medium {{ request()->routeIs('dashboard') ? 'bg-[#0d2c6c] text-white font-semibold shadow-sm border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="material-symbols-outlined text-[19px] shrink-0">dashboard</span>
                        <span class="whitespace-nowrap truncate">Dashboard</span>
                    </div>
                </a>

                <!-- 2. Inventory Module (Parent Accordion with Dedicated Sub-Menus) -->
                <div class="space-y-1 pt-1">
                    <!-- Inventory Parent Header Toggle -->
                    <button type="button" @click="openInventory = !openInventory" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all text-xs font-medium cursor-pointer {{ (request()->routeIs('inventory.*') || request()->routeIs('products.*') || request()->routeIs('stock-in.*') || request()->routeIs('stock-out.*') || request()->routeIs('configuration.*')) ? 'bg-[#0d2c6c] text-white font-semibold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="material-symbols-outlined text-[19px] text-[#fb7800] shrink-0">warehouse</span>
                            <span class="whitespace-nowrap truncate font-bold">Inventory</span>
                        </div>
                        <span class="material-symbols-outlined text-[16px] text-[#7e96dc] transition-transform duration-200" :class="openInventory ? 'rotate-180' : ''">expand_more</span>
                    </button>

                    <!-- Sub Menus Container -->
                    <div x-show="openInventory" class="pl-3.5 pr-1 py-1 space-y-1.5 border-l-2 border-[#202e5a] ml-4">
                        <!-- Submenu: Products & SKU -->
                        <a href="{{ route('inventory.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('inventory.index') || request()->routeIs('products.*') ? 'bg-[#fb7800] text-white font-bold shadow-xs' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="material-symbols-outlined text-[16px]">inventory_2</span>
                                <span class="truncate">Products &amp; SKU</span>
                            </div>
                        </a>

                        <!-- Operations Sub-Section -->
                        <div class="pt-1.5">
                            <span class="text-[10px] uppercase tracking-wider text-[#7e96dc] font-bold px-2 block">Operations</span>
                            <div class="space-y-1 mt-1">
                                <!-- Receipts (PO) -->
                                <a href="{{ route('stock-in.po.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('stock-in.po*') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">move_to_inbox</span>
                                        <span class="truncate">Receipts (PO)</span>
                                    </div>
                                    <span class="text-[9px] px-1 py-0.2 rounded font-bold bg-[#202e5a] text-[#dae1ff]">IN</span>
                                </a>

                                <!-- Production Returns -->
                                <a href="{{ route('stock-in.leftover.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('stock-in.leftover*') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">replay</span>
                                        <span class="truncate">Production Returns</span>
                                    </div>
                                </a>

                                <!-- Subcontracting (Dyeing) -->
                                <a href="{{ route('stock-out.dyeing.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('stock-out.dyeing*') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">palette</span>
                                        <span class="truncate">Dyeing Subcontract</span>
                                    </div>
                                </a>

                                <!-- Sample Deliveries -->
                                <a href="{{ route('stock-out.sample.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('stock-out.sample*') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">science</span>
                                        <span class="truncate">Sample Issues</span>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Reporting Sub-Section -->
                        <div class="pt-1.5">
                            <span class="text-[10px] uppercase tracking-wider text-[#7e96dc] font-bold px-2 block">Reporting</span>
                            <a href="{{ route('inventory.moves') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium mt-1 {{ request()->routeIs('inventory.moves') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="material-symbols-outlined text-[15px]">bar_chart</span>
                                    <span class="truncate">Stock Moves Ledger</span>
                                </div>
                            </a>
                        </div>

                        <!-- Configuration Sub-Section (Odoo Standard) -->
                        <div class="pt-1.5">
                            <span class="text-[10px] uppercase tracking-wider text-[#7e96dc] font-bold px-2 block">Configuration</span>
                            <div class="space-y-1 mt-1">
                                <!-- Settings -->
                                <a href="{{ route('configuration.settings') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('configuration.settings') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">tune</span>
                                        <span class="truncate">Settings</span>
                                    </div>
                                </a>

                                <!-- Warehouses -->
                                <a href="{{ route('configuration.warehouses.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('configuration.warehouses*') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">warehouse</span>
                                        <span class="truncate">Warehouses</span>
                                    </div>
                                </a>

                                <!-- Locations -->
                                <a href="{{ route('configuration.locations.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('configuration.locations*') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">location_on</span>
                                        <span class="truncate">Locations</span>
                                    </div>
                                </a>

                                <!-- Operations Types -->
                                <a href="{{ route('configuration.operation-types') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('configuration.operation-types') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">alt_route</span>
                                        <span class="truncate">Operations Types</span>
                                    </div>
                                </a>

                                <!-- Product Categories -->
                                <a href="{{ route('configuration.categories') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('configuration.categories') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">category</span>
                                        <span class="truncate">Product Categories</span>
                                    </div>
                                </a>

                                <!-- Units of Measure -->
                                <a href="{{ route('configuration.uom.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('configuration.uom*') ? 'bg-[#0d2c6c] text-white font-bold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="material-symbols-outlined text-[15px]">straighten</span>
                                        <span class="truncate">Units of Measure</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Manufacturing Module (MRP) with Dedicated Sub-Menus -->
                <div class="space-y-1 pt-1">
                    <!-- Manufacturing Parent Header Toggle -->
                    <button type="button" @click="openManufacturing = !openManufacturing" class="w-full flex items-center justify-between px-3 py-2 rounded-lg transition-all text-xs font-medium cursor-pointer {{ request()->routeIs('manufacturing.*') ? 'bg-[#0d2c6c] text-white font-semibold border border-[#202e5a]' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="material-symbols-outlined text-[19px] text-[#fb7800] shrink-0">precision_manufacturing</span>
                            <span class="whitespace-nowrap truncate font-bold">Manufacturing</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] px-1.5 py-0.2 rounded font-bold bg-[#fb7800] text-white">MRP</span>
                            <span class="material-symbols-outlined text-[16px] text-[#7e96dc] transition-transform duration-200" :class="openManufacturing ? 'rotate-180' : ''">expand_more</span>
                        </div>
                    </button>

                    <!-- Sub Menus Container -->
                    <div x-show="openManufacturing" class="pl-3.5 pr-1 py-1 space-y-1.5 border-l-2 border-[#202e5a] ml-4">
                        <!-- Manufacturing Orders -->
                        <a href="{{ route('manufacturing.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ (request()->routeIs('manufacturing.index') || request()->routeIs('manufacturing.show') || request()->routeIs('manufacturing.create')) ? 'bg-[#fb7800] text-white font-bold shadow-xs' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="material-symbols-outlined text-[15px]">assignment</span>
                                <span class="truncate">Manufacturing Orders</span>
                            </div>
                        </a>

                        <!-- Bills of Materials (BoM) -->
                        <a href="{{ route('manufacturing.boms.index') }}" class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-all text-[11px] font-medium {{ request()->routeIs('manufacturing.boms.*') ? 'bg-[#fb7800] text-white font-bold shadow-xs' : 'text-[#b3c5ff] hover:bg-[#202e5a] hover:text-white' }}">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="material-symbols-outlined text-[15px]">account_tree</span>
                                <span class="truncate">Bills of Materials</span>
                            </div>
                            <span class="text-[9px] px-1 py-0.2 rounded font-bold bg-[#202e5a] text-[#dae1ff]">BoM</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Footer Status (Fixed at Bottom) -->
        <div class="p-4 bg-[#081844] border-t border-[#202e5a] flex items-center justify-between text-[#c5c6d2] text-[11px] shrink-0">
            <span class="whitespace-nowrap">Sistem Versi 2.4.0</span>
            <span class="flex items-center gap-1.5 text-white font-medium whitespace-nowrap shrink-0">
                <span class="w-2 h-2 rounded-full bg-[#fb7800] animate-pulse"></span>
                Online
            </span>
        </div>
    </aside>

    <!-- HEADER (TOPBAR) -->
    <header class="fixed top-0 left-0 lg:left-64 right-0 h-16 bg-white/90 backdrop-blur-xl border-b border-[#e5eeff] z-40 flex items-center justify-between px-4 sm:px-6">
        <!-- Left: Mobile Toggle & Global Search & Warehouse Filter -->
        <div class="flex items-center gap-3 sm:gap-5 flex-1 max-w-2xl">
            <button type="button" onclick="toggleMobileNav(true)" aria-label="Toggle Navigation" class="p-1.5 rounded-lg text-[#001849] hover:bg-[#eff4ff] lg:hidden cursor-pointer">
                <span class="material-symbols-outlined text-[24px]">menu</span>
            </button>

            <!-- Global Search -->
            <div class="relative flex-1 max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[#757681] text-[18px]">
                    search
                </span>
                <input type="text" id="globalSearchInput" placeholder="Cari SKU, nama barang, kode barcode, atau PO..."
                    class="w-full pl-9 pr-3 py-1.5 bg-[#eff4ff] rounded-lg text-[#0b1c30] text-xs sm:text-sm focus:outline-none focus:ring-1 focus:ring-[#0d2c6c] placeholder:text-[#757681]/70 transition-all border border-transparent focus:border-[#0d2c6c]">
            </div>

            <!-- Warehouse Selector Dropdown -->
            <div class="relative hidden md:block">
                <button type="button" id="warehouseBtn" onclick="toggleDropdown('warehouseMenu')" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#eff4ff] hover:bg-[#e5eeff] text-[#0b1c30] text-xs font-semibold cursor-pointer transition-colors border border-[#dce9ff]">
                    <span class="material-symbols-outlined text-[#fb7800] text-[18px]">store</span>
                    <span id="warehouseLabel" class="truncate max-w-[170px]">Semua Gudang Terpadu</span>
                    <span class="material-symbols-outlined text-[16px] text-[#757681]">expand_more</span>
                </button>

                <div id="warehouseMenu" class="hidden absolute left-0 mt-1.5 w-60 rounded-xl bg-white shadow-xl border border-[#c5c6d2] py-1 z-50">
                    <div class="px-3 py-1.5 text-[11px] font-bold text-[#757681] uppercase tracking-wider border-b border-[#eff4ff]">
                        Pilih Wilayah Gudang
                    </div>
                    <button type="button" onclick="selectWarehouse('Semua Gudang Terpadu')" class="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-[#eff4ff] text-[#0d2c6c] font-bold bg-[#eff4ff]">
                        <span>Semua Gudang Terpadu</span>
                        <span class="material-symbols-outlined text-[16px] text-[#0d2c6c]">check</span>
                    </button>
                    <button type="button" onclick="selectWarehouse('Gudang Bahan Baku (WH-MAIN)')" class="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-[#eff4ff] text-[#0b1c30]">
                        <span>Gudang Bahan Baku (WH-MAIN)</span>
                    </button>
                    <button type="button" onclick="selectWarehouse('Gudang Barang Jadi (WH-FG)')" class="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-[#eff4ff] text-[#0b1c30]">
                        <span>Gudang Barang Jadi (WH-FG)</span>
                    </button>
                    <button type="button" onclick="selectWarehouse('Lantai Produksi (PROD-FLOOR)')" class="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-[#eff4ff] text-[#0b1c30]">
                        <span>Lantai Produksi (PROD-FLOOR)</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Notifications, Help, Profile -->
        <div class="flex items-center gap-3 sm:gap-4">
            <!-- Notifications -->
            <div class="relative">
                <button type="button" onclick="toggleDropdown('notificationMenu')" class="relative p-2 rounded-lg text-[#444650] hover:text-[#0b1c30] hover:bg-[#eff4ff] transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#fb7800] ring-2 ring-white"></span>
                </button>

                <div id="notificationMenu" class="hidden absolute right-0 mt-2 w-80 sm:w-96 rounded-xl bg-white shadow-2xl border border-[#c5c6d2] overflow-hidden z-50">
                    <div class="p-3 bg-[#f8f9ff] border-b border-[#e5eeff] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-display font-bold text-sm text-[#001849]">Notifikasi Sistem</span>
                            <span class="px-1.5 py-0.5 rounded-full bg-[#fb7800] text-white text-[10px] font-bold">2 baru</span>
                        </div>
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-[#eff4ff]">
                        <div class="p-3 text-xs hover:bg-[#f8f9ff] transition-colors flex items-start gap-3 bg-[#eff4ff]/40">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5 bg-[#ffdad6] text-[#ba1a1a]">
                                <span class="material-symbols-outlined text-[16px]">warning</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-[#0b1c30]">Stok Benang Cotton 30s Menipis</p>
                                <p class="text-[#444650] text-[11px] mt-0.5">Sisa stok 5 kg di bawah batas minimum 500 kg. Component Switching disarankan.</p>
                                <span class="text-[10px] text-[#757681] mt-1 block">Baru saja</span>
                            </div>
                        </div>
                        <div class="p-3 text-xs hover:bg-[#f8f9ff] transition-colors flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 mt-0.5 bg-emerald-100 text-emerald-800">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-[#0b1c30]">Sinkronisasi Mutasi Sukses</p>
                                <p class="text-[#444650] text-[11px] mt-0.5">Ledger mutasi stok real-time aktif.</p>
                                <span class="text-[10px] text-[#757681] mt-1 block">15 menit lalu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Button -->
            <button type="button" onclick="openSopModal()" class="p-2 rounded-lg text-[#444650] hover:text-[#0b1c30] hover:bg-[#eff4ff] transition-colors cursor-pointer" title="Petunjuk SOP ERP">
                <span class="material-symbols-outlined text-[22px]">help_outline</span>
            </button>

            <div class="h-6 w-[1px] bg-[#c5c6d2]"></div>

            <!-- User Profile Pill -->
            <div class="relative">
                <button type="button" onclick="toggleDropdown('profileMenu')" class="flex items-center gap-2.5 cursor-pointer p-1 rounded-lg hover:bg-[#eff4ff] transition-colors">
                    <div class="flex flex-col text-right hidden sm:flex">
                        <span class="text-xs font-bold text-[#0b1c30] leading-tight whitespace-nowrap">
                            {{ auth()->user()->name ?? 'Bpk. Hendra Pratama' }}
                        </span>
                        <span class="text-[10px] text-[#757681] leading-tight whitespace-nowrap">
                            Supervisi Logistik
                        </span>
                    </div>
                    <img src="https://lh3.googleusercontent.com/aida/AEtjO1XVC8Oe-2cMLoHs5sPshIn2oHlyqHrc4CRbHDpm_pGD5u7pWXIYPhuhfVAnpnIs2kcaSEopwQGR0O9PaS5BOm2iQvmWljQmoA006Rd2zqcnPx-NmLT6rKZze-4m2YeU-b733k4cgyZPf6VKt3pK_ll_cGGcUPu3B9VnRxzlai2BjLuIz3Wo7jgQBue-FasZAgkfzWVPvyNfwGhVoF8nLiuhvst8jpCppY62AwBX7gRjvQo9DDH6Lr8CCXIv-jfEp7_tM6lNtnGXMg"
                        alt="Profile {{ auth()->user()->name ?? 'Admin' }}"
                        onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'32\' height=\'32\' viewBox=\'0 0 32 32\'><rect width=\'32\' height=\'32\' fill=\'%230d2c6c\'/><text x=\'50%\' y=\'55%\' font-size=\'12\' font-weight=\'bold\' fill=\'%23ffffff\' text-anchor=\'middle\' dominant-baseline=\'middle\'>HP</text></svg>';"
                        class="w-8 h-8 rounded-full object-cover ring-2 ring-[#0d2c6c]/20 shrink-0"
                        referrerpolicy="no-referrer">
                </button>

                <div id="profileMenu" class="hidden absolute right-0 mt-2 w-60 rounded-xl bg-white shadow-xl border border-[#c5c6d2] py-1.5 z-50 animate-in fade-in zoom-in-95 duration-100">
                    <div class="px-4 py-2 border-b border-[#eff4ff]">
                        <p class="text-xs font-bold text-[#001849] whitespace-nowrap">
                            {{ auth()->user()->name ?? 'Bpk. Hendra Pratama' }}
                        </p>
                        <p class="text-[11px] text-[#757681] truncate">
                            {{ auth()->user()->email ?? 'hendra.pratama@marel.co.id' }}
                        </p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-semibold whitespace-nowrap">
                            Kepala Logistik &amp; Rantai Pasok
                        </span>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}" class="w-full text-left px-4 py-2 text-xs text-[#0b1c30] hover:bg-[#eff4ff] flex items-center gap-2 whitespace-nowrap">
                            <span class="material-symbols-outlined text-[16px] text-[#757681]">manage_accounts</span>
                            <span>Pengaturan Profil Akun</span>
                        </a>
                        <button type="button" onclick="openSopModal()" class="w-full text-left px-4 py-2 text-xs text-[#0b1c30] hover:bg-[#eff4ff] flex items-center gap-2 whitespace-nowrap">
                            <span class="material-symbols-outlined text-[16px] text-[#757681]">help</span>
                            <span>Petunjuk SOP Logistik</span>
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-[#eff4ff]">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-xs text-[#ba1a1a] hover:bg-[#ffdad6]/40 flex items-center gap-2 font-semibold whitespace-nowrap cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">logout</span>
                                <span>Keluar / Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT AREA (Odoo Full-Width Fluid Layout) -->
    <main class="lg:pl-64 pt-16 flex-1 flex flex-col min-w-0">
        <div class="px-3 sm:px-5 py-4 w-full space-y-5">
            <!-- Flash Message Alerts -->
            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-semibold flex items-center gap-3 shadow-xs">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold flex items-center gap-3 shadow-xs">
                    <span class="material-symbols-outlined text-rose-600 text-[20px]">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
            {{ $slot ?? '' }}
        </div>
    </main>

    <!-- MODAL SOP & PETUNJUK LOGISTIK -->
    <div id="sopModal" class="fixed inset-0 bg-[#001849]/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl border border-[#c5c6d2] max-w-xl w-full p-6 text-left space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-[#eff4ff]">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#fb7800] text-[22px]">menu_book</span>
                    <h3 class="font-display font-bold text-base text-[#001849]">Petunjuk SOP &amp; Fitur ERP Marel</h3>
                </div>
                <button type="button" onclick="closeSopModal()" class="p-1 rounded-lg text-[#757681] hover:text-[#0b1c30]">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <div class="space-y-3 text-xs text-[#444650] leading-relaxed">
                <div class="p-3 rounded-xl bg-[#eff4ff] border border-[#dce9ff]">
                    <p class="font-bold text-[#001849] flex items-center gap-1.5 mb-1">
                        <span class="material-symbols-outlined text-[#fb7800] text-[16px]">shuffle</span>
                        Dynamic BoM Component Switching
                    </p>
                    <p>Jika benang standar dalam BoM habis, Anda dapat menggantinya langsung pada tingkat Manufacturing Order (MO) tanpa mengubah data Master BoM asli.</p>
                </div>
                <div class="p-3 rounded-xl bg-[#eff4ff] border border-[#dce9ff]">
                    <p class="font-bold text-[#001849] flex items-center gap-1.5 mb-1">
                        <span class="material-symbols-outlined text-[#0d2c6c] text-[16px]">swap_horiz</span>
                        Alur Masuk &amp; Keluar Stok Terpadu
                    </p>
                    <ul class="list-disc ml-5 space-y-1 mt-1 text-[11px]">
                        <li><strong>Stok Masuk:</strong> Penerimaan PO Supplier &amp; Retur Sisa Produksi dari MO.</li>
                        <li><strong>Stok Keluar:</strong> Konsumsi Produksi MO, Pengeluaran Sample R&amp;D, dan Pengiriman ke Proses Celup (Dyeing).</li>
                    </ul>
                </div>
            </div>
            <div class="pt-3 border-t border-[#eff4ff] text-right">
                <button type="button" onclick="closeSopModal()" class="px-4 py-2 rounded-xl bg-[#001849] text-white text-xs font-semibold hover:bg-[#0d2c6c]">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Interactive Navigation Scripts -->
    <script>
        function toggleMobileNav(show) {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('mobileBackdrop');
            if (show) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        function toggleDropdown(menuId) {
            const menu = document.getElementById(menuId);
            const isHidden = menu.classList.contains('hidden');
            // Close all
            document.querySelectorAll('#warehouseMenu, #notificationMenu, #profileMenu').forEach(el => el.classList.add('hidden'));
            if (isHidden) {
                menu.classList.remove('hidden');
            }
        }

        function selectWarehouse(name) {
            document.getElementById('warehouseLabel').textContent = name;
            document.getElementById('warehouseMenu').classList.add('hidden');
        }

        function openSopModal() {
            const modal = document.getElementById('sopModal');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.getElementById('profileMenu').classList.add('hidden');
        }

        function closeSopModal() {
            const modal = document.getElementById('sopModal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }

        // Close dropdowns on click outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#warehouseBtn') && !e.target.closest('#warehouseMenu') &&
                !e.target.closest('[onclick*="notificationMenu"]') && !e.target.closest('#notificationMenu') &&
                !e.target.closest('[onclick*="profileMenu"]') && !e.target.closest('#profileMenu')) {
                document.querySelectorAll('#warehouseMenu, #notificationMenu, #profileMenu').forEach(el => el.classList.add('hidden'));
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
