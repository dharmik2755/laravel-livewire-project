<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">
            Welcome, {{ auth()->user()->name }} 👋
        </h1>

        @if (auth()->user()->isAdmin())
            <p class="text-slate-500 mt-1">Here's your admin dashboard — create and track every task in the system.</p>
        @elseif (auth()->user()->isManager())
            <p class="text-slate-500 mt-1">Here's your manager dashboard — assign incoming tasks to your developers.</p>
        @else
            <p class="text-slate-500 mt-1">Here's your developer dashboard — tasks assigned to you appear below.</p>
        @endif
    </div>
    @if (auth()->user()->isAdmin())
        <livewire:tasks.task-manager />
    @elseif (auth()->user()->isManager())
        <livewire:tasks.assign-task />
    @else
        <livewire:tasks.developer-tasks />
    @endif
</div>
