<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;

class ReplicateToSlave implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        // Switch to slave DB
        Config::set('database.default', 'slave');

        $attributes = $this->model->getAttributes();

        // If record exists in slave, update it; otherwise, create it
        $slaveModel = $this->model->replicate();
        $slaveModel->setConnection('slave');

        // Force the same ID for consistency
        $slaveModel->id = $this->model->id;

        // Save to slave
        $slaveModel->save();

        // Handle pivot tables if needed (example for orders → products)
        if (method_exists($this->model, 'products')) {
            $productIds = $this->model->products()->pluck('id')->toArray();
            $slaveModel->products()->sync($productIds);
        }
    }
}
