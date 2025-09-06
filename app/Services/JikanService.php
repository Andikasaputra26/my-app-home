<?php

namespace App\Services;

use GuzzleHttp\Client;

class JikanService
{
    protected $httpClient;
    protected $apiUrl;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->apiUrl = 'https://api.jikan.moe/v4/anime/';
    }

    /**
     * Mengambil data lengkap sebuah anime dari API Jikan berdasarkan ID.
     *
     * @param int $animeId
     * @return array
     */
    public function getAnimeFullData(int $animeId)
    {
        try {
            $response = $this->httpClient->get("{$this->apiUrl}{$animeId}/full");
            $data = json_decode($response->getBody(), true);
            return $data['data'] ?? [];
        } catch (\Exception $e) {
            // Catat atau tangani kesalahan API.
            return ['error' => 'Tidak dapat mengambil data anime: ' . $e->getMessage()];
        }
    }
}
