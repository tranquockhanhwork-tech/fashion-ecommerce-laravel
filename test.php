<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$noVariants = \App\Models\Product::doesntHave('variants')->get();
echo "Products without variants: " . $noVariants->count() . "\n";
foreach($noVariants as $p) {
    echo $p->id . " - " . $p->name . "\n";
}
