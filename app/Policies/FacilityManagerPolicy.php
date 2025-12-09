<?php

namespace App\Policies;

use App\Models\StockRequest;
use App\Models\User;

class FacilityManagerPolicy
{
    /**
     * Determine whether the user can view the facility manager index.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('approve_stock_requests_facility');
    }

    /**
     * Determine whether the user can view a specific stock request for approval.
     */
    public function view(User $user, StockRequest $request): bool
    {
        return $user->hasPermission('approve_stock_requests_facility') &&
               $request->canBeApprovedByFacilityManager();
    }

    /**
     * Determine whether the user can approve a stock request.
     */
    public function approve(User $user, StockRequest $request): bool
    {
        return $user->hasPermission('approve_stock_requests_facility') &&
               $request->canBeApprovedByFacilityManager();
    }

    /**
     * Determine whether the user can reject a stock request.
     */
    public function reject(User $user, StockRequest $request): bool
    {
        return $user->hasPermission('approve_stock_requests_facility') &&
               $request->canBeRejected();
    }
}