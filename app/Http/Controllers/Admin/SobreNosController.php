<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SobreNos;
use Illuminate\Support\Facades\Storage;

class SobreNosController extends Controller
{
    /**
     * Exibe a página de edição visual do "Sobre Nós" no Admin.
     */
    public function edit()
    {
        // Busca o registro único (ID 1) ou cria uma instância vazia para não quebrar a View
        $conteudo = SobreNos::first() ?? new SobreNos();

        return view('admin.sobre_nos_visual', compact('conteudo'));
    }

    /**
     * Atualiza os campos de texto (Título e Parágrafos).
     * Acionado pelo 'onchange="this.form.submit()"' nos inputs e textareas.
     */
    public function updateTexto(Request $request)
    {
        // Garante que estamos sempre editando o mesmo registro de configuração da página
        $conteudo = SobreNos::firstOrCreate(['id' => 1]);

        // Atualiza apenas os campos de texto enviados
        $conteudo->update($request->only([
            'titulo_header',
            'texto_1',
            'texto_2'
        ]));

        return redirect()->back()->with('sucesso', 'Texto atualizado com sucesso!');
    }

    /**
     * Gerencia o upload das fotos individualmente.
     * @param int $id Corresponde ao slot da imagem (1 ou 2).
     */
    public function updateFoto(Request $request, $id)
    {
        $conteudo = SobreNos::firstOrCreate(['id' => 1]);
        $campo = "imagem_" . $id; // Dinamicamente define imagem_1 ou imagem_2

        if ($request->hasFile('foto')) {

            // Validação básica para garantir que é uma imagem
            $request->validate([
                'foto' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
            ]);

            // Deleta a imagem antiga do storage se ela existir
            if ($conteudo->$campo && Storage::disk('public')->exists($conteudo->$campo)) {
                Storage::disk('public')->delete($conteudo->$campo);
            }

            // Salva a nova imagem na pasta 'sobre' e atualiza o caminho no banco
            $path = $request->file('foto')->store('sobre', 'public');
            $conteudo->$campo = $path;
            $conteudo->save();
        }

        return redirect()->back()->with('sucesso', 'Imagem atualizada com sucesso!');
    }

    public function show()
    {
        // Busca os dados no banco
        $conteudo = SobreNos::first() ?? new SobreNos();

        // Retorna a view pública (a que não tem os forms de edição)
        return view('sobre', compact('conteudo'));
    }
}
