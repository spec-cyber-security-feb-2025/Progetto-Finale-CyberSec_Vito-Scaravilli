<?php

namespace App\Livewire;

use GuzzleHttp\Client;
use Livewire\Component;
use App\Services\HttpService;
use Illuminate\Support\Facades\Auth;

class LatestNews extends Component
{
    public $selectedApi;
    public $news;
    protected $httpService;
    // Chiave API per NewsAPI
    protected $newsApiKey = '5fbe92849d5648eabcbe072a1cf91473';
    // Mappa dei paesi consentiti per le API news
    protected $allowedCountries = ['it', 'gb', 'us'];

    public function __construct()
    {
        $this->httpService = app(HttpService::class);
    }

    /**
     * Genera l'URL dell'API in base al codice paese selezionato
     * 
     * @param string $countryCode
     * @return string|null
     */
    protected function generateApiUrl($countryCode)
    {
        if (in_array($countryCode, $this->allowedCountries)) {
            return "https://newsapi.org/v2/top-headlines?country={$countryCode}&apiKey={$this->newsApiKey}";
        }
        
        return null;
    }

    public function fetchNews()
    {
        // Verifica che sia stato selezionato un paese valido
        if (empty($this->selectedApi) || !in_array($this->selectedApi, $this->allowedCountries)) {
            $this->news = 'Invalid country selection';
            return;
        }

        // Genera l'URL dell'API in base al paese selezionato
        $apiUrl = $this->generateApiUrl($this->selectedApi);
        
        // Effettua la richiesta solo se l'URL è stato generato correttamente
        if ($apiUrl) {
            $this->news = json_decode($this->httpService->getRequest($apiUrl), true);
        } else {
            $this->news = 'Error generating API URL';
        }
    }
    public function render()
    {
        return view('livewire.latest-news');
    }
}
