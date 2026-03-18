<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 p-8 shadow-2xl">
        <div class="relative z-10">
            <h2 class="text-3xl font-bold tracking-tight text-white">
                Welcome back, {{ auth()->user()->name }}!
            </h2>
            <p class="mt-2 text-indigo-100 text-lg">
                Here's what's happening with your quotations today.
            </p>

            <div class="mt-6 flex gap-4">
                <a href="{{ \App\Filament\Resources\Quotations\QuotationResource::getUrl('create') }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/20 backdrop-blur-sm border border-white/20">

                    New Quotation
                </a>
                <a href="{{ \App\Filament\Resources\Quotations\QuotationResource::getUrl('index') }}"
                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">
                    View All Quotations
                </a>
            </div>
        </div>

        <!-- Decorative Shapes -->
        <div class="absolute -right-12 -top-12 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-12 right-12 h-40 w-40 rounded-full bg-indigo-400/20 blur-3xl"></div>
    </div>
</x-filament-widgets::widget>