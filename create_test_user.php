<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'lycamarieantoque@gmail.com';
if (!User::where('email', $email)->exists()) {
    $user = User::create([
        'name' => 'Lyca Marie Antoque',
        'email' => $email,
        'password' => Hash::make('password'),
        'role' => 'client'
    ]);
    echo "User created with ID: " . $user->id;
} else {
    echo "User already exists.";
}
