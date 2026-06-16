<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Add security headers without changing the local development flow.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Permite desligar todos os headers por config, caso algum ambiente precise isolar testes.
        if (!config('security.headers.enabled', true)) {
            return $response;
        }

        $headers = $response->headers;

        // Evita content sniffing no navegador, reduzindo interpretacao indevida de arquivos como script.
        if (!$headers->has('X-Content-Type-Options')) {
            $headers->set('X-Content-Type-Options', config('security.headers.content_type_options', 'nosniff'));
        }

        // Mitiga clickjacking ao controlar se a pagina pode abrir dentro de frame/iframe.
        if (!$headers->has('X-Frame-Options')) {
            $headers->set('X-Frame-Options', config('security.headers.frame_options', 'SAMEORIGIN'));
        }

        // Limita quanto do URL de origem o navegador envia para sites externos via header Referer.
        if (!$headers->has('Referrer-Policy')) {
            $headers->set('Referrer-Policy', config('security.headers.referrer_policy', 'strict-origin-when-cross-origin'));
        }

        // Desabilita APIs sensiveis do navegador por padrao, liberando apenas se o projeto realmente precisar.
        if (!$headers->has('Permissions-Policy')) {
            $headers->set('Permissions-Policy', config('security.headers.permissions_policy'));
        }

        // Define uma CSP padrao para reduzir impacto de XSS e carregamento de recursos indevidos.
        if (config('security.csp.enabled', true) && !$headers->has('Content-Security-Policy')) {
            $headers->set('Content-Security-Policy', $this->buildContentSecurityPolicy());
        }

        // HSTS so deve ser enviado em HTTPS real; em HTTP/local ele pode atrapalhar o ambiente de desenvolvimento.
        if (config('security.hsts.enabled', false) && $request->isSecure() && !$headers->has('Strict-Transport-Security')) {
            $value = 'max-age=' . (int) config('security.hsts.max_age', 31536000);

            // Estende a politica para subdominios quando o ambiente estiver pronto para isso.
            if (config('security.hsts.include_subdomains', true)) {
                $value .= '; includeSubDomains';
            }

            // Permite adesao futura a preload, mas so quando o dominio estiver totalmente preparado.
            if (config('security.hsts.preload', false)) {
                $value .= '; preload';
            }

            $headers->set('Strict-Transport-Security', $value);
        }

        return $response;
    }

    private function buildContentSecurityPolicy(): string
    {
        $policy = (string) config('security.csp.policy', '');
        $formAction = $this->buildFormActionDirectiveValue();

        if ($policy === '') {
            return "default-src 'self'; base-uri 'self'; form-action {$formAction}; frame-ancestors 'self'; object-src 'none'";
        }

        return preg_replace(
            '/base-uri\s+\'self\';/i',
            "base-uri 'self'; form-action {$formAction};",
            $policy,
            1
        ) ?? $policy;
    }

    private function buildFormActionDirectiveValue(): string
    {
        $sources = [(string) config('security.csp.form_action', "'self'")];

        foreach (config('security.csp.form_action_extra', []) as $source) {
            if (is_string($source) && $source !== '') {
                $sources[] = $source;
            }
        }

        return implode(' ', array_unique($sources));
    }
}
