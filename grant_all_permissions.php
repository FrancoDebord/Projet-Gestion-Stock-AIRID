<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

try {
    // Find the user Fhoueha
    $user = User::where('email', 'fhoueha@gmail.com')->first();

    if (!$user) {
        echo "❌ User Fhoueha not found\n";
        exit(1);
    }

    echo "👤 Found user: {$user->name} ({$user->email})\n";

    // Get all permissions
    $allPermissions = Permission::all();

    if ($allPermissions->isEmpty()) {
        echo "❌ No permissions found in database. Creating them first...\n";

        // Create all permissions manually
        $permissions = [
            // Stock management permissions
            'view_stock' => 'View stock items',
            'create_stock' => 'Create stock items',
            'edit_stock' => 'Edit stock items',
            'delete_stock' => 'Delete stock items',
            'view_stock_low' => 'View low stock alerts',

            // Stock movement permissions
            'record_stock_in' => 'Record stock incoming',
            'record_stock_out' => 'Record stock outgoing',
            'view_movements' => 'View stock movements',
            'view_audit_log' => 'View audit logs',
            'adjust_stock' => 'Adjust stock quantity',

            // User management permissions
            'view_users' => 'View users',
            'manage_users' => 'Create and edit users',
            'assign_roles' => 'Assign roles to users',
            'delete_users' => 'Delete users',

            // Reporting permissions
            'view_reports' => 'View stock reports',
            'export_reports' => 'Export reports',

            // Settings permissions
            'manage_settings' => 'Manage system settings',
            'manage_arrivals' => 'Manage stock arrivals (administration reception)',

            // Stock reception permissions
            'view_stock_receptions' => 'View stock receptions',
            'create_stock_receptions' => 'Create stock receptions',
            'edit_stock_receptions' => 'Edit stock receptions',
            'delete_stock_receptions' => 'Delete stock receptions',

            // Stock request permissions
            'view_stock_requests' => 'View stock requests',
            'create_stock_requests' => 'Create stock requests',
            'approve_stock_requests_facility' => 'Approve stock requests as facility manager',
            'approve_stock_requests_data' => 'Approve stock requests as data manager',
            'fulfill_stock_requests' => 'Fulfill approved stock requests',
        ];

        foreach ($permissions as $name => $description) {
            Permission::updateOrCreate(['name' => $name], ['description' => $description]);
            echo "✅ Created: {$name}\n";
        }

        $allPermissions = Permission::all();
    }

    echo "\n🔐 Granting all permissions to Fhoueha...\n";

    // Attach all permissions to the user
    $user->permissions()->sync($allPermissions->pluck('id'));

    echo "✅ All permissions granted to Fhoueha!\n";

    // Verify
    $userPermissions = $user->permissions()->get();
    echo "\n📋 Fhoueha now has {$userPermissions->count()} permissions:\n";

    foreach ($userPermissions as $perm) {
        echo "- {$perm->name}: {$perm->description}\n";
    }

    // Test specific permissions
    echo "\n🎯 Testing key permissions:\n";
    $testPerms = ['approve_stock_requests_facility', 'view_stock_requests', 'create_stock_requests'];

    foreach ($testPerms as $permName) {
        $hasPerm = $user->hasPermission($permName);
        $status = $hasPerm ? "✅ YES" : "❌ NO";
        echo "{$status}: {$permName}\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}