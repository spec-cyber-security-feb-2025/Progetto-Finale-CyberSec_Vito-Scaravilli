<?php

namespace App\Livewire;

use GuzzleHttp\Client;
use Livewire\Component;
use App\Services\HttpService;

class LatestNews extends Component
{
    public $selectedApi;
    public $news;
    protected $httpService;

    public function __construct()
    {
        $this->httpService = app(HttpService::class);
    }

    public function fetchNews()
    {
        if (filter_var($this->selectedApi, FILTER_VALIDATE_URL) === FALSE) {
            $this->news = 'Invalid URL';
            return;
        }

        $this->news = json_decode($this->httpService->getRequest($this->selectedApi), true);

    }
    public function render()
    {
        return view('livewire.latest-news');
    }
}
