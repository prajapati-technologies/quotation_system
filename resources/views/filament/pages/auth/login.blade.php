<x-filament-panels::page.simple>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white font-outfit">
            VistaConfig Admin
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 font-outfit">
            Sign in to your account
        </p>
    </div>

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions :actions="$this->getCachedFormActions()"
            :full-width="$this->hasFullWidthFormActions()" />
    </x-filament-panels::form>

    <div class="mt-8 text-center">
        <p class="text-xs text-gray-500 dark:text-gray-500 font-outfit">
            &copy; {{ date('Y') }} MODA Windows & Doors. All rights reserved.
        </p>
    </div>
</x-filament-panels::page.simple>