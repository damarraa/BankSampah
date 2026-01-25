<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

            // ✅ penting: set default role untuk user baru
            // sesuaikan nama kolomnya: 'role' / 'level' / 'type'
            'role' => 'user',
        ]);

        event(new Registered($user));

        Auth::login($user);

        // ✅ Redirect berdasarkan role (sama seperti login)
        if ($user->isAdmin()) {
            return redirect()->route('admin.setoran.index');
        } elseif ($user->isPetugas()) {
            return redirect()->route('petugas.setoran.index');
        }

        return redirect()->route('user.dashboard');
    }
}
