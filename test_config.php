<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$senderProvince = (int) config('services.viettelpost.sender_province', 5);
$senderDistrict = (int) config('services.viettelpost.sender_district', 3591);

echo "Config province: $senderProvince, district: $senderDistrict\n";
