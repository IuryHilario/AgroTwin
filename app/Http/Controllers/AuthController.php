<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('auth.login');
    }

    public function registerView()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registro realizado com sucesso! Você pode fazer login agora.');

    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')->with('success', 'Login realizado com sucesso!');
        }

        return redirect()->back()->withErrors(['email' => 'Credenciais inválidas.'])->withInput($request->only('email'));

    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Logout realizado com sucesso!');
    }

    public function forgotPasswordView()
    {
        return view('auth.forgot-password');
    }

    /**
     * Sempre responde com a mesma mensagem de sucesso, exista ou não o email
     * — não dá pra um visitante descobrir quais emails estão cadastrados.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        Password::sendResetLink($request->only('email'));

        return back()->with('success', 'Se esse email estiver cadastrado, você vai receber um link para redefinir a senha.');
    }

    public function resetPasswordView(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
        }

        return redirect()->route('login')->with('success', 'Senha redefinida com sucesso! Você já pode fazer login.');
    }
}
