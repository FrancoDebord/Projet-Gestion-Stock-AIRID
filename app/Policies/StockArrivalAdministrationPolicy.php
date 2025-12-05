<?php

namespace App\Policies;

use App\Models\StockArrivalAdministration;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockArrivalAdministrationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any arrivals.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_stock') || $user->hasPermission('manage_settings') || $user->hasPermission('manage_arrivals');
    }

    /**
     * Determine whether the user can view the arrival.
     */
    public function view(User $user, StockArrivalAdministration $arrival): bool
    {
        return $user->hasPermission('view_stock') || $user->hasPermission('manage_settings') || $user->hasPermission('manage_arrivals');
    }

    /**
     * Determine whether the user can create arrivals.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_stock') || $user->hasPermission('manage_settings') || $user->hasPermission('manage_arrivals');
    }

    /**
     * Determine whether the user can update the arrival.
     */
    public function update(User $user, StockArrivalAdministration $arrival): bool
    {
        return $user->hasPermission('edit_stock') || $user->hasPermission('manage_settings') || $user->hasPermission('manage_arrivals');
    }

    /**
     * Determine whether the user can delete the arrival.
     */
    public function delete(User $user, StockArrivalAdministration $arrival): bool
    {
        return $user->hasPermission('delete_stock') || $user->hasPermission('manage_settings') || $user->hasPermission('manage_arrivals');
    }
}
