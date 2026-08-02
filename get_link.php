<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('role', 'hr_manager')->orderBy('created_at', 'desc')->first();
if ($user) {
    $token = Illuminate\Support\Facades\Password::broker()->createToken($user);
    echo 'http://127.0.0.1:8000/reset-password/' . $token . '?email=' . urlencode($user->email) . "\n";
} else {
    echo "User not found\n";
}
