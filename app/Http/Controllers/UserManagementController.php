<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of users.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user->hasPermission('view_users')) {
            abort(403, 'Unauthorized');
        }

        $users = User::with('roles')->paginate(15);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for assigning roles to a user.
     */
    public function editRoles(User $user)
    {
        $authUser = Auth::user();

        if (!$authUser->hasPermission('assign_roles')) {
            abort(403, 'Unauthorized');
        }

        $roles = Role::all();
        $userRoles = $user->roles()->pluck('id')->toArray();

        return view('users.edit-roles', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update user roles.
     */
    public function updateRoles(Request $request, User $user)
    {
        $authUser = Auth::user();

        if (!$authUser->hasPermission('assign_roles')) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($validated['roles']);

        return redirect()->route('users.index')->with('success', 'User roles updated successfully');
    }

    /**
     * Show user details.
     */
    public function show(User $user)
    {
        $authUser = Auth::user();

        if (!$authUser->hasPermission('view_users')) {
            abort(403, 'Unauthorized');
        }

        $roles = $user->roles;
        $permissions = $user->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn($role) => $role->permissions)
            ->unique('id');

        return view('users.show', compact('user', 'roles', 'permissions'));
    }
}
