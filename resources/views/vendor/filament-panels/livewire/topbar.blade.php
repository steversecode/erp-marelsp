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
                            class="odoo-home-overlay fixed inset-0 z-[99999] flex flex-col overflow-y-auto select-none"
                            style="background: radial-gradient(circle at 50% 35%, #3c546a 0%, #2b3d4f 55%, #18232e 100%); min-height: 100vh; min-height: 100dvh;"
                            x-on:click.self="closeMenu()"
                        >
                            {{-- Top Action Bar --}}
                            <div class="w-full flex items-center justify-between px-6 py-4 sm:px-10 z-10" x-on:click.self="closeMenu()">
                                {{-- Left Brand/Close Toggle --}}
                                <div class="flex items-center gap-3">
                                    <button
                                        type="button"
                                        x-on:click="closeMenu()"
                                        class="flex items-center justify-center p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition focus:outline-none"
                                        title="{{ __('Tutup Menu') }}"
                                    >
                                        <x-filament::icon
                                            icon="icon-menu"
                                            style="width: 20px; height: 20px; filter: brightness(0) invert(1);"
                                        />
                                    </button>
                                </div>

                                {{-- Centered Odoo Search Input --}}
                                <div class="relative w-full max-w-sm sm:max-w-md mx-4">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                        <svg class="h-4 w-4 text-white/60" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                    </div>
                                    <input
                                        type="text"
                                        x-ref="odooSearch"
                                        x-model="search"
                                        x-on:keydown.enter.prevent="selectFirstMatch()"
                                        placeholder="Cari aplikasi..."
                                        class="w-full rounded-full border border-white/25 bg-black/20 hover:bg-black/30 focus:bg-black/40 py-2.5 pl-10 pr-9 text-sm text-white placeholder-white/50 backdrop-blur-md transition-all duration-200 focus:border-white/50 focus:outline-none focus:ring-2 focus:ring-white/20 shadow-inner"
                                    />
                                    <button
                                        type="button"
                                        x-show="search.length > 0"
                                        x-on:click="search = ''; $refs.odooSearch.focus()"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-white/60 hover:text-white"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Right Close Button --}}
                                <div>
                                    <button
                                        type="button"
                                        x-on:click="closeMenu()"
                                        class="flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-white/80 hover:bg-white/10 hover:text-white transition"
                                    >
                                        <span class="hidden sm:inline">Tutup</span>
                                        <kbd class="rounded border border-white/30 bg-white/15 px-1.5 py-0.5 text-[10px] text-white">ESC</kbd>
                                    </button>
                                </div>
                            </div>

                            <style>
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
                                class="flex-1 flex flex-col items-center justify-center px-4 py-6"
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
                                                class="odoo-app-tile group flex flex-col items-center justify-start text-center rounded-2xl p-2 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white/40"
                                                style="width: 104px;"
                                            >
                                                {{-- App Icon Squircle --}}
                                                <div
                                                    class="relative flex items-center justify-center transition-all duration-200 group-hover:scale-110 group-hover:-translate-y-1 group-active:scale-95 shadow-lg shadow-black/30 group-hover:shadow-2xl group-hover:shadow-white/20 rounded-2xl overflow-hidden"
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
                                                <span
                                                    class="mt-2 text-xs sm:text-sm font-medium text-white/95 text-center leading-tight tracking-wide drop-shadow-md group-hover:text-white transition-colors line-clamp-2"
                                                >
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
                                    class="text-center py-16 text-white/70"
                                >
                                    <svg class="mx-auto h-12 w-12 text-white/40 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                    <p class="text-base text-white/90">Tidak ada aplikasi yang cocok dengan "<span x-text="search" class="font-semibold text-white"></span>"</p>
                                    <p class="text-xs text-white/50 mt-1.5">Tekan Backspace untuk menghapus atau cari kata kunci lain</p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            @endif
        @endif

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

        @if ($hasTopNavigation || (! $hasNavigation))
            @if ($hasTenancy && filament()->hasTenantMenu())
                <x-filament-panels::tenant-menu />
            @endif

            @if ($hasNavigation)
                <ul class="fi-topbar-nav-groups">
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
