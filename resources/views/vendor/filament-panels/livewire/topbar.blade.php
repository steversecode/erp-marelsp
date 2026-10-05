<div class="fi-topbar-ctn">
    @php
        $navigation = filament()->getNavigation();
        $isRtl = __('filament-panels::layout.direction') === 'rtl';
        $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();
        $isSidebarFullyCollapsibleOnDesktop = filament()->isSidebarFullyCollapsibleOnDesktop();
        $hasTopNavigation = filament()->hasTopNavigation();
        $hasNavigation = filament()->hasNavigation();
        $hasTenancy = filament()->hasTenancy();
        $isAdminPanel = filament()->getCurrentPanel()->getId() === 'admin';
    @endphp

    <nav class="fi-topbar">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_START) }}

        @if ($hasNavigation)
            <x-filament::icon-button
                color="gray"
                :icon="\Filament\Support\Icons\Heroicon::OutlinedBars3"
                :icon-alias="\Filament\View\PanelsIconAlias::TOPBAR_OPEN_SIDEBAR_BUTTON"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                x-cloak
                x-data="{}"
                x-on:click="$store.sidebar.open()"
                x-show="! $store.sidebar.isOpen"
                class="fi-topbar-open-sidebar-btn"
            />

            <x-filament::icon-button
                color="gray"
                :icon="\Filament\Support\Icons\Heroicon::OutlinedXMark"
                :icon-alias="\Filament\View\PanelsIconAlias::TOPBAR_CLOSE_SIDEBAR_BUTTON"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                x-cloak
                x-data="{}"
                x-on:click="$store.sidebar.close()"
                x-show="$store.sidebar.isOpen"
                class="fi-topbar-close-sidebar-btn"
            />

            @if ($isAdminPanel)
                @php
                    $appItems = [];
                    foreach ($navigation as $group) {
                        $groupLabel = $group->getLabel();
                        $groupIcon = $group->getIcon();
                        $firstItem = $group->getItems()->first();
                        $itemUrl = $firstItem?->getUrl();

                        if (! $groupLabel || ! $itemUrl || ! $groupIcon) {
                            continue;
                        }

                        $appItems[] = [
                            'label' => $groupLabel,
                            'icon' => $groupIcon,
                            'url' => $itemUrl,
                            'isActive' => $group->isActive(),
                        ];
                    }
                @endphp

                <div
                    x-data="{
                        odooMenuOpen: false,
                        search: '',
                        appCount: {{ count($appItems) }},
                        appNames: {{ json_encode(array_map(fn ($a) => strtolower($a['label']), $appItems)) }},
                        openMenu() {
                            this.odooMenuOpen = true;
                            this.search = '';
                            document.body.style.overflow = 'hidden';
                            $nextTick(() => {
                                this.$refs.odooSearch?.focus();
                            });
                        },
                        closeMenu() {
                            this.odooMenuOpen = false;
                            this.search = '';
                            document.body.style.overflow = '';
                        },
                        toggleMenu() {
                            if (this.odooMenuOpen) {
                                this.closeMenu();
                            } else {
                                this.openMenu();
                            }
                        },
                        hasMatches() {
                            if (!this.search.trim()) return true;
                            const q = this.search.toLowerCase().trim();
                            return this.appNames.some(name => name.includes(q));
                        },
                        selectFirstMatch() {
                            const firstVisible = document.querySelector('.odoo-app-tile:not([style*=\'display: none\'])');
                            if (firstVisible) {
                                firstVisible.click();
                            }
                        }
                    }"
                    x-on:keydown.escape.window="closeMenu()"
                    class="fi-topbar-odoo-launcher flex items-center"
                >
                    {{-- 9-Dots App Switcher Button --}}
                    <button
                        type="button"
                        x-on:click="toggleMenu()"
                        class="fi-icon-btn relative flex items-center justify-center rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-200 transition"
                        :class="{ 'bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400 ring-2 ring-primary-500/30': odooMenuOpen }"
                        title="{{ __('Aplikasi') }}"
                        aria-label="{{ __('Aplikasi') }}"
                    >
                        <x-filament::icon
                            icon="icon-menu"
                            style="width: 22px; height: 22px;"
                        />
                    </button>

                    {{-- Odoo Fullscreen Home Menu Overlay --}}
                    <template x-teleport="body">
                        <div
                            x-show="odooMenuOpen"
                            x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-[0.98]"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-[0.98]"
                            class="odoo-home-overlay fixed inset-x-0 bottom-0 z-[9999] flex flex-col overflow-y-auto select-none border-t border-slate-200/80 dark:border-white/10 backdrop-blur-md"
                            style="top: 4rem; height: calc(100vh - 4rem); height: calc(100dvh - 4rem);"
                            x-on:click.self="closeMenu()"
                        >
                            {{-- Centered Odoo Search Bar Header --}}
                            <div class="odoo-search-container" x-on:click.self="closeMenu()">
                                <div class="odoo-search-wrapper">
                                    {{-- Search Icon --}}
                                    <div class="odoo-search-icon-box">
                                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                    </div>

                                    {{-- Search Input --}}
                                    <input
                                        type="text"
                                        x-ref="odooSearch"
                                        x-model="search"
                                        x-on:keydown.enter.prevent="selectFirstMatch()"
                                        placeholder="Cari aplikasi..."
                                        class="odoo-search-input"
                                    />

                                    {{-- Right Actions (Clear button & ESC badge) --}}
                                    <div class="odoo-search-right-actions">
                                        <button
                                            type="button"
                                            x-show="search.length > 0"
                                            x-cloak
                                            x-on:click="search = ''; $refs.odooSearch.focus()"
                                            class="odoo-search-clear-btn"
                                            title="{{ __('Hapus') }}"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            x-on:click="closeMenu()"
                                            class="odoo-search-esc-badge"
                                            title="{{ __('Tutup (ESC)') }}"
                                        >
                                            ESC
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <style>
                                /* App Launcher Base Theme (Light Mode) */
                                .odoo-home-overlay {
                                    background: radial-gradient(circle at 50% 25%, #ffffff 0%, #f8fafc 50%, #e2e8f0 100%) !important;
                                }

                                /* App Launcher Dark Mode */
                                :is(.dark .odoo-home-overlay),
                                html.dark .odoo-home-overlay {
                                    background: radial-gradient(circle at 50% 35%, #3c546a 0%, #2b3d4f 55%, #18232e 100%) !important;
                                }

                                /* Centered Search Container */
                                .odoo-search-container {
                                    width: 100%;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    padding: 1.75rem 1rem 0.75rem 1rem;
                                    flex-shrink: 0;
                                }

                                .odoo-search-wrapper {
                                    position: relative;
                                    width: 100%;
                                    max-width: 440px;
                                    display: flex;
                                    align-items: center;
                                }

                                .odoo-search-icon-box {
                                    pointer-events: none;
                                    position: absolute;
                                    left: 14px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: #94a3b8;
                                    z-index: 2;
                                }
                                :is(.dark .odoo-search-icon-box),
                                html.dark .odoo-search-icon-box {
                                    color: rgba(255, 255, 255, 0.6);
                                }

                                .odoo-search-input {
                                    width: 100% !important;
                                    height: 44px !important;
                                    padding-left: 42px !important;
                                    padding-right: 74px !important;
                                    font-size: 0.875rem !important;
                                    line-height: 1.25rem !important;
                                    border-radius: 9999px !important;
                                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                                    outline: none !important;
                                }

                                /* Search Input - Light Mode */
                                .odoo-search-input {
                                    background-color: #ffffff !important;
                                    border: 1.5px solid #cbd5e1 !important;
                                    color: #0f172a !important;
                                    box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(15, 23, 42, 0.04) !important;
                                }
                                .odoo-search-input::placeholder {
                                    color: #94a3b8 !important;
                                }
                                .odoo-search-input:focus {
                                    background-color: #ffffff !important;
                                    border-color: #2563eb !important;
                                    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18), 0 4px 12px -2px rgba(15, 23, 42, 0.1) !important;
                                }

                                /* Search Input - Dark Mode */
                                :is(.dark .odoo-search-input),
                                html.dark .odoo-search-input {
                                    background-color: rgba(15, 23, 42, 0.5) !important;
                                    border: 1.5px solid rgba(255, 255, 255, 0.2) !important;
                                    color: #ffffff !important;
                                    box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.25), 0 4px 12px rgba(0, 0, 0, 0.3) !important;
                                }
                                :is(.dark .odoo-search-input)::placeholder {
                                    color: rgba(255, 255, 255, 0.5) !important;
                                }
                                :is(.dark .odoo-search-input):focus {
                                    border-color: #60a5fa !important;
                                    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.25) !important;
                                }

                                /* Search Input Right Actions */
                                .odoo-search-right-actions {
                                    position: absolute;
                                    right: 8px;
                                    display: flex;
                                    align-items: center;
                                    gap: 6px;
                                    z-index: 2;
                                }

                                .odoo-search-clear-btn {
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 24px;
                                    height: 24px;
                                    border-radius: 9999px;
                                    color: #94a3b8;
                                    background: transparent;
                                    border: none;
                                    cursor: pointer;
                                    padding: 0;
                                    transition: all 0.15s ease;
                                }
                                .odoo-search-clear-btn:hover {
                                    color: #475569;
                                    background-color: #f1f5f9;
                                }
                                :is(.dark .odoo-search-clear-btn),
                                html.dark .odoo-search-clear-btn {
                                    color: rgba(255, 255, 255, 0.5);
                                }
                                :is(.dark .odoo-search-clear-btn):hover {
                                    color: #ffffff;
                                    background-color: rgba(255, 255, 255, 0.1);
                                }

                                .odoo-search-esc-badge {
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 10px;
                                    font-weight: 700;
                                    line-height: 1;
                                    letter-spacing: 0.05em;
                                    padding: 4px 7px;
                                    border-radius: 6px;
                                    cursor: pointer;
                                    user-select: none;
                                    transition: all 0.15s ease;
                                }
                                /* ESC Badge - Light Mode */
                                .odoo-search-esc-badge {
                                    background-color: #f1f5f9;
                                    border: 1px solid #cbd5e1;
                                    color: #64748b;
                                }
                                .odoo-search-esc-badge:hover {
                                    background-color: #e2e8f0;
                                    color: #0f172a;
                                    border-color: #94a3b8;
                                }
                                /* ESC Badge - Dark Mode */
                                :is(.dark .odoo-search-esc-badge),
                                html.dark .odoo-search-esc-badge {
                                    background-color: rgba(255, 255, 255, 0.12);
                                    border: 1px solid rgba(255, 255, 255, 0.25);
                                    color: rgba(255, 255, 255, 0.85);
                                }
                                :is(.dark .odoo-search-esc-badge):hover {
                                    background-color: rgba(255, 255, 255, 0.2);
                                    color: #ffffff;
                                }

                                /* App Icon Squircle - Light Mode */
                                .odoo-app-icon-squircle {
                                    background-color: #ffffff;
                                    border: 1px solid rgba(226, 232, 240, 0.9);
                                    box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.08), 0 2px 4px -2px rgba(15, 23, 42, 0.04);
                                    border-radius: 1rem;
                                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                                }
                                .odoo-app-tile:hover .odoo-app-icon-squircle {
                                    transform: translateY(-4px) scale(1.06);
                                    box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.12), 0 4px 6px -2px rgba(15, 23, 42, 0.05);
                                    border-color: rgba(59, 130, 246, 0.4);
                                }

                                /* App Icon Squircle - Dark Mode */
                                :is(.dark .odoo-app-icon-squircle),
                                html.dark .odoo-app-icon-squircle {
                                    background-color: rgba(255, 255, 255, 0.06);
                                    border: 1px solid rgba(255, 255, 255, 0.12);
                                    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35);
                                }
                                :is(.dark .odoo-app-tile:hover .odoo-app-icon-squircle),
                                html.dark .odoo-app-tile:hover .odoo-app-icon-squircle {
                                    border-color: rgba(255, 255, 255, 0.35);
                                    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.5);
                                }

                                /* App Title Label - Light Mode */
                                .odoo-app-label {
                                    color: #1e293b !important;
                                    font-weight: 600 !important;
                                    text-shadow: none !important;
                                    transition: color 0.15s ease;
                                }
                                .odoo-app-tile:hover .odoo-app-label {
                                    color: #2563eb !important;
                                }

                                /* App Title Label - Dark Mode */
                                :is(.dark .odoo-app-label),
                                html.dark .odoo-app-label {
                                    color: rgba(255, 255, 255, 0.95) !important;
                                    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6) !important;
                                }
                                :is(.dark .odoo-app-tile:hover .odoo-app-label),
                                html.dark .odoo-app-tile:hover .odoo-app-label {
                                    color: #60a5fa !important;
                                }

                                .odoo-app-grid {
                                    display: grid;
                                    grid-template-columns: repeat(5, 110px);
                                    gap: 28px 24px;
                                    justify-content: center;
                                    justify-items: center;
                                    width: 100%;
                                    max-width: 680px;
                                    margin-left: auto;
                                    margin-right: auto;
                                }
                                @media (max-width: 680px) {
                                    .odoo-app-grid {
                                        grid-template-columns: repeat(3, 100px);
                                        gap: 20px 16px;
                                        max-width: 360px;
                                    }
                                }
                                @media (max-width: 360px) {
                                    .odoo-app-grid {
                                        grid-template-columns: repeat(2, 96px);
                                        gap: 16px 12px;
                                        max-width: 240px;
                                    }
                                }
                            </style>

                            {{-- Main App Icons Grid Container --}}
                            <div
                                class="flex-1 flex flex-col items-center justify-center px-4 py-8"
                                x-on:click.self="closeMenu()"
                            >
                                <div class="odoo-app-grid">
                                    @foreach ($appItems as $app)
                                        <div
                                            x-show="!search.trim() || '{{ strtolower(addslashes($app['label'])) }}'.includes(search.toLowerCase().trim())"
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 scale-90"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="flex flex-col items-center"
                                        >
                                            <a
                                                href="{{ $app['url'] }}"
                                                x-on:click="closeMenu()"
                                                class="odoo-app-tile group flex flex-col items-center justify-start text-center rounded-2xl p-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500/40"
                                                style="width: 104px;"
                                            >
                                                {{-- App Icon Squircle --}}
                                                <div
                                                    class="odoo-app-icon-squircle relative flex items-center justify-center overflow-hidden"
                                                    style="width: 64px; height: 64px;"
                                                >
                                                    <x-filament::icon
                                                        :icon="$app['icon']"
                                                        style="width: 64px; height: 64px; display: block;"
                                                    />

                                                    @if ($app['isActive'])
                                                        <span
                                                            class="absolute top-1 right-1 flex h-2.5 w-2.5"
                                                            title="{{ __('Sedang Aktif') }}"
                                                        >
                                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500 border border-white"></span>
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- App Title --}}
                                                <span class="odoo-app-label mt-2.5 text-xs sm:text-sm text-center leading-tight tracking-wide line-clamp-2">
                                                    {{ $app['label'] }}
                                                </span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Empty Search State --}}
                                <div
                                    x-cloak
                                    x-show="!hasMatches()"
                                    class="text-center py-16"
                                >
                                    <svg class="mx-auto h-12 w-12 text-slate-400 dark:text-white/40 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                    <p class="text-base font-medium text-slate-700 dark:text-white/90">Tidak ada aplikasi yang cocok dengan "<span x-text="search" class="font-semibold text-primary-600 dark:text-white"></span>"</p>
                                    <p class="text-xs text-slate-500 dark:text-white/50 mt-1.5">Tekan Backspace untuk menghapus atau cari kata kunci lain</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @endif
        @endif

        @if (! $isAdminPanel)
            <div class="fi-topbar-start" style="margin-right: 0">
                @if ($isSidebarCollapsibleOnDesktop)
                    <x-filament::icon-button
                        color="gray"
                        :icon="$isRtl ? \Filament\Support\Icons\Heroicon::OutlinedChevronLeft : \Filament\Support\Icons\Heroicon::OutlinedChevronRight"
                        :icon-alias="
                            $isRtl
                                ? [
                                    \Filament\View\PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL,
                                    \Filament\View\PanelsIconAlias::SIDEBAR_EXPAND_BUTTON,
                                ]
                                : \Filament\View\PanelsIconAlias::SIDEBAR_EXPAND_BUTTON
                        "
                        icon-size="lg"
                        :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                        x-cloak
                        x-data="{}"
                        x-on:click="$store.sidebar.open()"
                        x-show="! $store.sidebar.isOpen"
                        class="fi-topbar-open-collapse-sidebar-btn"
                    />
                @endif

                @if ($isSidebarCollapsibleOnDesktop || $isSidebarFullyCollapsibleOnDesktop)
                    <x-filament::icon-button
                        color="gray"
                        :icon="$isRtl ? \Filament\Support\Icons\Heroicon::OutlinedChevronRight : \Filament\Support\Icons\Heroicon::OutlinedChevronLeft"
                        :icon-alias="
                            $isRtl
                                ? [
                                    \Filament\View\PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON_RTL,
                                    \Filament\View\PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON,
                                ]
                                : \Filament\View\PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON
                        "
                        icon-size="lg"
                        :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                        x-cloak
                        x-data="{}"
                        x-on:click="$store.sidebar.close()"
                        x-show="$store.sidebar.isOpen"
                        class="fi-topbar-close-collapse-sidebar-btn"
                    />
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_LOGO_BEFORE) }}

                @if ($homeUrl = filament()->getHomeUrl())
                    <a {{ \Filament\Support\generate_href_html($homeUrl) }}>
                        <x-filament-panels::logo />
                    </a>
                @else
                    <x-filament-panels::logo />
                @endif

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_LOGO_AFTER) }}
            </div>
        @endif

        @if ($hasTopNavigation || (! $hasNavigation))
            @if ($hasTenancy && filament()->hasTenantMenu())
                <x-filament-panels::tenant-menu />
            @endif

            @if ($hasNavigation)
                <ul class="fi-topbar-nav-groups" style="flex-wrap: nowrap !important; align-items: center !important;">
                    @foreach ($navigation as $group)
                        @php
                            $groupLabel = $group->getLabel();
                            $groupExtraTopbarAttributeBag = $group->getExtraTopbarAttributeBag();
                            $isGroupActive = $group->isActive();
                            $groupIcon = $group->getIcon();

                            if ($isAdminPanel && ! $isGroupActive) {
                                continue;
                            }
                        @endphp

                        @if ($groupLabel)
                            @if ($isAdminPanel)
                                {{-- Admin panel: show active group name as a plain bold heading --}}
                                <li class="fi-topbar-item">
                                    <span class="px-3 py-2 text-xl font-bold">
                                        <a {{ \Filament\Support\generate_href_html($group->getItems()->first()->getUrl()) }}>
                                            {{ $groupLabel }}
                                        </a>
                                    </span>
                                </li>

                                @foreach ($group->getItems() as $item)
                                    @php
                                        $isItemActive = $item->isActive();
                                        $itemActiveIcon = $item->getActiveIcon();
                                        $itemBadge = $item->getBadge();
                                        $itemBadgeColor = $item->getBadgeColor();
                                        $itemBadgeTooltip = $item->getBadgeTooltip();
                                        $itemIcon = $item->getIcon();
                                        $shouldItemOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                        $itemUrl = $item->getUrl();
                                    @endphp

                                    <x-filament-panels::topbar.item
                                        :active="$isItemActive"
                                        :active-icon="$itemActiveIcon"
                                        :badge="$itemBadge"
                                        :badge-color="$itemBadgeColor"
                                        :badge-tooltip="$itemBadgeTooltip"
                                        :icon="$itemIcon"
                                        :should-open-url-in-new-tab="$shouldItemOpenUrlInNewTab"
                                        :url="$itemUrl"
                                    >
                                        {{ $item->getLabel() }}
                                    </x-filament-panels::topbar.item>
                                @endforeach
                            @else
                                <x-filament::dropdown
                                    placement="bottom-start"
                                    teleport
                                    :attributes="\Filament\Support\prepare_inherited_attributes($groupExtraTopbarAttributeBag)"
                                >
                                    <x-slot name="trigger">
                                        <x-filament-panels::topbar.item
                                            :active="$isGroupActive"
                                            :icon="$groupIcon"
                                        >
                                            {{ $groupLabel }}
                                        </x-filament-panels::topbar.item>
                                    </x-slot>

                                    @php
                                        $lists = [];

                                        foreach ($group->getItems() as $item) {
                                            if ($childItems = $item->getChildItems()) {
                                                $lists[] = [$item, ...$childItems];
                                                $lists[] = [];

                                                continue;
                                            }

                                            if (empty($lists)) {
                                                $lists[] = [$item];

                                                continue;
                                            }

                                            $lists[count($lists) - 1][] = $item;
                                        }

                                        if (! empty($lists) && empty($lists[count($lists) - 1])) {
                                            array_pop($lists);
                                        }
                                    @endphp

                                    @foreach ($lists as $list)
                                        <x-filament::dropdown.list>
                                            @foreach ($list as $item)
                                                @php
                                                    $isItemActive = $item->isActive();
                                                    $itemBadge = $item->getBadge();
                                                    $itemBadgeColor = $item->getBadgeColor();
                                                    $itemBadgeTooltip = $item->getBadgeTooltip();
                                                    $itemUrl = $item->getUrl();
                                                    $itemIcon = $isItemActive
                                                        ? ($item->getActiveIcon() ?? $item->getIcon())
                                                        : $item->getIcon();
                                                    $shouldItemOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                                @endphp

                                                <x-filament::dropdown.list.item
                                                    :badge="$itemBadge"
                                                    :badge-color="$itemBadgeColor"
                                                    :badge-tooltip="$itemBadgeTooltip"
                                                    :color="$isItemActive ? 'primary' : 'gray'"
                                                    :href="$itemUrl"
                                                    :icon="$itemIcon"
                                                    tag="a"
                                                    :target="$shouldItemOpenUrlInNewTab ? '_blank' : null"
                                                >
                                                    {{ $item->getLabel() }}
                                                </x-filament::dropdown.list.item>
                                            @endforeach
                                        </x-filament::dropdown.list>
                                    @endforeach
                                </x-filament::dropdown>
                            @endif
                        @else
                            @foreach ($group->getItems() as $item)
                                @php
                                    $isItemActive = $item->isActive();
                                    $itemActiveIcon = $item->getActiveIcon();
                                    $itemBadge = $item->getBadge();
                                    $itemBadgeColor = $item->getBadgeColor();
                                    $itemBadgeTooltip = $item->getBadgeTooltip();
                                    $itemIcon = $item->getIcon();
                                    $shouldItemOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                    $itemUrl = $item->getUrl();
                                @endphp

                                <x-filament-panels::topbar.item
                                    :active="$isItemActive"
                                    :active-icon="$itemActiveIcon"
                                    :badge="$itemBadge"
                                    :badge-color="$itemBadgeColor"
                                    :badge-tooltip="$itemBadgeTooltip"
                                    :icon="$itemIcon"
                                    :should-open-url-in-new-tab="$shouldItemOpenUrlInNewTab"
                                    :url="$itemUrl"
                                >
                                    {{ $item->getLabel() }}
                                </x-filament-panels::topbar.item>
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            @endif
        @endif

        <div
            @if ($hasTenancy)
                x-persist="topbar.end.panel-{{ filament()->getId() }}.tenant-{{ filament()->getTenant()?->getKey() }}"
            @else
                x-persist="topbar.end.panel-{{ filament()->getId() }}"
            @endif
            class="fi-topbar-end"
        >
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE) }}

            @if (filament()->isGlobalSearchEnabled())
                @livewire(Filament\Livewire\GlobalSearch::class)
            @endif

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_AFTER) }}

            @if (filament()->auth()->check())
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                    ])
                @endif

                @if (filament()->hasUserMenu())
                    <x-filament-panels::user-menu />
                @endif
            @endif
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_END) }}
    </nav>

    <x-filament-actions::modals />
</div>
