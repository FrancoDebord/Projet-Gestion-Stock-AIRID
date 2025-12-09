<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\StockArrivalAdministration;
use App\Models\StockIncomingRecord;
use App\Models\StockRequest;
use App\Policies\StockArrivalAdministrationPolicy;
use App\Policies\StockIncomingRecordPolicy;
use App\Policies\StockRequestPolicy;
use App\Policies\FacilityManagerPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        StockArrivalAdministration::class => StockArrivalAdministrationPolicy::class,
        StockIncomingRecord::class => StockIncomingRecordPolicy::class,
        StockRequest::class => StockRequestPolicy::class,
        StockRequest::class => FacilityManagerPolicy::class, // For facility manager actions
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // You can define additional gates here if needed
    }
}
