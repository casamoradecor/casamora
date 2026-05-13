<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Models\Produto;
use Illuminate\Support\Facades\Http;
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
    public function gerarComIA(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string'
        ]);

        $promptUsuario = $request->prompt;

        $promptCompleto = "Aja como o Diretor de Marketing da 'Casa MORÁ', uma loja de decorações elegante e premium. "
            . "Crie uma newsletter baseada neste pedido: '{$promptUsuario}'. "
            . "REGRAS OBRIGATÓRIAS:\n"
            . "1. Crie um ASSUNTO de e-mail altamente chamativo e elegante (máximo 60 caracteres).\n"
            . "2. Escreva o CONTEÚDO em HTML limpo, usando tags como <h2>, <p>, <strong> e <br>. NÃO inclua as tags <html>, <head> ou <body>.\n"
            . "3. RESPONDA EXATAMENTE NO FORMATO JSON ABAIXO, sem blocos de código markdown, sem explicações:\n"
            . '{"assunto": "Seu assunto aqui", "conteudo": "Seu html aqui"}';

        $apiKey = env('GROQ_API_KEY');

        $response = Http::timeout(15)->withToken($apiKey)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                ['role' => 'system', 'content' => 'Você é um assistente especialista em marketing de luxo que responde ESTRITAMENTE em JSON limpo.'],
                ['role' => 'user', 'content' => $promptCompleto]
            ],
            'temperature' => 0.7,
            'response_format' => ['type' => 'json_object']
        ]);

        if ($response->successful()) {
            $dados = $response->json();
            $textoGerado = $dados['choices'][0]['message']['content'] ?? '{}';
            $textoGerado = str_replace(['```json', '```html', '```'], '', $textoGerado);
            $dadosIA = json_decode(trim($textoGerado), true);

            $assunto = $dadosIA['assunto'] ?? 'Casa MORÁ: Novidades exclusivas para você';
            $conteudo = $dadosIA['conteudo'] ?? '';

            return response()->json([
                'sucesso' => true,
                'assunto' => $assunto,
                'conteudo' => $conteudo
            ]);
        }

        return response()->json([
            'sucesso' => false,
            'erro' => 'Detalhe do erro Groq: ' . $response->body()
        ], 500);
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
