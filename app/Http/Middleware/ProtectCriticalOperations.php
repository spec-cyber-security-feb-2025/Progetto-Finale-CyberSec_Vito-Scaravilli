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
            // Log del tentativo di accesso non autorizzato usando AuditLogger
            \App\Helpers\AuditLogger::securityLog('csrf_attempt', [
                'ip' => $request->ip(),
                'user_id' => $request->user() ? $request->user()->id : 'guest',
                'route' => $request->path(),
                'method' => $request->method()
            ]);
            
            // Emetti anche un evento per il sistema di eventi
            event('security.csrf_attempt', [
                'ip' => $request->ip(),
                'user_id' => $request->user() ? $request->user()->id : 'guest',
                'route' => $request->path(),
                'method' => $request->method()
            ]);
            
            // Reindirizza alla dashboard con un messaggio di errore
            return redirect()->route('admin.dashboard')
                ->with('error', 'Le operazioni critiche devono essere eseguite tramite POST per motivi di sicurezza.');
        }

        return $next($request);
    }
}