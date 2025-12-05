# Implementation Details - Multi-User Stock Management System

## Architecture Overview

### Core Components

#### 1. Authentication Layer
- Uses Laravel Breeze authentication scaffolding
- All routes protected with `auth` middleware
- Session-based user authentication

#### 2. Authorization Layer
Three levels of authorization:

**Route-Level Authorization**
```php
// In routes/web.php
Route::middleware('auth')->prefix('stocks')->group(function () {
    // Routes here require authentication
});
```

**Controller-Level Authorization**
```php
// In controllers
if (!$user->hasPermission('create_stock')) {
    abort(403, 'Unauthorized');
}
```

**Model-Level Authorization**
- Soft deletes preserve data
- User tracking on all operations
- Foreign keys ensure data integrity

#### 3. Role & Permission System
- **Roles**: Collections of permissions (e.g., Manager has 12 permissions)
- **Permissions**: Atomic actions (e.g., "create_stock")
- **User-Role Relationship**: Many-to-many, user can have multiple roles
- **Role-Permission Relationship**: Many-to-many, role can have many permissions

### Database Relationships

```
User
  ├─ has many → User Roles
  │              └─ belongs to Role
  │                  ├─ has many → Role Permissions
  │                  │              └─ belongs to Permission
  │                  └─ has many → Users
  ├─ has many → Stock Items
  ├─ has many → Stock Movements
  └─ has many → Activity Logs

Stock Item
  ├─ belongs to → User (creator)
  ├─ has many → Stock Movements
  └─ has many → Activity Logs

Stock Movement
  ├─ belongs to → Stock Item
  ├─ belongs to → User (recorder)
  └─ has one → Activity Log

Role
  ├─ has many → Permissions
  ├─ has many → Users
  └─ has many → Activity Logs

Permission
  ├─ has many → Roles
  └─ has many → Activity Logs

Activity Log
  ├─ belongs to → Causer (User - polymorphic)
  └─ belongs to → Subject (Stock Item - polymorphic)
```

## Key Classes and Methods

### User Model Extensions

```php
class User extends Authenticatable
{
    // Relationships
    public function roles() → BelongsToMany
    public function permissions() → HasManyThrough
    public function stockItems() → HasMany
    public function stockMovements() → HasMany
    
    // Permission Checking
    public function hasRole($roleName) → bool
    public function hasAnyRole($roleNames) → bool
    public function hasAllRoles($roleNames) → bool
    public function hasPermission($permissionName) → bool
}
```

### Role Model

```php
class Role extends Model
{
    public function permissions() → BelongsToMany
    public function users() → BelongsToMany
    public function hasPermission($permission) → bool
}
```

### StockItem Model

```php
class StockItem extends Model
{
    // Relationships
    public function creator() → BelongsTo  // User who created it
    public function movements() → HasMany
    
    // Methods
    public function getTotalValueAttribute() → float
    public function isLowStock() → bool
}
```

### StockMovement Model

```php
class StockMovement extends Model
{
    // Types: 'in', 'out', 'adjustment'
    // Relationships
    public function stockItem() → BelongsTo
    public function user() → BelongsTo
    
    // Methods
    public function getQuantityChangeAttribute() → int
}
```

## Controller Flow

### StockItemController Flow

**Index (GET /stocks)**
1. Check `view_stock` permission
2. Query StockItems with optional low stock filter
3. Paginate results (15 per page)
4. Render `stocks.index` view

**Store (POST /stocks)**
1. Check `create_stock` permission
2. Validate input
3. Create StockItem with current user_id
4. Log activity (stock_item_created)
5. Redirect to show page

**Update (PATCH /stocks/{stock})**
1. Check `edit_stock` permission
2. Validate input
3. Store old data for comparison
4. Update item
5. Log activity with before/after data
6. Redirect to show page

**Destroy (DELETE /stocks/{stock})**
1. Check `delete_stock` permission
2. Delete item (soft delete)
3. Log activity
4. Redirect with success message

### StockMovementController Flow

**StoreIn (POST /movements/in)**
1. Check `record_stock_in` permission
2. Validate input
3. Create StockMovement with type='in'
4. Increment stock quantity
5. Log activity with quantity details
6. Redirect to movements index

**StoreOut (POST /movements/out)**
1. Check `record_stock_out` permission
2. Validate input
3. Check sufficient quantity (prevent negative)
4. Create StockMovement with type='out'
5. Decrement stock quantity
6. Log activity
7. Redirect with success or error

**StoreAdjustment (POST /movements/adjustment)**
1. Check `adjust_stock` permission
2. Validate input
3. Create StockMovement with type='adjustment'
4. Apply adjustment (can be +/-)
5. Log activity
6. Redirect

### UserManagementController Flow

**Index (GET /users)**
1. Check `view_users` permission
2. Load all users with their roles
3. Paginate (15 per page)
4. Render users list

**EditRoles (GET /users/{user}/edit-roles)**
1. Check `assign_roles` permission
2. Load all available roles
3. Load user's current roles
4. Render role assignment form

**UpdateRoles (PATCH /users/{user}/roles)**
1. Check `assign_roles` permission
2. Validate selected roles exist
3. Sync user roles (replace all with new)
4. Log activity
5. Redirect

## Permission Verification Patterns

### Pattern 1: Direct Check in Controller
```php
if (!$user->hasPermission('view_stock')) {
    return redirect('/dashboard')->with('error', 'Unauthorized');
}
```

### Pattern 2: Abort with 403
```php
if (!$user->hasPermission('create_stock')) {
    abort(403, 'Unauthorized');
}
```

### Pattern 3: Method Conditional in View
```blade
@if(auth()->user()->hasPermission('edit_stock'))
    <a href="{{ route('stocks.edit', $stock) }}">Edit</a>
@endif
```

## Activity Logging System

### What Gets Logged
- All stock item CRUD operations
- All stock movements (in/out/adjustment)
- All role assignments
- User details, old values, new values

### Log Structure
```php
ActivityLog::create([
    'log_name' => 'stock_item_created',
    'description' => 'Created stock item',
    'subject_type' => StockItem::class,
    'subject_id' => $stock->id,
    'causer_type' => User::class,
    'causer_id' => auth()->id(),
    'properties' => [
        'name' => $stock->name,
        'quantity' => $stock->quantity,
        // ... other relevant data
    ]
]);
```

## Validation Rules

### Stock Item Creation
```
name: required|string|max:255
sku: required|string|unique:stock_items
description: nullable|string
quantity: required|integer|min:0
min_quantity: required|integer|min:0
unit: required|string|max:50
unit_price: nullable|numeric|min:0
category: nullable|string|max:100
location: nullable|string|max:255
```

### Stock Movement
```
stock_item_id: required|exists:stock_items,id
quantity: required|integer|min:1
reason: nullable|string
reference: nullable|string|max:255
notes: nullable|string
```

### Role Assignment
```
roles: required|array
roles.*: exists:roles,id
```

## Seeding Strategy

### RolePermissionSeeder
1. Creates 17 permissions (stock, movement, user, reporting)
2. Creates 4 roles:
   - **Admin**: All 17 permissions
   - **Manager**: 12 permissions (management + viewing)
   - **Operator**: 5 permissions (movements + viewing)
   - **Viewer**: 3 permissions (read-only)
3. Uses `updateOrCreate` to be idempotent

## Error Handling

### Missing Permission
- Returns 403 Forbidden
- Message: "Unauthorized - You do not have the required permission"

### Invalid Data
- Validation errors returned to form
- Fields highlighted with error messages
- User returned to form with old input

### Quantity Validation
- Cannot record more outgoing than available
- Prevents negative inventory
- Shows error message with available quantity

## Testing Approach

### Test Each Role
1. Login as each role type
2. Verify accessible pages/features
3. Verify inaccessible pages return 403
4. Verify actions work as expected

### Test Permission Logic
1. Create test user
2. Assign specific permissions via role
3. Test that user can/cannot perform actions
4. Remove permission, test again

### Test Activity Logging
1. Perform action
2. Check activity_logs table
3. Verify correct user and action logged
4. Verify properties recorded correctly

## Extending the System

### Add New Permission
1. Edit `RolePermissionSeeder.php`
2. Add to `$permissions` array
3. Assign to roles that need it
4. Run: `php artisan db:seed RolePermissionSeeder`

### Add New Role
1. Create in seeder:
   ```php
   $supervisor = Role::create(['name' => 'supervisor']);
   $supervisor->permissions()->sync([/* permission ids */]);
   ```
2. Run seeder

### Add Role Check in View
```blade
@role('admin|manager')
    <!-- Admin/Manager only content -->
@endrole
```

### Add Permission Check in View
```blade
@permission('edit_stock')
    <!-- Can edit stock -->
@endpermission
```

### Create Policy for Fine-Grained Authorization
```php
class StockItemPolicy
{
    public function update(User $user, StockItem $item)
    {
        return $user->hasPermission('edit_stock');
    }
}
```

## Performance Considerations

### Eager Loading
Relations are loaded with `with()` to prevent N+1:
```php
StockItem::with('creator', 'movements')->paginate(15);
```

### Pagination
All lists paginated for scalability:
- Stock items: 15 per page
- Movements: 20 per page
- Users: 15 per page

### Indexing
Database should have indexes on:
- user_id (foreign keys)
- stock_item_id (movements)
- created_at (sorting)
- sku (unique searches)

## Security Considerations

### CSRF Protection
All forms use `@csrf` token

### Mass Assignment Protection
Models use `$fillable` whitelist

### SQL Injection
Uses Laravel query builder/Eloquent (parameterized)

### XSS Protection
Blade templates auto-escape with `{{ }}`

### Authentication
All routes require `auth` middleware

### Authorization
Every action checks permissions

## Migration Path

If upgrading from single-user system:

1. **Backup data**
   ```bash
   php artisan backup:run
   ```

2. **Run migrations**
   ```bash
   php artisan migrate
   ```

3. **Seed roles/permissions**
   ```bash
   php artisan db:seed RolePermissionSeeder
   ```

4. **Assign roles to existing users**
   - Go to `/users` → "Manage Roles"
   - Assign admin role to system admin
   - Assign appropriate roles to others

5. **Test thoroughly**
   - Login as each user
   - Verify access and functionality

## Conclusion

The system provides a complete, production-ready multi-user stock management solution with:
- ✅ Granular permission system
- ✅ Multiple role support
- ✅ Complete audit trail
- ✅ Scalable architecture
- ✅ Easy to extend
- ✅ Secure by default
