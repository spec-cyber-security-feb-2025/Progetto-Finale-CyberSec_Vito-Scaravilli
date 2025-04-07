<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnlyLocalAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Definisci gli host consentiti, in produzione vanno gli IPs reali o i domini
        $allowedHosts = ['internal.admin:8000'];

        // Recupera l'host dalla richiesta
        $host = $request->header('Host');

        // Verifica se l'host è nell'elenco degli host consentiti
        if (!in_array($host, $allowedHosts)) {
            return redirect(route('homepage'))->with('alert', 'Not Authorized');
        }

        return $next($request);
    }
}
