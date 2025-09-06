<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GeminiAIService
{
    protected $apiKey;
    protected $apiUrl;
    protected $httpClient;

    public function __construct()
    {
        $this->apiKey = config('services.gemini_ai.key');
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $this->apiKey;
        $this->httpClient = new Client();
    }

    /**
     * Menghasilkan konten dari model Gemini.
     * Mengembalikan seluruh respons API dalam bentuk array.
     *
     * @param string $prompt
     * @param array $tools
     * @return array
     */
    public function generateContent(string $prompt, array $tools = []): array
    {
        $body = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'tools' => $tools
        ];

        try {
            $response = $this->httpClient->post($this->apiUrl, ['json' => $body]);
            $data = json_decode($response->getBody(), true);
            return $data; 
        } catch (\Exception $e) {
            Log::error('Gemini API Error: ' . $e->getMessage());
            return ['error' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
}