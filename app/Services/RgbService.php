<?php

namespace App\Services;

use GuzzleHttp\Client;

class RgbService
{
    protected $httpClient;
    protected $apiUrl;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->apiUrl = 'https://pinisy.telkomsel.co.id/api/mobile/rgb/area4';
    }

    /**
     * Mengambil data RGB berdasarkan parameter filter.
     *
     * @param array $params
     * @return array
     */
    public function getRgbData(array $params = [])
    {
        try {
            // Buat query string dari parameter
            $query = http_build_query($params);

            $response = $this->httpClient->get("{$this->apiUrl}?{$query}");
            $data = json_decode($response->getBody(), true);

            return $data['data'] ?? [];
        } catch (\Exception $e) {
            return ['error' => 'Tidak dapat mengambil data RGB: ' . $e->getMessage()];
        }
    }
}
