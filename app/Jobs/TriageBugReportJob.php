<?php

namespace App\Jobs;

use App\Models\BugReport;
use App\Services\BugTriageService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TriageBugReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public BugReport $bugReport)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(BugTriageService $bugTriageService): void
    {
        $bugTriageService->triage($this->bugReport);
    }
}
