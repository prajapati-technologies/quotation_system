<?php

use Illuminate\Support\Facades\Schema;

$tables = ['glasses', 'glass_films', 'accessories'];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "Table '$table' exists.\n";
        $columns = Schema::getColumnListing($table);
        echo "Columns: " . implode(', ', $columns) . "\n";
    } else {
        echo "Table '$table' DOES NOT exist.\n";
    }
    echo "----------------\n";
}
