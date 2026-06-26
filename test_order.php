<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$o = App\Models\Order::latest()->first();
if ($o) {
    file_put_contents('out_order_php.json', json_encode($o->toArray(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
} else {
    file_put_contents('out_order_php.json', "No orders.\n");
}
