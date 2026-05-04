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
use App\Rules\CpfValido;

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
        // 1. Validação com mensagens customizadas para a Casa MORÁ:
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email' => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()->min(8)->symbols()->numbers()],
            'cpf' => ['required', 'string', 'unique:'.User::class, new CpfValido],
            'telefone' => ['required', 'string', 'regex:/^\(\d{2}\)\s\d{4,5}-\d{4}$/'],
        ], [
            'name.required' => 'Por favor, informe o seu nome completo.',
            'name.regex' => 'O nome deve conter apenas letras e espaços.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já foi cadastrado na Casa MORÁ.',
            'email.email' => 'Insira um e-mail válido.',
            'password.required' => 'A senha é obrigatória para proteger a sua conta.',
            'password.confirmed' => 'As senhas digitadas não são iguais.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password' => 'A senha deve conter pelo menos um número e um símbolo (ex: @, #, !).',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado em outra conta.',
            'telefone.required' => 'O telefone é obrigatório.',
            'telefone.regex' => 'O número de telefone é inválido. Verifique se o formato está correto.',
        ]);

        // 2. Na criação do usuário:
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
