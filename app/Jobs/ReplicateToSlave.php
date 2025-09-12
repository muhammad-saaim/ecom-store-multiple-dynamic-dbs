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

    protected $model;

    /**
     * @param Model $model - the model instance to replicate
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Switch to slave DB
        Config::set('database.default', 'slave');

        // Update or create the record in slave
        $this->model->replicate()->updateOrCreate(
            ['id' => $this->model->id],
            $this->model->getAttributes()
        );

        // Optional: reset back to master (not needed in jobs)
    }
}
