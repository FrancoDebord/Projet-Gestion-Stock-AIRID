<?php

namespace App\Policies;

use App\Models\StockRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any stock requests.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_stock_requests') || $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can view the stock request.
     */
    public function view(User $user, StockRequest $request): bool
    {
        return $user->hasPermission('view_stock_requests') ||
               $user->hasPermission('manage_settings') ||
               $user->id === $request->requester_id;
    }

    /**
     * Determine whether the user can create stock requests.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_stock_requests') || $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can update the stock request.
     */
    public function update(User $user, StockRequest $request): bool
    {
        // Only the requester can update if status is pending
        if ($request->isPending() && $user->id === $request->requester_id) {
            return true;
        }

        // Facility manager can update during approval process
        if ($request->canBeApprovedByFacilityManager() && $user->hasPermission('approve_stock_requests_facility')) {
            return true;
        }

        // Data manager can update during approval process
        if ($request->canBeApprovedByDataManager() && $user->hasPermission('approve_stock_requests_data')) {
            return true;
        }

        return $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can delete the stock request.
     */
    public function delete(User $user, StockRequest $request): bool
    {
        // Only the requester can delete if status is pending
        if ($request->isPending() && $user->id === $request->requester_id) {
            return true;
        }

        return $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can approve the stock request.
     */
    public function approve(User $user, StockRequest $request): bool
    {
        if ($request->canBeApprovedByFacilityManager() && $user->hasPermission('approve_stock_requests_facility')) {
            return true;
        }

        if ($request->canBeApprovedByDataManager() && $user->hasPermission('approve_stock_requests_data')) {
            return true;
        }

        return $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can reject the stock request.
     */
    public function reject(User $user, StockRequest $request): bool
    {
        if ($request->canBeRejected()) {
            if ($request->canBeApprovedByFacilityManager() && $user->hasPermission('approve_stock_requests_facility')) {
                return true;
            }

            if ($request->canBeApprovedByDataManager() && $user->hasPermission('approve_stock_requests_data')) {
                return true;
            }
        }

        return $user->hasPermission('manage_settings');
    }

    /**
     * Determine whether the user can fulfill the stock request.
     */
    public function fulfill(User $user, StockRequest $request): bool
    {
        return $request->isApproved() &&
               ($user->hasPermission('fulfill_stock_requests') || $user->hasPermission('manage_settings'));
    }
}