<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visita;

class RegistrarVisita
{
    public function handle(Request $request, Closure $next)
    {
        // Pega o IP e a data de hoje
        $ip = $request->ip();
        $dataAtual = now()->toDateString();

        // Salva apenas se esse IP ainda não acessou o site hoje
        Visita::firstOrCreate([
            'ip_address' => $ip,
            'data_visita' => $dataAtual
        ]);

        return $next($request);
    }
}
