<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Config;

class SyncMasterToSlave extends Command
{
    protected $signature = 'sync:master-to-slave';
    protected $description = 'Sync existing orders and products from master to slave DB';

    public function handle()
    {
        // Sync Products
        Config::set('database.default', 'mysql'); // master
        $products = Product::all();

        Config::set('database.default', 'slave'); // slave
        foreach ($products as $product) {
            $product->replicate()->updateOrCreate(
                ['id' => $product->id],
                $product->getAttributes()
            );
        }

        // Sync Orders
        Config::set('database.default', 'mysql'); // master
        $orders = Order::with('products')->get();

        Config::set('database.default', 'slave'); // slave
        foreach ($orders as $order) {
            $replicatedOrder = $order->replicate()->updateOrCreate(
                ['id' => $order->id],
                $order->getAttributes()
            );

            // Attach products to order
            $replicatedOrder->products()->sync(
                $order->products->pluck('id')->toArray()
            );
        }

        $this->info('Master DB synced to slave successfully!');
    }
}
