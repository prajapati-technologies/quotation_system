<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Quotation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        // URLs
        $customerUrl = \App\Filament\Resources\Customers\CustomerResource::getUrl();
        $projectUrl = \App\Filament\Resources\Projects\ProjectResource::getUrl();
        $quotationUrl = \App\Filament\Resources\Quotations\QuotationResource::getUrl();

        // Calculate stats based on user role
        if ($isAdmin) {
            $totalCustomers = Customer::count();
            $totalProjects = Project::count();
            $totalQuotations = Quotation::count();
            $totalRevenue = Quotation::whereIn('status', ['Approved', 'Production', 'Completed'])->sum('final_price');
            $pendingQuotations = Quotation::whereIn('status', ['Draft', 'Signed'])->count();
            $completedProjects = Quotation::where('status', 'Completed')->count();

            // Admin pending filter: Draft + Signed
            $pendingUrl = $quotationUrl . '?tableFilters[status][values][0]=Draft&tableFilters[status][values][1]=Signed';
        } else {
            // Sales user - only their data
            $totalCustomers = Customer::where('user_id', $user->id)->count();
            $totalProjects = Project::whereHas('customer', fn($q) => $q->where('user_id', $user->id))->count();
            $totalQuotations = Quotation::whereHas('project.customer', fn($q) => $q->where('user_id', $user->id))->count();
            $totalRevenue = Quotation::whereHas('project.customer', fn($q) => $q->where('user_id', $user->id))
                ->whereIn('status', ['Approved', 'Production', 'Completed'])
                ->sum('final_price');
            $pendingQuotations = Quotation::whereHas('project.customer', fn($q) => $q->where('user_id', $user->id))
                ->where('status', 'Draft')
                ->count();
            $completedProjects = Quotation::whereHas('project.customer', fn($q) => $q->where('user_id', $user->id))
                ->where('status', 'Completed')
                ->count();

            // Sales pending filter: Draft only
            $pendingUrl = $quotationUrl . '?tableFilters[status][values][0]=Draft';
        }

        // Common filtered URLs
        $revenueUrl = $quotationUrl . '?tableFilters[status][values][0]=Approved&tableFilters[status][values][1]=Production&tableFilters[status][values][2]=Completed';
        $completedUrl = $quotationUrl . '?tableFilters[status][values][0]=Completed';

        return [
            Stat::make('Total Customers', $totalCustomers)
                ->description($isAdmin ? 'All registered customers' : 'Your customers')
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 25, $totalCustomers])
                ->url($customerUrl),

            Stat::make('Total Projects', $totalProjects)
                ->description($isAdmin ? 'All projects' : 'Your projects')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('info')
                ->chart([5, 8, 12, 15, 18, 20, $totalProjects])
                ->url($projectUrl),

            Stat::make('Total Quotations', $totalQuotations)
                ->description($isAdmin ? 'All quotations' : 'Your quotations')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('warning')
                ->chart([3, 6, 9, 12, 15, 18, $totalQuotations])
                ->url($quotationUrl),

            Stat::make('Total Revenue', '฿' . number_format($totalRevenue, 2))
                ->description('Approved & completed')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->chart([1000, 2500, 3500, 5000, 7500, 10000, $totalRevenue])
                ->url($revenueUrl),

            Stat::make($isAdmin ? 'Pending Approvals' : 'Draft Quotations', $pendingQuotations)
                ->description($isAdmin ? 'Awaiting your approval' : 'Not yet signed')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger')
                ->url($pendingUrl),

            Stat::make('Completed', $completedProjects)
                ->description('Finished projects')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->url($completedUrl),
        ];
    }
}
