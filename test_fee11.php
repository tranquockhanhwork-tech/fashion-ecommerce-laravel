<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$token = app(App\Services\ViettelPostService::class)->getToken();
$res = \Illuminate\Support\Facades\Http::withHeaders(['Token' => $token])
    ->post("https://partner.viettelpost.vn/v2/order/getPriceAll", [
        'SENDER_PROVINCE'   => 5,
        'SENDER_DISTRICT'   => 91,
        'RECEIVER_PROVINCE' => 1,
        'RECEIVER_DISTRICT' => 1,
        'PRODUCT_TYPE'      => 'HH',   // Hàng hóa
        'PRODUCT_WEIGHT'    => 500,
        'PRODUCT_PRICE'     => 0,
        'MONEY_COLLECTION'  => 0,
        'ORDER_SERVICE_ADD' => '',
        'ORDER_SERVICE'     => ''
    ]);

var_dump($res->json());
var_dump($res->json('data'));
var_dump($res->json('error'));
var_dump($res->json('status'));
