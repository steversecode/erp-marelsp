<x-filament-panels::page class="fi-dashboard-page">
    <div 
        x-data="{ 
            search: '',
            init() {
                this.$refs.searchInput?.focus();
            }
        }" 
        class="-m-6 min-h-[calc(100vh-4.5rem)] bg-gradient-to-b from-[#2e475d] via-[#1e3345] to-[#111f2c] px-6 py-8 flex flex-col items-center select-none"
    >
        {{-- Search Input --}}
        <div class="w-full max-w-md mb-8">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input
                    x-ref="searchInput"
                    x-model="search"
                    type="text"
                    placeholder="Search apps..."
                    class="block w-full rounded-2xl border-0 bg-white/10 py-3 pl-11 pr-4 text-white placeholder-gray-300 shadow-xl backdrop-blur-md ring-1 ring-inset ring-white/20 focus:bg-white/20 focus:ring-2 focus:ring-inset focus:ring-primary-400 sm:text-sm sm:leading-6 transition"
                />
                <button
                    x-show="search.length > 0"
                    @click="search = ''; $refs.searchInput.focus()"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-300 hover:text-white"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Apps Grid --}}
        <div 
            class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-6 sm:gap-8 md:gap-10 max-w-5xl w-full justify-items-center"
        >
            @foreach ($this->apps as $app)
                <a
                    href="{{ $app['url'] }}"
                    x-show="!search || '{{ strtolower($app['label']) }}'.includes(search.toLowerCase())"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="group flex flex-col items-center justify-center p-2 rounded-2xl focus:outline-none transition-transform duration-200 hover:-translate-y-1.5 cursor-pointer"
                >
                    {{-- App Icon Card --}}
                    <div class="flex h-20 w-20 sm:h-24 sm:w-24 items-center justify-center rounded-2xl bg-white shadow-xl ring-1 ring-black/5 transition-all duration-300 group-hover:scale-105 group-hover:shadow-2xl group-hover:ring-2 group-hover:ring-primary-400/80 p-3">
                        <x-filament::icon
                            :icon="$app['icon']"
                            class="h-14 w-14 sm:h-16 sm:w-16 transition duration-200"
                            style="height: 56px; width: 56px;"
                        />
                    </div>

                    {{-- App Label --}}
                    <span class="mt-2.5 text-xs sm:text-sm font-semibold text-white/95 group-hover:text-white text-center leading-tight tracking-wide drop-shadow max-w-[100px] line-clamp-2">
                        {{ $app['label'] }}
                    </span>
                </a>
            @endforeach
        </div>

        {{-- Empty Search State --}}
        <div 
            x-cloak
            x-show="search.length > 0 && $el.closest('div').querySelectorAll('a:not([style*=\'display: none\'])').length === 0"
            class="mt-12 text-center text-gray-300"
        >
            <p class="text-lg font-medium">No apps found matching "<span x-text="search"></span>"</p>
            <p class="text-sm text-gray-400 mt-1">Try searching with a different term</p>
        </div>
    </div>
</x-filament-panels::page>
