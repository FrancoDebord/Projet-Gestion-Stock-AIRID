<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use App\Models\StockLocation;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get stock locations by stock_name
        $chemicalRoom = StockLocation::where('stock_name', 'Stock Chemical Room')->first();
        $dataManagement = StockLocation::where('stock_name', 'Stock Data Management')->first();
        $administration = StockLocation::where('stock_name', 'Stock Administration')->first();
        $insectarium = StockLocation::where('stock_name', 'Stock Insectarium')->first();
        $airid = StockLocation::where('stock_name', 'Stock AIRID')->first();

        // Create 7 product categories with their location mappings
        ProductCategory::create([
            'name' => 'Bed-Nets',
            'description' => 'Mosquito nets and bed net products for malaria prevention',
            'stock_location_id' => $chemicalRoom?->id,
        ]);

        ProductCategory::create([
            'name' => 'Insecticides',
            'description' => 'Insecticide products for pest control and disease vector management',
            'stock_location_id' => $chemicalRoom?->id,
        ]);

        ProductCategory::create([
            'name' => 'Consommables Informatiques',
            'description' => 'IT consumables such as toner, paper, ink cartridges, and other office supplies',
            'stock_location_id' => $dataManagement?->id,
        ]);

        ProductCategory::create([
            'name' => 'Appareils Informatiques',
            'description' => 'IT equipment and devices such as computers, servers, and networking hardware',
            'stock_location_id' => $dataManagement?->id,
        ]);

        ProductCategory::create([
            'name' => 'Fournitures de Bureau',
            'description' => 'Office supplies including stationery, folders, pens, and other administrative materials',
            'stock_location_id' => $administration?->id,
        ]);

        ProductCategory::create([
            'name' => 'Produits d\'Insectarium',
            'description' => 'Insectarium products and supplies for insect research and breeding facilities',
            'stock_location_id' => $insectarium?->id,
        ]);

        ProductCategory::create([
            'name' => 'Produits Stock AIRID',
            'description' => 'AIRID-specific products and materials for agricultural research and development',
            'stock_location_id' => $airid?->id,
        ]);
    }
}
