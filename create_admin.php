<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::query()->firstOrCreate(
    ['email' => 'admin@myrecipes.test'],
    [
        'name' => 'Admin',
        'password' => Illuminate\Support\Facades\Hash::make('admin123'),
        'is_admin' => true,
        'is_blocked' => false,
        'email_verified_at' => now(),
    ]
);

$user->is_admin = true;
$user->is_blocked = false;
$user->save();

echo $user->email . '|' . ($user->is_admin ? 'admin' : 'user') . '|' . ($user->is_blocked ? 'blocked' : 'active') . PHP_EOL;
