<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
