<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProtectCriticalOperations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica che le operazioni critiche siano eseguite solo tramite POST
        if ($request->isMethod('get')) {
            // Registra il tentativo di accesso non autorizzato
            Log::warning('Tentativo di accesso a operazione critica tramite GET', [
                'ip' => $request->ip(),
                'user_id' => $request->user() ? $request->user()->id : 'non autenticato',
                'path' => $request->path(),
                'method' => $request->method()
            ]);
            
            // Reindirizza alla dashboard con un messaggio di errore
            return redirect()->route('admin.dashboard')
                ->with('error', 'Le operazioni critiche devono essere eseguite tramite POST per motivi di sicurezza.');
        }

        return $next($request);
    }
}