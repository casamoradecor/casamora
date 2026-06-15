<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules; // Certifique-se de que isso está aqui
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Aplicando a mesma régua de segurança da Casa MORÁ
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()->min(8)->symbols()->numbers()],
        ], [
            'password.required' => 'A senha é obrigatória para proteger a sua conta.',
            'password.confirmed' => 'As senhas digitadas não são iguais.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            // Esta mensagem abaixo cobre o Symbols e Numbers
            'password' => 'A senha deve conter pelo menos um número e um símbolo (ex: @, #, !).',
        ]);

        // Se passar na validação, tentamos resetar
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Sua senha foi redefinida com sucesso. Voce ja pode entrar com a nova senha.')
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => $this->mensagemErroResetSenha($status, (string) $request->input('email'))]);
    }

    private function mensagemErroResetSenha(string $status, string $email): string
    {
        Log::warning('Falha ao redefinir senha.', [
            'email' => $email,
            'status' => $status,
        ]);

        return match ($status) {
            Password::INVALID_TOKEN => 'O link de redefinicao de senha e invalido ou expirou. Solicite um novo link e tente novamente.',
            Password::INVALID_USER => 'Nao foi possivel validar a solicitacao de redefinicao de senha.',
            default => 'Nao foi possivel redefinir a senha agora. Tente novamente.',
        };
    }
}
