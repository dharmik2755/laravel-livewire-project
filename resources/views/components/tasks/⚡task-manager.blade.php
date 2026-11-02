<?php

use Livewire\Component;
use App\Models\Task;

new class extends Component
{
    public bool $showModal = false;

    public string $title = '';
    public string $description = '';
    public string $time = '';
    public string $status = 'pending';

    public function openModal()
    {
        $this->reset(['title', 'description', 'time', 'status']);
        $this->status = 'pending';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function createTask()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        Task::create([
            'title' => $this->title,
            'description' => $this->description,
            'time' => $this->time ?: null,
            'status' => $this->status,
            'created_by' => auth()->id(),
        ]);

        $this->showModal = false;
        $this->reset(['title', 'description', 'time', 'status']);
    }

    public function with(): array
    {
        return [
            'tasks' => Task::with('assignee')->latest()->get(),
        ];
    }
};
?>

<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-slate-800">All Tasks</h2>
        <button wire:click="openModal"
            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm">
            + Create Task
        </button>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">Time</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Assigned To</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($tasks as $task)
                    <tr wire:key="task-{{ $task->id }}">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ $task->title }}</td>
                        <td class="px-4 py-3 text-slate-500 max-w-xs truncate">{{ $task->description }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $task->time?->format('d M Y, h:i A') ?? '-' }}</td>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-400">No tasks yet. Create the first one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($showModal)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" wire:click.self="closeModal">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Create Task</h3>

                <form wire:submit="createTask" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Title</label>
                        <input type="text" wire:model="title"
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('title') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <textarea wire:model="description" rows="3"
                            class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"></textarea>
                        @error('description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Time</label>
                            <input type="datetime-local" wire:model="time"
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @error('time') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <select wire:model="status"
                                class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm">
                            Save Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
