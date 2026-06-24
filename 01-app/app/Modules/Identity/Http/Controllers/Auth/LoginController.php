<?php

namespace App\Modules\Identity\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\AccessControl\Domain\RoleName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Credenciales inválidas.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->isActive()) {
            Auth::logout();

            return back()->withErrors([
                'email' => 'Su cuenta está suspendida.',
            ])->onlyInput('email');
        }

        return redirect()->intended($this->dashboardRouteFor($user));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function dashboardRouteFor($user): string
    {
        if ($user->hasRole(RoleName::ADMIN)) {
            return route('admin.dashboard');
        }

        if ($user->hasRole(RoleName::SPECIALIST)) {
            return route('specialist.dashboard');
        }

        return route('patient.dashboard');
    }
}
