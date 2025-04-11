<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Failed;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Helpers\AuditLogger;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Eventi di autenticazione già definiti in Laravel
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Registra listener per l'evento di login
        Event::listen(Login::class, function (Login $event) {
            AuditLogger::authLog('login', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);
        });

        // Registra listener per l'evento di logout
        Event::listen(Logout::class, function (Logout $event) {
            AuditLogger::authLog('logout', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);
        });

        // Registra listener per l'evento di registrazione
        Event::listen(Registered::class, function (Registered $event) {
            AuditLogger::authLog('register', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
            ]);
        });

        // Registra listener per l'evento di autenticazione fallita
        Event::listen(Failed::class, function (Failed $event) {
            AuditLogger::authLog('failed', [
                'email' => $event->credentials['email'] ?? 'unknown',
            ]);
        });

        // Registra listener per gli eventi di sicurezza (rate limiting)
        $this->app['events']->listen('security.rate_limit_exceeded', function ($data) {
            AuditLogger::securityLog('rate_limit_exceeded', $data);
        });

        // Registra listener per gli eventi di sicurezza (CSRF)
        $this->app['events']->listen('security.csrf_attempt', function ($data) {
            AuditLogger::securityLog('csrf_attempt', $data);
        });
    }
}