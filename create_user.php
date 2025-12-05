<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Role;

function createUserWithRole($name, $email, $password, $roleName = null) {
    $user = User::firstOrCreate([
        'email' => $email,
    ], [
        'name' => $name,
        'password' => bcrypt($password),
    ]);

    if ($roleName) {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            if (! $user->roles()->where('role_id', $role->id)->exists()) {
                $user->roles()->attach($role->id);
            }
            echo "✅ User created/exists: {$email} (role: {$roleName})\n";
        } else {
            echo "❌ Role not found: {$roleName}\n";
        }
    } else {
        echo "✅ User created/exists: {$email} (no role)\n";
    }
}

// Create admin user with all rights
createUserWithRole('Fhoueha', 'fhoueha@gmail.com', 'password', 'admin');

// Create limited user without all rights (viewer role)
createUserWithRole('Test User', 'test@gmail.com', 'password', 'viewer');
