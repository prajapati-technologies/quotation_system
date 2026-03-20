<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$methods = get_class_methods(new \Filament\Schemas\Schema());
print_r($methods);
