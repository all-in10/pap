<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectFormProtectionScript
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Apenas injetar em respostas HTML do Filament
        if ($this->shouldInjectScript($request, $response)) {
            $script = $this->getFormProtectionScript();
            $response->setContent(str_replace(
                '</body>',
                $script . '</body>',
                $response->getContent()
            ));
        }

        return $response;
    }

    /**
     * Verifica se deve injetar o script
     */
    private function shouldInjectScript(Request $request, Response $response): bool
    {
        // Apenas em respostas HTML
        $contentType = $response->headers->get('Content-Type') ?? '';
        if (!str_contains($contentType, 'text/html')) {
            return false;
        }

        // Apenas em rotas do Filament
        $path = $request->path();
        if (!str_contains($path, 'admin') && 
            !str_contains($path, 'hr') && 
            !str_contains($path, 'employee')) {
            return false;
        }

        return true;
    }

    /**
     * Retorna a tag script
     */
    private function getFormProtectionScript(): string
    {
        return '<script src="' . asset('js/form-protection.js') . '"></script>' . "\n";
    }
}
