<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
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
            // Arrival management
            'manage_arrivals' => 'Manage stock arrivals (administration reception)',

            // Stock reception permissions
            'view_stock_receptions' => 'View stock receptions',
            'create_stock_receptions' => 'Create stock receptions',
            'edit_stock_receptions' => 'Edit stock receptions',
            'delete_stock_receptions' => 'Delete stock receptions',
        ];

        foreach ($permissions as $name => $description) {
            Permission::updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Create roles with permissions
        $admin = Role::updateOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator with full access']
        );

        $manager = Role::updateOrCreate(
            ['name' => 'manager'],
            ['description' => 'Manager - can manage stock and users']
        );

        $operator = Role::updateOrCreate(
            ['name' => 'operator'],
            ['description' => 'Operator - can record stock movements']
        );

        $viewer = Role::updateOrCreate(
            ['name' => 'viewer'],
            ['description' => 'Viewer - read-only access']
        );

        // Admin gets all permissions
        $adminPermissions = Permission::all()->pluck('id');
        $admin->permissions()->sync($adminPermissions);

        // Manager permissions
        $managerPermissions = Permission::whereIn('name', [
            'view_stock',
            'create_stock',
            'edit_stock',
            'view_movements',
            'view_audit_log',
            'adjust_stock',
            'view_users',
            'manage_users',
            'assign_roles',
            'view_reports',
            'export_reports',
            'manage_arrivals',
            'view_stock_receptions',
            'create_stock_receptions',
            'edit_stock_receptions',
            'delete_stock_receptions',
        ])->pluck('id');
        $manager->permissions()->sync($managerPermissions);

        // Operator permissions
        $operatorPermissions = Permission::whereIn('name', [
            'view_stock',
            'record_stock_in',
            'record_stock_out',
            'view_movements',
            'view_stock_low',
            'view_stock_receptions',
            'create_stock_receptions',
        ])->pluck('id');
        $operator->permissions()->sync($operatorPermissions);

        // Viewer permissions
        $viewerPermissions = Permission::whereIn('name', [
            'view_stock',
            'view_movements',
            'view_reports',
            'view_stock_receptions',
        ])->pluck('id');
        $viewer->permissions()->sync($viewerPermissions);
    }
}
