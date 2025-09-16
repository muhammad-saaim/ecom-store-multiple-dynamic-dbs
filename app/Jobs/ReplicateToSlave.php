<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

class ReplicateToSlave implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected Model $model;
    protected string $table;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->table = $model->getTable();
    }

    public function handle()
    {
        try {
            $attributes = $this->model->getAttributes();

            Log::info("Starting replication for table: {$this->table}, ID: {$this->model->id}", $attributes);

            DB::connection('slave')->table($this->table)->updateOrInsert(
                ['id' => $this->model->id],
                $attributes
            );

            Log::info("Replication successful for table: {$this->table}, ID: {$this->model->id}");

            // Handle pivot tables (example: Order ↔ Products)
            if (method_exists($this->model, 'products')) {
                $productIds = $this->model->products()->pluck('id')->toArray();

                DB::connection('slave')
                    ->table('order_product')
                    ->where('order_id', $this->model->id)
                    ->delete();

                foreach ($productIds as $pid) {
                    DB::connection('slave')->table('order_product')->insert([
                        'order_id' => $this->model->id,
                        'product_id' => $pid,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                Log::info("Pivot sync complete for order_id: {$this->model->id}");
            }

        } catch (\Exception $e) {
            Log::error("Replication failed for table: {$this->table}, ID: {$this->model->id} → " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // rethrow so the job is marked as failed
        }
    }
}
