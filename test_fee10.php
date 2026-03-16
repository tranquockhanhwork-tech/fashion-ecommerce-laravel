<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$vtp = app(App\Services\ViettelPostService::class);
$fees = $vtp->calculateFee(5, 91, 1, 1);
var_dump($fees);
