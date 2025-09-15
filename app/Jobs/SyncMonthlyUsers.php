<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;


class SyncMonthlyUsers implements ShouldQueue
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
            $users = DB::table('users')
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('year', 'month')
                ->get();

            foreach ($users as $row) {
                DB::connection('analytics')->table('monthly_users')
                    ->updateOrInsert(
                        ['year' => $row->year, 'month' => $row->month],
                        ['count' => $row->count, 'updated_at' => now()]
                    );
            }
        }
}
