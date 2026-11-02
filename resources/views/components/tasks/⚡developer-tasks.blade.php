<?php

use Livewire\Component;
use App\Models\Task;

new class extends Component
{
    public function markCompleted(int $taskId)
    {
        Task::where('id', $taskId)
            ->where('assigned_to', auth()->id())
            ->update(['status' => 'completed']);
    }

    public function with(): array
    {
        return [
            'tasks' => Task::where('assigned_to', auth()->id())->latest('assigned_at')->get(),
        ];
    }
};
?>

<div>
    <h2 class="text-lg font-semibold text-slate-800 mb-4">My Assigned Tasks</h2>

    <div class="grid gap-4">
        @forelse ($tasks as $task)
            <div wire:key="task-{{ $task->id }}" class="bg-white rounded-xl shadow p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold text-slate-800">{{ $task->title }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $task->description }}</p>
                        <p class="text-xs text-slate-400 mt-2">Due: {{ $task->time?->format('d M Y, h:i A') ?? 'No deadline' }}</p>
                    </div>
                    <span @class([
                        'px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap',
                        'bg-amber-100 text-amber-700' => $task->status === 'pending',
                        'bg-blue-100 text-blue-700' => $task->status === 'in_progress',
                        'bg-green-100 text-green-700' => $task->status === 'completed',
                    ])>
                        {{ str_replace('_', ' ', $task->status) }}
                    </span>
                </div>

                @if ($task->status !== 'completed')
                    <button wire:click="markCompleted({{ $task->id }})"
                        class="mt-4 text-xs font-medium text-white bg-green-600 hover:bg-green-700 px-3 py-1.5 rounded-lg">
                        Mark as Completed
                    </button>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl shadow p-6 text-center text-slate-400">
                No tasks assigned to you yet.
            </div>
        @endforelse
    </div>
</div>
