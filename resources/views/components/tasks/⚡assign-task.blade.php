<?php

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use App\Jobs\ProcessTaskAssignmentJob;

new class extends Component
{
    public array $selectedDeveloper = [];

    public function assign(int $taskId)
    {
        $developerId = $this->selectedDeveloper[$taskId] ?? null;

        if (! $developerId) {
            return;
        }

        $task = Task::findOrFail($taskId);
        $task->update([
            'assigned_to' => $developerId,
            'assigned_at' => now(),
        ]);

        ProcessTaskAssignmentJob::dispatch($task->id, (int) $developerId);
    }

    public function with(): array
    {
        return [
            'tasks' => Task::with('assignee')->latest()->get(),
            'developers' => User::where('role', 'developer')->orderBy('name')->get(),
        ];
    }
};
?>

<div>
    <h2 class="text-lg font-semibold text-slate-800 mb-4">Task List — Assign to Developers</h2>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Assigned To</th>
                    <th class="px-4 py-3">Assign</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tasks as $task)
                    <tr wire:key="task-{{ $task->id }}">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $task->title }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 rounded-full text-xs font-medium',
                                'bg-amber-100 text-amber-700' => $task->status === 'pending',
                                'bg-blue-100 text-blue-700' => $task->status === 'in_progress',
                                'bg-green-100 text-green-700' => $task->status === 'completed',
                            ])>
                                {{ str_replace('_', ' ', $task->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <select wire:model="selectedDeveloper.{{ $task->id }}"
                                    class="rounded-lg border-slate-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select developer</option>
                                    @foreach ($developers as $developer)
                                        <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                                    @endforeach
                                </select>
                                <button wire:click="assign({{ $task->id }})"
                                    wire:loading.attr="disabled" wire:target="assign({{ $task->id }})"
                                    class="text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg">
                                    Assign
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-400">No tasks available yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
