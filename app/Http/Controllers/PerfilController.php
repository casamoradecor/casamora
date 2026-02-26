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
    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => ['required', 'current_password'], // Valida se a senha atual está correta
        'password' => ['required', 'confirmed', Password::defaults()], // 'confirmed' exige o campo password_confirmation
    ], [
        'current_password.current_password' => 'A senha atual está incorreta.',
        'password.confirmed' => 'A confirmação da nova senha não confere.'
    ]);

    $user = Auth::user();
    $user->update([
        'password' => Hash::make($request->password),
    ]);

    return redirect()->back()->with('sucesso_senha', 'Senha alterada com sucesso!');
}
    public function update(Request $request)
{
    $user = Auth::user();

    // 1. Regras básicas para o perfil
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'cpf' => 'required|string',
    ];

    // 2. Se o checkbox 'alterar_senha' estiver marcado, aplicamos validações rígidas
    if ($request->has('alterar_senha')) {
        $rules['current_password'] = ['required', 'current_password']; // Valida se a senha atual está certa
        $rules['password'] = ['required', 'confirmed', 'min:8']; // 'confirmed' checa o campo password_confirmation
    }

    // 3. Executa a validação com mensagens em português
    $request->validate($rules, [
        'current_password.current_password' => 'Sua senha atual está incorreta.',
        'password.confirmed' => 'As senhas novas não coincidem.',
        'password.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
        'email.unique' => 'Este e-mail já está sendo utilizado.',
    ]);

    // 4. Salva os dados básicos
    $user->name = $request->name;
    $user->email = $request->email;
    $user->cpf = preg_replace('/\D/', '', $request->cpf);

    // 5. Se o checkbox estava marcado, atualiza a senha de fato
    if ($request->has('alterar_senha')) {
        $user->password = \Hash::make($request->password);
    }

    $user->save();

    return redirect()->back()->with('sucesso', 'Perfil atualizado com sucesso!');
}
}