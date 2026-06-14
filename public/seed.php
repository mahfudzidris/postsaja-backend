<?php
// Temporary seeder - hit this URL to run seed on production
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
echo "Exit code: $status\n";
echo $kernel->output();
