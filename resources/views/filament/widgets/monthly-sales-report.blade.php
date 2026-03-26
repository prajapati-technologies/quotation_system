<x-filament-widgets::widget>
    <x-filament::card>
        <div class="flex items-center gap-2 mb-4 border-b pb-2">
            <x-filament::icon
                icon="heroicon-o-chart-bar"
                class="w-5 h-5 text-primary-500"
            />
            <h3 class="text-lg font-bold">Monthly Sales Performance</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 uppercase text-xs font-semibold text-gray-400">
                        <th class="px-4 py-3 border-b dark:border-gray-700">Financial Month</th>
                        <th class="px-4 py-3 border-b dark:border-gray-700 text-right">Revenue (THB)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:border-gray-800">
                    @forelse($this->getData() as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-4 font-bold text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($row->month . '-01')->format('F Y') }}
                            </td>
                            <td class="px-4 py-4 text-right font-mono font-black text-emerald-600 italic">
                                ฿{{ number_format($row->total_revenue, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-400 italic font-medium">No sales records found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
