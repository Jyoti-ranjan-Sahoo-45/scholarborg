<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$users = DB::table('users')
    ->join('roles', 'users.role_id', '=', 'roles.id')
    ->select('users.id', 'users.email', 'users.full_name', 'roles.name as role_name')
    ->limit(30)
    ->get();

echo "User list:\n";
foreach ($users as $user) {
    echo "ID: {$user->id} | Email: {$user->email} | Name: {$user->full_name} | Role: {$user->role_name}\n";
}
