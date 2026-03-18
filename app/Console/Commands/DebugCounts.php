<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Quotation;

class DebugCounts extends Command
{
    protected $signature = 'debug:counts';
    protected $description = 'Debug database counts';

    public function handle()
    {
        $this->info("--- Debugging Database Counts ---");
        $this->info("Total Customers: " . Customer::count());
        $this->info("Total Projects: " . Project::count());
        $this->info("Total Quotations: " . Quotation::count());

        $statuses = Quotation::select('status')->distinct()->pluck('status');
        $this->info("\nAvailable Statuses: " . $statuses->implode(', '));

        $revenue = Quotation::whereIn('status', ['Approved', 'Production', 'Completed'])->sum('final_price');
        $this->info("Calculated Revenue (Approved, Production, Completed): " . $revenue);

        $pending = Quotation::where('status', 'Signed')->count();
        $this->info("Pending (Signed): " . $pending);

        $completed = Quotation::where('status', 'Completed')->count();
        $this->info("Completed count: " . $completed);
    }
}
