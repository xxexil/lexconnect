<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$lawyers = \App\Models\LawyerProfile::all();
foreach ($lawyers as $lp) {
    echo "ID: " . $lp->user_id . " | Name: " . $lp->user->name . " | Avail: " . $lp->availability_status . " | GCash: " . ($lp->gcash_number ?: 'NONE') . PHP_EOL;
}
