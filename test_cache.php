<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$vtp = app(App\Services\ViettelPostService::class);
$provs = $vtp->getProvinces();
foreach($provs as $p) {
    if (mb_stripos($p['PROVINCE_NAME'], 'Hồ Chí Minh') !== false) {
        echo "HCM ID in Cache: " . $p['PROVINCE_ID'] . "\n";
    }
}
