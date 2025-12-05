<?php

namespace App\Policies;

use App\Models\StockIncomingRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockIncomingRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any stock receptions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_stock_receptions') || $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can view the stock reception.
     */
    public function view(User $user, StockIncomingRecord $record): bool
    {
        return $user->hasPermission('view_stock_receptions') || $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can create stock receptions.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_stock_receptions') || $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can update the stock reception.
     */
    public function update(User $user, StockIncomingRecord $record): bool
    {
        return $user->hasPermission('edit_stock_receptions') || $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can delete the stock reception.
     */
    public function delete(User $user, StockIncomingRecord $record): bool
    {
        return $user->hasPermission('delete_stock_receptions') || $user->hasPermission('manage_settings');
    }
}
