<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessTaskAssignmentJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $taskId, public int $assignedTo)
    {
        // Small artificial delay so the job stays visible in the jobs
        // table for a bit and can be observed working via queue:listen.
        $this->delay(now()->addSeconds(10));
    }

    public function handle(): void
    {
        $task = Task::find($this->taskId);

        if (! $task) {
            return;
        }

        $task->update(['status' => 'in_progress']);

        Log::info("Task #{$task->id} '{$task->title}' assigned to user #{$this->assignedTo} - notification job processed.");
    }
}
