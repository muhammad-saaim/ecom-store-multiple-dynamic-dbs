<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class SyncRevenuePerMonth implements ShouldQueue
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
        $orders = DB::table('orders')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as orders_count, SUM(total) as revenue')
            ->groupBy('year', 'month')
            ->get();

        foreach ($orders as $row) {
            DB::connection('analytics')->table('revenue_per_month')
                ->updateOrInsert(
                    ['year' => $row->year, 'month' => $row->month],
                    [
                        'orders_count' => $row->orders_count,
                        'revenue' => $row->revenue,
                        'updated_at' => now()
                    ]
                );
        }
}

}
