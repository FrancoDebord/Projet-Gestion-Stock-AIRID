# QUICK_REFERENCE.md - Multi-User Stock Management System

## 🎯 System Overview

A complete **role-based, multi-user stock management system** with:
- ✅ 4 predefined roles (Admin, Manager, Operator, Viewer)
- ✅ 17 granular permissions
- ✅ Stock inventory CRUD operations
- ✅ Stock movement tracking (in/out/adjustments)
- ✅ User and role management
- ✅ Complete audit trail
- ✅ Permission-based access control

**Status**: ✅ Production Ready
**Build Date**: December 2, 2025
**Framework**: Laravel 12

## 🚀 How to Use

### Step 1: Register/Login Users
All users must register or be created in the system first (using Laravel's built-in auth).

### Step 2: Assign Roles
1. **As Admin/Manager**, go to `/users`
2. **Click "Manage Roles"** on any user
3. **Select roles**:
   - **Admin** - Full system access
   - **Manager** - Create/edit stock, manage users
   - **Operator** - Record stock movements only
   - **Viewer** - Read-only access
4. **Click "Update Roles"**

### Step 3: Use Based on Role

#### For Admin/Manager:
- **Create Stock**: `/stocks` → "Add Stock Item"
- **Edit Stock**: Click item → "Edit"
- **View Movements**: `/movements`
- **Manage Users**: `/users`

#### For Operator:
- **View Stock**: `/stocks` (read-only)
- **Record Incoming**: `/movements` → "Record Incoming"
- **Record Outgoing**: `/movements` → "Record Outgoing"

#### For Viewer:
- **View Stock**: `/stocks` (read-only)
- **View Movements**: `/movements` (read-only)
- **View Reports**: (placeholder)

## 📊 Key Features

### Stock Management
| Feature | Admin | Manager | Operator | Viewer |
|---------|-------|---------|----------|--------|
| View Stocks | ✅ | ✅ | ✅ | ✅ |
| Create Stock | ✅ | ✅ | ❌ | ❌ |
| Edit Stock | ✅ | ✅ | ❌ | ❌ |
| Delete Stock | ✅ | ✅ | ❌ | ❌ |

### Movement Recording
| Feature | Admin | Manager | Operator | Viewer |
|---------|-------|---------|----------|--------|
| View Movements | ✅ | ✅ | ✅ | ✅ |
| Record Incoming | ✅ | ✅ | ✅ | ❌ |
| Record Outgoing | ✅ | ✅ | ✅ | ❌ |
| Adjust Stock | ✅ | ✅ | ❌ | ❌ |

### User Management
| Feature | Admin | Manager | Operator | Viewer |
|---------|-------|---------|----------|--------|
| View Users | ✅ | ✅ | ❌ | ❌ |
| Manage Users | ✅ | ✅ | ❌ | ❌ |
| Assign Roles | ✅ | ✅ | ❌ | ❌ |

## 💾 Database Schema

### Users Table (Existing)
- id, name, email, password, etc.

### Roles Table (New)
- id, name, description

### Permissions Table (New)
- id, name, description

### User Roles Table (New)
- user_id, role_id (many-to-many)

### Stock Items Table (New)
```
- id, name, sku (unique), description
- quantity, min_quantity, unit
- unit_price, category, location
- user_id (creator), created_at, updated_at, deleted_at
```

### Stock Movements Table (New)
```
- id, stock_item_id, user_id
- type (in/out/adjustment)
- quantity, reason, notes, reference
- created_at, updated_at
```

### Activity Logs Table (New)
- Complete audit trail of all actions

## 🔐 Permission System

### 17 Permissions Available
```
Stock Management:
- view_stock
- create_stock
- edit_stock
- delete_stock
- view_stock_low

Stock Movements:
- record_stock_in
- record_stock_out
- view_movements
- adjust_stock

User Management:
- view_users
- manage_users
- assign_roles
- delete_users

Reporting:
- view_reports
- export_reports
- view_audit_log

Settings:
- manage_settings
```

## 📁 Files Created

### Models (5 new)
- `app/Models/Role.php`
- `app/Models/Permission.php`
- `app/Models/StockItem.php`
- `app/Models/StockMovement.php`
- `app/Models/ActivityLog.php`

### Controllers (3 new)
- `app/Http/Controllers/StockItemController.php`
- `app/Http/Controllers/StockMovementController.php`
- `app/Http/Controllers/UserManagementController.php`

### Middleware (2 new)
- `app/Http/Middleware/CheckRole.php`
- `app/Http/Middleware/CheckPermission.php`

### Migrations (6 new)
- `database/migrations/2025_12_02_000001_create_roles_table.php`
- `database/migrations/2025_12_02_000002_create_permissions_table.php`
- `database/migrations/2025_12_02_000003_create_user_roles_table.php`
- `database/migrations/2025_12_02_000004_create_stock_items_table.php`
- `database/migrations/2025_12_02_000005_create_stock_movements_table.php`
- `database/migrations/2025_12_02_000006_create_activity_log_table.php`

### Seeders (1 new)
- `database/seeders/RolePermissionSeeder.php` (creates roles/permissions)

### Views (12 new)
**Stocks**:
- `resources/views/stocks/index.blade.php`
- `resources/views/stocks/create.blade.php`
- `resources/views/stocks/edit.blade.php`
- `resources/views/stocks/show.blade.php`

**Movements**:
- `resources/views/movements/index.blade.php`
- `resources/views/movements/create-in.blade.php`
- `resources/views/movements/create-out.blade.php`
- `resources/views/movements/create-adjustment.blade.php`

**Users**:
- `resources/views/users/index.blade.php`
- `resources/views/users/show.blade.php`
- `resources/views/users/edit-roles.blade.php`

### Configuration
- `routes/web.php` - Updated with 30+ new routes
- `resources/views/dashboard.blade.php` - Enhanced with role-aware navigation
- `app/Models/User.php` - Extended with role methods

## 🎓 Example Workflows

### Workflow 1: New Employee Onboarding
```
1. HR Admin creates new user account (registration)
2. Go to /users → Find new user → "Manage Roles"
3. Assign "Operator" role
4. Operator can now record stock movements
5. All actions logged in activity_logs table
```

### Workflow 2: Stock Check-In
```
1. Operator logged in with "Operator" role
2. Go to Dashboard → Click "Movements"
3. Click "Record Incoming"
4. Select Stock Item: "Office Supplies"
5. Enter Quantity: 50
6. Reference: "PO-2025-001"
7. System updates stock quantity +50
8. Activity log records who, what, when
```

### Workflow 3: Stock Check-Out
```
1. Operator goes to "Record Outgoing"
2. Select item and quantity
3. System validates sufficient stock
4. If valid: quantity decreases, logged
5. If invalid: error message shown
```

### Workflow 4: Manager Reviews Audit
```
1. Manager goes to /users
2. Can see all users and their roles
3. Clicks on a user to see their permissions
4. All actions tied to users for accountability
```

## 🔧 Customization Options

### Add New Role
Edit `RolePermissionSeeder.php`, add role and assign permissions, then:
```bash
php artisan db:seed RolePermissionSeeder
```

### Add New Permission
Edit seeder, add to permissions array, assign to roles

### Change Role Requirements
Update controller permission checks:
```php
if (!auth()->user()->hasPermission('create_stock')) {
    abort(403);
}
```

### Customize Dashboard
Edit `resources/views/dashboard.blade.php` to add role-specific sections

## 📊 Usage Statistics Available

The dashboard shows users:
- Total stock items count
- Low stock items count
- Total movements count
- Today's movements count
- Total users count

## 🛡️ Security Built-In

✅ All routes require authentication
✅ All operations check permissions
✅ Full audit trail maintained
✅ User isolation per role
✅ CSRF token protection
✅ Mass assignment protection
✅ Soft deletes prevent data loss

## 📞 Testing the System

### Test as Operator
1. Login with operator user
2. Dashboard shows limited menu
3. Try accessing /users → 403 Forbidden
4. Go to /movements → Works
5. Can't access /stocks/create → 403

### Test as Manager
1. Login with manager user
2. Can access /stocks, /users, /movements
3. Try accessing admin features → Limited

### Test as Admin
1. Login with admin user
2. Can access everything
3. Can assign/revoke roles

## ✅ Everything Ready!

Your multi-user stock management system is:
- ✅ Fully implemented
- ✅ Database migrated
- ✅ Roles and permissions seeded
- ✅ Views created
- ✅ Controllers set up
- ✅ Routes configured
- ✅ Ready to use!

**Start by registering users and assigning roles via `/users`**

---

For detailed setup information, see `MULTIUSER_SETUP.md`
