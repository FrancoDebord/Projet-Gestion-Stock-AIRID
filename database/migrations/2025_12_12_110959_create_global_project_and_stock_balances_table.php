<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer le projet "Global" s'il n'existe pas
        if (!DB::table('projects')->where('name', 'Global')->exists()) {
            DB::table('projects')->insert([
                'name' => 'Global',
                'description' => 'Stock global non affecté à un projet spécifique',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Créer la table des soldes de stock par projet
        Schema::create('project_stock_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('stock_item_id');
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('stock_item_id')->references('id')->on('stock_items')->onDelete('cascade');

            $table->unique(['project_id', 'stock_item_id'], 'unique_project_stock_item');
            $table->index(['project_id', 'balance'], 'idx_project_balance');
            $table->index(['stock_item_id'], 'idx_stock_item');
        });

        // Peupler la table avec les soldes existants
        $this->populateExistingBalances();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_stock_balances');

        // Supprimer le projet Global
        DB::table('projects')->where('name', 'Global')->delete();
    }

    /**
     * Peupler les soldes existants basés sur les mouvements de stock
     */
    private function populateExistingBalances(): void
    {
        $globalProjectId = DB::table('projects')->where('name', 'Global')->value('id');

        if (!$globalProjectId) {
            return; // Le projet Global n'existe pas encore
        }

        // Calculer les soldes par projet et article
        $balances = DB::table('stock_movements')
            ->selectRaw("
                COALESCE(project_id, ?) as project_id,
                stock_item_id,
                SUM(CASE WHEN type = 'in' THEN quantity ELSE -quantity END) as balance,
                MAX(created_at) as last_movement_at
            ", [$globalProjectId])
            ->groupBy('project_id', 'stock_item_id')
            ->havingRaw('SUM(CASE WHEN type = \'in\' THEN quantity ELSE -quantity END) != 0')
            ->get();

        foreach ($balances as $balance) {
            DB::table('project_stock_balances')->insert([
                'project_id' => $balance->project_id,
                'stock_item_id' => $balance->stock_item_id,
                'balance' => $balance->balance,
                'last_movement_at' => $balance->last_movement_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
