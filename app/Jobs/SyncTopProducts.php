<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;


class SyncTopProducts implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
        public function handle()
        {
            $products = DB::table('order_product')
                ->join('products', 'order_product.product_id', '=', 'products.id')
                ->select('products.id', 'products.name', DB::raw('COUNT(order_product.product_id) as sold'))
                ->groupBy('products.id', 'products.name')
                ->orderByDesc('sold')
                ->get();

            foreach ($products as $p) {
                DB::connection('analytics')->table('top_products')
                    ->updateOrInsert(
                        ['product_id' => $p->id],
                        ['name' => $p->name, 'sold' => $p->sold, 'updated_at' => now()]
                    );
            }
        }
}
