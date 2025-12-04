<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = Illuminate\Support\Facades\DB::table('users')->select('role')->distinct()->pluck('role')->toArray();
echo json_encode($rows, JSON_PRETTY_PRINT) . PHP_EOL;
