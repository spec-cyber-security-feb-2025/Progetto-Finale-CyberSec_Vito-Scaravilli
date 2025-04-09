<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limiterName = 'api'): Response
    {
        // Definisci il rate limiter in base al nome fornito
        if ($limiterName === 'articles.search') {
            // Limita le richieste a 10 al minuto per IP
            $key = 'articles-search:' . $request->ip();
            $maxAttempts = 10;
            $decayMinutes = 1;
        } elseif ($limiterName === 'careers.submit') {
            // Limita le richieste a 3 al minuto per IP
            $key = 'careers-submit:' . $request->ip();
            $maxAttempts = 3;
            $decayMinutes = 1;
        } else {
            // Rate limiter globale: 60 richieste al minuto per IP
            $key = 'global:' . $request->ip();
            $maxAttempts = 60;
            $decayMinutes = 1;
        }

        // Incrementa il contatore delle richieste
        $executed = RateLimiter::attempt(
            $key,
            $maxAttempts,
            function() {
                // Callback eseguito quando la richiesta è consentita
            },
            $decayMinutes * 60
        );

        if (!$executed) {
            // Log del tentativo di attacco
            \Illuminate\Support\Facades\Log::warning(
                'Rate limit exceeded', 
                [
                    'ip' => $request->ip(),
                    'user_id' => $request->user() ? $request->user()->id : 'guest',
                    'route' => $request->path(),
                    'limiter' => $limiterName
                ]
            );

            return response()->json([
                'message' => 'Troppe richieste. Riprova piu tardi.'
            ], 429);
        }

        // Aggiungi gli header di rate limiting alla risposta
        $response = $next($request);
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $maxAttempts),
        ]);

        return $response;
    }
}