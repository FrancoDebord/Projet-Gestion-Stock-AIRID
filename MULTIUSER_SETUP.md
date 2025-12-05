# Multi-User Stock Management System - Setup Guide

## Overview
Your Laravel application is now configured as a **multi-user stock management system** with role-based access control (RBAC). Each user has specific roles that determine what they can see and do in the system.

## 📋 Database Structure

### Tables Created:
- **roles** - User roles (Admin, Manager, Operator, Viewer)
- **permissions** - Granular permissions for different actions
- **role_has_permissions** - Links roles to permissions
- **user_roles** - Links users to roles
- **stock_items** - Inventory items
- **stock_movements** - History of stock in/out transactions
- **activity_logs** - Audit trail of all changes

## 👥 User Roles

### 1. **Admin** (Full Access)
- All permissions granted
- Can manage all users and assign roles
- Can view audit logs
- Can export reports

### 2. **Manager** (Management Level)
- Create and manage stock items
- Record stock movements
- Manage users (create, edit, assign roles)
- View reports and export data
- View audit logs

### 3. **Operator** (Day-to-Day Operations)
- View stock items
- Record stock incoming (purchases, donations)
- Record stock outgoing (sales, returns)
- View stock movement history
- Receive low stock alerts

### 4. **Viewer** (Read-Only)
- View stock items
- View stock movements
- View reports
- No editing or recording capabilities

## 🚀 Quick Start

### 1. Initial Setup
```bash
# All migrations and seeding have been completed
# Database tables are ready to use
```

### 2. Create Admin User (Optional)
You can assign roles to existing users via the Users Management interface:

1. Go to `/users` (if you have permission)
2. Click "Manage Roles" on any user
3. Select the appropriate roles
4. Click "Update Roles"

### 3. Access the System
1. Login with your user account
2. You'll see the **Dashboard** with:
   - Quick navigation to stock management
   - User information and roles
   - Quick statistics

## 📊 Feature Overview

### Stock Management (`/stocks`)
- **View Stocks**: See all inventory items with real-time quantities
- **Create Stock**: Add new items to inventory
- **Edit Stock**: Update item details (name, SKU, price, etc.)
- **Delete Stock**: Remove items permanently
- **Low Stock Alerts**: Visual indicators for items below minimum quantity
- **Stock Details**: View complete history of movements for each item

### Stock Movements (`/movements`)

#### Record Stock Incoming
- Add purchased items
- Add donations
- Reference PO numbers
- Track receiving dates

#### Record Stock Outgoing
- Record sales
- Process returns
- Document damage/loss
- Prevent overselling with quantity validation

#### Adjust Stock
- Correct inventory counts
- Adjust for discrepancies found during physical count
- Document reasons for adjustments
- Full audit trail maintained

### User Management (`/users`)
- **View Users**: See all system users and their roles
- **User Details**: View assigned roles and effective permissions
- **Manage Roles**: Assign/revoke roles for any user
- **Dynamic Permissions**: Permissions automatically update based on assigned roles

## 🔒 Permission System

### Stock Permissions
- `view_stock` - View inventory items
- `create_stock` - Create new items
- `edit_stock` - Modify item details
- `delete_stock` - Remove items
- `view_stock_low` - See low stock alerts

### Movement Permissions
- `record_stock_in` - Record incoming items
- `record_stock_out` - Record outgoing items
- `view_movements` - View movement history
- `adjust_stock` - Adjust quantities

### User Management Permissions
- `view_users` - View all users
- `manage_users` - Create/edit users
- `assign_roles` - Assign roles to users
- `delete_users` - Remove users

### Other Permissions
- `view_audit_log` - Access audit trail
- `view_reports` - Generate reports
- `export_reports` - Export data
- `manage_settings` - System configuration

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── StockItemController.php      # Stock CRUD
│   │   ├── StockMovementController.php  # Movement recording
│   │   ├── UserManagementController.php # User & role management
│   │   └── ProfileController.php        # User profile
│   └── Middleware/
│       ├── CheckRole.php                # Role-based access
│       └── CheckPermission.php          # Permission checking
├── Models/
│   ├── User.php                         # Extended with roles/permissions
│   ├── Role.php                         # Role model
│   ├── Permission.php                   # Permission model
│   ├── StockItem.php                    # Inventory model
│   ├── StockMovement.php                # Movement record model
│   └── ActivityLog.php                  # Audit trail model
└── View/
    └── Components/                      # Reusable view components

database/
├── migrations/
│   ├── 2025_12_02_000001_create_roles_table.php
│   ├── 2025_12_02_000002_create_permissions_table.php
│   ├── 2025_12_02_000003_create_user_roles_table.php
│   ├── 2025_12_02_000004_create_stock_items_table.php
│   ├── 2025_12_02_000005_create_stock_movements_table.php
│   └── 2025_12_02_000006_create_activity_log_table.php
└── seeders/
    └── RolePermissionSeeder.php         # Seed roles & permissions

resources/views/
├── stocks/
│   ├── index.blade.php                  # Stock list
│   ├── create.blade.php                 # Create form
│   ├── edit.blade.php                   # Edit form
│   └── show.blade.php                   # Stock details
├── movements/
│   ├── index.blade.php                  # Movement history
│   ├── create-in.blade.php              # Record incoming
│   ├── create-out.blade.php             # Record outgoing
│   └── create-adjustment.blade.php      # Adjust stock
└── users/
    ├── index.blade.php                  # User list
    ├── show.blade.php                   # User details
    └── edit-roles.blade.php             # Manage roles

routes/
└── web.php                              # All application routes
```

## 🔄 Workflow Example

### Scenario: New Operator Receiving Stock

1. **Admin assigns "Operator" role** to new employee
2. **Operator sees limited dashboard** - only stock and movements access
3. **Operator goes to `/movements`**
4. **Clicks "Record Incoming"**
5. **Fills form:**
   - Stock Item: "Widget A"
   - Quantity: 100
   - Reason: "Purchase Order"
   - Reference: "PO-2025-001"
6. **System updates stock quantity** automatically
7. **Activity log records the transaction** with operator's name
8. **Manager can view** the complete audit trail

## 🛡️ Security Features

1. **Role-Based Access Control** - Users can only access features their roles permit
2. **Permission Verification** - Every action checks if user has required permission
3. **Audit Trail** - All stock movements and user actions logged
4. **Authorization Middleware** - Routes protected with role/permission checks
5. **Soft Deletes** - Stock items can be archived without losing history
6. **User Tracking** - Every action records who made it and when

## 📝 Common Tasks

### Assign Admin Role to User
```
1. Go to /users
2. Find the user
3. Click "Manage Roles"
4. Check "Admin"
5. Click "Update Roles"
```

### Create Stock Item as Manager
```
1. Go to /stocks
2. Click "Add Stock Item"
3. Fill in details:
   - Name: Item name
   - SKU: Unique identifier
   - Quantity: Current amount
   - Min Quantity: Reorder point
4. Click "Create Stock Item"
```

### Record Stock Movement as Operator
```
1. Go to /movements
2. Click "Record Incoming" or "Record Outgoing"
3. Select stock item
4. Enter quantity
5. Add reference (PO, SO number)
6. Click "Record"
```

## 🚨 Troubleshooting

### User Can't Access a Feature
- Check Dashboard for available features
- Contact administrator to assign required role
- Verify user has the correct role in `/users`

### Stock Quantity Won't Decrease
- Only users with "record_stock_out" permission can record outgoing
- Check current quantity isn't below request amount
- Look in stock movements for adjustment history

### Need to Reset Demo Data
```bash
php artisan migrate:fresh --seed --seeder=RolePermissionSeeder
```

## 🔧 Adding New Features

### To Add New Permission
1. Update `RolePermissionSeeder.php`
2. Add to `$permissions` array
3. Run seeder: `php artisan db:seed RolePermissionSeeder`

### To Add New Role
1. Update `RolePermissionSeeder.php`
2. Create role and assign permissions
3. Run seeder

### To Protect a Route
```php
Route::middleware(['auth', 'role:manager|admin'])->group(function () {
    // Protected routes
});
```

## 📞 Support

For issues or questions about the multi-user system, check:
1. User roles assigned (`/users`)
2. User permissions via role
3. Dashboard for available features based on role
4. Check PHP artisan logs: `storage/logs/`

---

**System ready for multi-user stock management!**
