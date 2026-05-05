<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\Produto; // <--- O lugar correto é aqui no topo!
use App\Mail\NewsletterMail;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    /**
     * Lógica para o formulário público (Footer/Home)
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        Newsletter::updateOrCreate(
            ['email' => $request->email],
            ['active' => true]
        );

        return redirect()->back()->with('sucesso_newsletter', 'E-mail cadastrado com sucesso!');
    }

    /**
     * Lógica para o Painel Admin
     */
    public function adminIndex()
    {
        $inscritos = Newsletter::orderBy('created_at', 'desc')->get();
        return view('admin.newsletter.index', compact('inscritos'));
    }

    /**
     * Lógica para o disparo em massa
     */
    public function enviarEmail(Request $request)
    {
        $request->validate([
            'assunto' => 'required|string|max:255',
            'conteudo' => 'required|string'
        ]);

        // Buscamos os 4 últimos produtos para a vitrine do e-mail
        $produtosVitrine = Produto::latest()->take(4)->get();
        $emails = Newsletter::where('active', true)->pluck('email');

        foreach ($emails as $email) {
            Mail::to($email)->send(new NewsletterMail($request->assunto, $request->conteudo, $produtosVitrine));
        }

        return redirect()->back()->with('sucesso', 'Newsletter enviada com sucesso!');
    }

    /**
     * Remove um inscrito
     */
    public function destroy($id)
    {
        Newsletter::findOrFail($id)->delete();
        return redirect()->back()->with('sucesso', 'Inscrito removido com sucesso.');
    }
}
