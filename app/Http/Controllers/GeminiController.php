<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiAIService;
use App\Services\JikanService;
use App\Services\RgbService;
use GuzzleHttp\Exception\RequestException;

class GeminiController extends Controller
{
    protected $gemini;
    protected $jikanService;
    protected $rgbService;

    public function __construct(GeminiAIService $gemini, JikanService $jikanService, RgbService $rgbService)
    {
        $this->gemini = $gemini;
        $this->jikanService = $jikanService;
        $this->rgbService = $rgbService;
    }

    public function index()
    {
        return view('ai.gemini');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000'
        ]);

        // ✅ Daftar semua tools yang tersedia
        $tools = [[
            'function_declarations' => [
                [
                    'name' => 'get_anime_full_data',
                    'description' => 'Mengambil data lengkap sebuah anime dari API Jikan menggunakan MyAnimeList ID (mal_id).',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'mal_id' => [
                                'type' => 'integer',
                                'description' => 'ID unik dari anime di MyAnimeList.'
                            ]
                        ],
                        'required' => ['mal_id']
                    ]
                ],
                [
                    'name' => 'get_rgb_data',
                    'description' => 'Mengambil data RGB dari API internal dengan parameter filter.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'month'     => ['type' => 'string', 'description' => 'Bulan dalam format YYYY-MM'],
                            'week'      => ['type' => 'string', 'description' => 'Minggu ke-berapa'],
                            'area'      => ['type' => 'string', 'description' => 'Area'],
                            'region'    => ['type' => 'string', 'description' => 'Region'],
                            'branch'    => ['type' => 'string', 'description' => 'Branch'],
                            'cluster'   => ['type' => 'string', 'description' => 'Cluster'],
                            'kabupaten' => ['type' => 'string', 'description' => 'Kabupaten'],
                            'brand'     => ['type' => 'string', 'description' => 'Brand'],
                            'notes'     => ['type' => 'string', 'description' => 'Catatan'],
                            'kategori'  => ['type' => 'string', 'description' => 'Kategori data'],
                        ]
                    ]
                ]
            ]
        ]];

        try {
            $response = $this->gemini->generateContent($request->prompt, $tools);

            $parts = $response['candidates'][0]['content']['parts'] ?? null;

            if (!$parts) {
                return response()->json([
                    'status' => 'error',
                    'prompt' => $request->prompt,
                    'result' => 'Tidak ada kandidat dari Gemini.'
                ]);
            }

            $call = $parts[0]['function_call'] ?? null;

            if ($call) {
                $functionName = $call['name'] ?? null;
                $arguments = $call['args'] ?? [];

                if (!is_array($arguments)) {
                    $arguments = json_decode($arguments, true) ?? [];
                }

                // ✅ Handler untuk Anime
                if ($functionName === 'get_anime_full_data' && isset($arguments['mal_id'])) {
                    $animeData = $this->jikanService->getAnimeFullData((int)$arguments['mal_id']);

                    if (isset($animeData['error'])) {
                        return response()->json([
                            'status' => 'error',
                            'prompt' => $request->prompt,
                            'result' => $animeData['error']
                        ]);
                    }

                    $relevantData = [
                        'title'          => $animeData['title'] ?? 'Tidak ada judul',
                        'english_title'  => $animeData['title_english'] ?? null,
                        'japanese_title' => $animeData['title_japanese'] ?? null,
                        'synopsis'       => substr($animeData['synopsis'] ?? '', 0, 500)
                            . (strlen($animeData['synopsis'] ?? '') > 500 ? '...' : ''),
                        'episodes'   => $animeData['episodes'] ?? 'Tidak ada jumlah episode',
                        'status'     => $animeData['status'] ?? null,
                        'season'     => $animeData['season'] ?? null,
                        'year'       => $animeData['year'] ?? null,
                        'score'      => $animeData['score'] ?? 'Tidak ada skor',
                        'rank'       => $animeData['rank'] ?? null,
                        'popularity' => $animeData['popularity'] ?? null,
                        'genres'     => array_map(fn($g) => $g['name'], $animeData['genres'] ?? []),
                        'studios'    => array_map(fn($s) => $s['name'], $animeData['studios'] ?? []),
                        'image'      => $animeData['images']['jpg']['large_image_url'] ?? null,
                        'trailer'    => $animeData['trailer']['url'] ?? null,
                    ];

                    $newPrompt = "Saya mengambil data anime berikut: " . json_encode($relevantData)
                        . ". Tolong buat ringkasan singkat yang ramah pengguna. Sebutkan judul, jumlah episode, skor, genre, studio, dan ringkasan cerita.";
                    $finalResponse = $this->gemini->generateContent($newPrompt);

                    $finalParts = $finalResponse['candidates'][0]['content']['parts'][0] ?? null;
                    $finalText = $finalParts['text'] ?? json_encode($relevantData);

                    return response()->json([
                        'status' => 'success',
                        'type'   => 'anime',
                        'prompt' => $request->prompt,
                        'result' => $finalText,
                        'raw'    => $relevantData
                    ]);
                }

                // ✅ Handler untuk RGB
                if ($functionName === 'get_rgb_data') {
                    $rgbData = $this->rgbService->getRgbData($arguments);

                    if (isset($rgbData['error'])) {
                        return response()->json([
                            'status' => 'error',
                            'prompt' => $request->prompt,
                            'result' => $rgbData['error']
                        ]);
                    }

                    $newPrompt = "Saya mengambil data RGB berikut: " . json_encode($rgbData)
                        . ". Tolong buat ringkasan singkat dalam bahasa Indonesia yang mudah dipahami.";
                    $finalResponse = $this->gemini->generateContent($newPrompt);

                    $finalText = $finalResponse['candidates'][0]['content']['parts'][0]['text']
                        ?? json_encode($rgbData);

                    return response()->json([
                        'status'  => 'success',
                        'type'    => 'rgb',
                        'prompt'  => $request->prompt,
                        'filters' => $arguments,
                        'result'  => $finalText,
                        'raw'     => $rgbData
                    ]);
                }
            }

            // ✅ Kalau bukan function_call, fallback ke teks biasa
            $resultText = $parts[0]['text'] ?? 'Tidak ada respons teks dari Gemini.';

            return response()->json([
                'status' => 'success',
                'type'   => 'default',
                'prompt' => $request->prompt,
                'result' => $resultText
            ]);

        } catch (RequestException $e) {
            return response()->json([
                'status' => 'error',
                'prompt' => $request->prompt,
                'result' => 'Gagal terhubung ke API Gemini / Jikan / RGB.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'prompt' => $request->prompt,
                'result' => 'Kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
