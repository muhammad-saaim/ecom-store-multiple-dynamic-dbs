<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB; // ✅ import DB facade

class SyncPageViews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $page;
    protected int $increment;

    /**
     * Create a new job instance.
     *
     * @param string $page The page name or URL
     * @param int $increment Number of views to increment (default 1)
     */
    public function __construct(string $page, int $increment = 1)
    {
        $this->page = $page;
        $this->increment = $increment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::connection('analytics')->table('page_views')->updateOrInsert(
            ['page' => $this->page],
            [
                'count' => DB::raw("count + {$this->increment}"),
                'updated_at' => now()
            ]
        );
    }
}
