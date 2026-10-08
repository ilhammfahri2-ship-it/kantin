<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (\App\Models\Product::orderBy('id')->get() as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | Image: {$p->image} | Slug: {$p->slug}\n";
}
