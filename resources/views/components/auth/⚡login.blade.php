<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $email = '';
    public string $password = '';
    public string $error = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->error = 'Invalid email or password.';
            return;
        }

        session()->regenerate();

        $this->redirect('/dashboard', navigate: false);
    }
};
?>

<div class="max-w-md mx-auto mt-16 bg-white rounded-xl shadow p-8">
    <h1 class="text-2xl font-bold text-slate-800 mb-1">Welcome back</h1>
    <p class="text-sm text-slate-500 mb-6">Sign in to your Task Manager account</p>

    @if ($error)
        <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
            {{ $error }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" wire:model="email"
                class="w-full py-2 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            @error('email')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" wire:model="password"
                class="w-full py-2 rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            @error('password')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg py-2.5 transition"
            wire:loading.attr="disabled" wire:target="login">
            <span wire:loading.remove wire:target="login">Sign in</span>
            <span wire:loading wire:target="login">Signing in...</span>
        </button>
    </form>

    <div class="mt-6 text-xs text-slate-400 border-t pt-4">
        <p class="font-medium text-slate-500 mb-1">Demo accounts (password: <code>password</code>)</p>
        <ul class="space-y-0.5">
            <li>admin@example.com</li>
            <li>manager@example.com</li>
            <li>developer1@example.com / developer2@example.com</li>
        </ul>
    </div>
</div>
