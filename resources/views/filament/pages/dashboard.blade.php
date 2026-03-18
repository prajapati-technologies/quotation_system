<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Welcome Message --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-content p-6">
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Welcome back, {{ auth()->user()->name }}! 👋
                        </h2>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            @if(auth()->user()->role === 'admin')
                                Here's an overview of your business performance.
                            @else
                                Here's a summary of your sales activities.
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ now()->format('g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Widgets --}}
        <x-filament-widgets::widgets
            :widgets="$this->getWidgets()"
            :columns="$this->getColumns()"
        />
    </div>
</x-filament-panels::page>
