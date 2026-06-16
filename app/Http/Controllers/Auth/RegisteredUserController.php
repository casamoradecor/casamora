<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CpfValido;
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
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()->min(8)->symbols()->numbers()],
            'cpf' => ['required', 'string', 'unique:' . User::class, new CpfValido],
            'telefone' => ['required', 'string', 'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'],
        ], [
            'name.required' => 'Por favor, informe o seu nome completo.',
            'name.regex' => 'O nome deve conter apenas letras e espacos.',
            'name.min' => 'O nome deve ter no minimo 3 caracteres.',
            'email.required' => 'O e-mail e obrigatorio.',
            'email.unique' => 'Este e-mail ja foi cadastrado na Casa MORA.',
            'email.email' => 'Insira um e-mail valido.',
            'password.required' => 'A senha e obrigatoria para proteger a sua conta.',
            'password.confirmed' => 'As senhas digitadas nao sao iguais.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password' => 'A senha deve conter pelo menos um numero e um simbolo (ex: @, #, !).',
            'cpf.required' => 'O CPF e obrigatorio.',
            'cpf.unique' => 'Este CPF ja esta vinculado a uma conta. Se precisar de ajuda, entre em contato com o suporte.',
            'telefone.required' => 'O telefone e obrigatorio.',
            'telefone.regex' => 'O numero de telefone e invalido. Verifique se o formato esta correto.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'cpf' => preg_replace('/\D/', '', $request->cpf),
            'telefone' => preg_replace('/\D/', '', $request->telefone),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
