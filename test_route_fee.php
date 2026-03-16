<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Directly call the method
$req = new \Illuminate\Http\Request([
    'receiver_province_id' => 1,
    'receiver_district_id' => 1,
    'weight' => 500,
    'price' => 0
]);
$controller = app(\App\Http\Controllers\ShippingController::class);
$res = $controller->calculateFee($req);

echo json_encode($res->getData(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) . "\n";
