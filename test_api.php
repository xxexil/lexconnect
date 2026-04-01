<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$id = 3;
$profile = \App\Models\LawyerProfile::where('user_id', $id)->first();
if (!$profile) {
    echo "Profile not found for ID $id\n";
    exit;
}

$response = [
    'gcash_number' => $profile->gcash_number,
    'gcash_qr'     => $profile->gcash_qr,
    'gcash_qr_url' => $profile->gcash_qr ? asset('storage/'.$profile->gcash_qr) : null,
];

echo json_encode($response, JSON_PRETTY_PRINT);
