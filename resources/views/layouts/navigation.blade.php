{{-- filepath: /c:/laragon/www/TaskManager/resources/views/layouts/navigation.blade.php --}}
<nav x-data="{ open: false }" class="bg-surface-container-lowest border-b border-surface-container-low sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:flex">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center gap-2 px-3 py-2 text-label-md font-label-md transition-colors rounded-lg text-primary font-semibold bg-primary/10">
                        <span class="material-symbols-outlined text-[20px]">task</span>
                        <span>{{ __('Task Manager') }}</span>
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 text-label-md font-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low rounded-lg transition duration-150 ease-in-out focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary font-bold flex items-center justify-center text-xs shadow-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-on-surface">{{ Auth::user()->name }}</span>
                            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">expand_more</span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-surface-container-low">
                            <p class="text-xs font-semibold text-on-surface">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-on-surface-variant truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2 text-on-surface-variant hover:bg-surface-container-low">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                            <span>{{ __('Profile') }}</span>
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center gap-2 text-error hover:bg-error-container/20">
                                <span class="material-symbols-outlined text-[18px]">logout</span>
                                <span>{{ __('Log Out') }}</span>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low focus:outline-none transition duration-150 ease-in-out">
                    <span class="material-symbols-outlined text-[24px]" x-show="!open">menu</span>
                    <span class="material-symbols-outlined text-[24px]" x-show="open" x-cloak>close</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-surface-container-low bg-surface-container-lowest">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-label-md font-label-md transition-colors {{ request()->routeIs('dashboard') ? 'text-primary font-semibold bg-primary/10' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low' }}">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                <span>{{ __('Dashboard') }}</span>
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-3 border-t border-surface-container-low px-4">
            <div class="flex items-center gap-3 px-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary font-bold flex items-center justify-center text-sm shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="font-semibold text-body-md text-on-surface">{{ Auth::user()->name }}</div>
                    <div class="font-normal text-body-sm text-on-surface-variant">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center gap-3 px-3 py-2 rounded-lg text-label-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-[20px]">person</span>
                    <span>{{ __('Profile') }}</span>
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}"
                       onclick="event.preventDefault(); this.closest('form').submit();"
                       class="flex items-center gap-3 px-3 py-2 rounded-lg text-label-md text-error hover:bg-error-container/20 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">logout</span>
                        <span>{{ __('Log Out') }}</span>
                    </a>
                </form>
            </div>
        </div>
    </div>
</nav>