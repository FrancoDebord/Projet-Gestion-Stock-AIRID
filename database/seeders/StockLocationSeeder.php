<?php

namespace Database\Seeders;

use App\Models\StockLocation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockLocationSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Get the first user to be the creator (usually admin user fhoueha)
        $creator = User::first() ?? User::factory()->create();

        $locations = [
            [
                'stock_name' => 'Stock Administration',
                'code_stock' => 'ADM',
                'description' => 'Central administration stock location',
                'creator' => $creator->id,
                'principal_manager' => $creator->id,
            ],
            [
                'stock_name' => 'Stock Chemical Room',
                'code_stock' => 'CHEM',
                'description' => 'Chemical products and reagents storage',
                'creator' => $creator->id,
                'principal_manager' => $creator->id,
            ],
            [
                'stock_name' => 'Stock Data Management',
                'code_stock' => 'DATA',
                'description' => 'Data management and documentation stock',
                'creator' => $creator->id,
                'principal_manager' => $creator->id,
            ],
            [
                'stock_name' => 'Stock Insectarium',
                'code_stock' => 'INSECT',
                'description' => 'Insectarium specimens and related materials',
                'creator' => $creator->id,
                'principal_manager' => $creator->id,
            ],
            [
                'stock_name' => 'Stock AIRID',
                'code_stock' => 'AIRID',
                'description' => 'AIRID project materials and equipment',
                'creator' => $creator->id,
                'principal_manager' => $creator->id,
            ],
        ];

        foreach ($locations as $location) {
            StockLocation::updateOrCreate(
                ['stock_name' => $location['stock_name']],
                array_merge($location, ['creation_date' => now()])
            );
        }
    }
}
