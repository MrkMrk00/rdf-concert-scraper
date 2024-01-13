<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;

class AuthController extends Controller
{
    #[Get('/login', name: 'login')]
    public function showLoginPage(): View
    {
        return $this->page('auth.login');
    }

    #[Post('/login', name: 'login_handle')]
    public function handleLogin(Request $request): RedirectResponse
    {
        try {
            // if throws -> migrate; je to fujky, ale zajistí to, že se to spustí i když je prázdná databáze
            DB::select('SELECT COUNT(*) FROM users');
        } catch (\Throwable $th) {
            Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended();
        }


        return back()->withErrors([
            'email' => 'Špatné přihlašovací údaje.',
        ])->onlyInput('email');
    }
}
