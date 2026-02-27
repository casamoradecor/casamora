<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('perfil.edit', compact('user'));
    }

    /**
     * Atualiza o perfil completo do usuário em uma única ação
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Regras de validação básicas e CPF matemático
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'cpf' => ['required', 'string', function ($attribute, $value, $fail) {
                $cpf = preg_replace('/\D/', '', $value);
                // Verifica se tem 11 dígitos ou se é uma sequência repetida (ex: 111.111...)
                if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
                    return $fail('O CPF informado é inválido.');
                }
                // Algoritmo oficial de dígitos verificadores
                for ($t = 9; $t < 11; $t++) {
                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $cpf[$c] * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf[$c] != $d) {
                        return $fail('O CPF informado é inválido.');
                    }
                }
            }],
        ];

        // 2. Adiciona regras de senha apenas se o checkbox estiver marcado
        if ($request->has('alterar_senha')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = [
                'required', 
                'confirmed', 
                Password::min(8)->numbers()->symbols() // 8 caracteres, números e símbolos
            ];
        }

        // 3. Executa a validação com mensagens em português
        $request->validate($rules, [
            'email.unique' => 'Este e-mail já está sendo utilizado por outra conta.',
            'email.email' => 'Insira um endereço de e-mail válido.',
            'current_password.current_password' => 'Sua senha atual está incorreta.',
            'password.confirmed' => 'As senhas novas não coincidem.',
            'password.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
        ]);

        // 4. Salva as alterações de dados pessoais
        $user->name = $request->name;
        $user->email = $request->email;
        $user->cpf = preg_replace('/\D/', '', $request->cpf);

        // 5. Salva a nova senha se solicitado
        if ($request->has('alterar_senha')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('sucesso', 'Perfil atualizado com sucesso!');
    }
}