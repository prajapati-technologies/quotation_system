<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-milestones', function() {
    return \App\Models\QuotationMilestone::all();
});
