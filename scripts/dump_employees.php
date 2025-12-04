<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;

$employees = Employee::with('user')->take(20)->get();
if ($employees->isEmpty()) {
    echo "No employees found.\n";
    exit(0);
}

foreach ($employees as $e) {
    $userEmail = $e->user?->email ?? 'NULL';
    echo sprintf("EMP:%s | emp_email:%s | user_id:%s | user_email:%s\n", $e->id, $e->email, $e->user_id, $userEmail);
}
