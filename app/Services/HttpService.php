<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;

class HttpService
{
    protected $client;
    // Rimuovo internal.finance dai domini consentiti per prevenire SSRF
    protected $allowedDomains = ['newsapi.org'];
    protected $allowedProtocols = ['https']; // Consento solo HTTPS per maggiore sicurezza
    protected $refererHeader;
    protected $blockedIpRanges = [
        '10.0.0.0/8',     // Reti private classe A
        '172.16.0.0/12',  // Reti private classe B
        '192.168.0.0/16', // Reti private classe C
        '127.0.0.0/8',    // Localhost
        '0.0.0.0/8',      // Indirizzi riservati
        '169.254.0.0/16', // Link-local
    ];

    public function __construct()
    {
        $this->refererHeader = config('app.url');
        $this->client = new Client();
    }

    /**
     * Verifica se un URL è sicuro da richiedere
     * 
     * @param array $parsedUrl URL analizzato con parse_url
     * @return bool
     */
    protected function isUrlSafe($parsedUrl)
    {
        // Verifica protocollo
        if (!isset($parsedUrl['scheme']) || !in_array($parsedUrl['scheme'], $this->allowedProtocols)) {
            return false;
        }
        
        // Verifica dominio
        if (!isset($parsedUrl['host']) || !in_array($parsedUrl['host'], $this->allowedDomains)) {
            return false;
        }

        // Verifica porta (blocca porte non standard)
        if (isset($parsedUrl['port']) && $parsedUrl['port'] != 80 && $parsedUrl['port'] != 443) {
            return false;
        }

        // Verifica che l'URL non contenga credenziali
        if (isset($parsedUrl['user']) || isset($parsedUrl['pass'])) {
            return false;
        }

        // Verifica che l'host non sia un IP
        if (filter_var($parsedUrl['host'], FILTER_VALIDATE_IP)) {
            return false;
        }

        return true;
    }

    public function getRequest($url)
    {
        // Validazione di base dell'URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return 'Invalid URL format';
        }

        $parsedUrl = parse_url($url);
        
        // Verifica se l'URL è sicuro
        if (!$this->isUrlSafe($parsedUrl)) {
            return 'URL not allowed for security reasons';
        }

        // Controllo accesso a risorse interne basato sul ruolo
        if (strpos($url, 'internal.finance') !== false) {
            // Solo gli amministratori possono accedere a risorse interne
            if (!Auth::check() || !Auth::user()->is_admin) {
                return 'Access denied: insufficient privileges';
            }
        }

        // Imposta header di sicurezza
        $options['headers'] = [
            'Referer' => $this->refererHeader,
            'User-Agent' => 'CyberBlog Security Agent',
        ];

        try {
            $response = $this->client->request('GET', $url, $options);
            return $response->getBody()->getContents();
        } catch (RequestException $e) {
            return 'Something went wrong: ' . $e->getMessage();
        }
    }
}
