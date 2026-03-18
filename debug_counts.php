<?php

use App\Models\Customer;
use App\Models\Project;
use App\Models\Quotation;

echo "--- Debugging Database Counts ---\n";

echo "Total Customers: " . Customer::count() . "\n";
echo "Total Projects: " . Project::count() . "\n";
echo "Total Quotations: " . Quotation::count() . "\n";

echo "\n--- Quotation Statuses ---\n";
$statuses = Quotation::select('status')->distinct()->pluck('status');
echo "Available Statuses: " . $statuses->implode(', ') . "\n";

echo "\n--- Admin Revenue Calculation ---\n";
$revenue = Quotation::whereIn('status', ['Approved', 'Production', 'Completed'])->sum('final_price');
echo "Calculated Revenue (Approved, Production, Completed): " . $revenue . "\n";

echo "\n--- Admin Pending Calculation ---\n";
$pending = Quotation::where('status', 'Signed')->count();
echo "Pending (Signed): " . $pending . "\n";

echo "\n--- Admin Completed Calculation ---\n";
$completed = Quotation::where('status', 'Completed')->count();
echo "Completed count: " . $completed . "\n";
