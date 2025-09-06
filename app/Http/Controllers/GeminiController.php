<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GeminiAIService;
use App\Services\JikanService;
use GuzzleHttp\Exception\RequestException;

class GeminiController extends Controller
{
    protected $gemini;
    protected $jikanService;

    public function __construct(GeminiAIService $gemini, JikanService $jikanService)
    {
        $this->gemini = $gemini;
        $this->jikanService = $jikanService;
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

        $tools = [
            [
                'function_declarations' => [
                    [
                        'name' => 'get_anime_full_data',
                        'description' => 'Mendapatkan data lengkap sebuah anime dari API Jikan. Gunakan tool ini ketika pengguna meminta informasi tentang anime menggunakan ID-nya.',
                        'parameters' => [
                            'type' => 'OBJECT',
                            'properties' => [
                                'anime_id' => [
                                    'type' => 'INTEGER',
                                    'description' => 'ID unik dari anime yang dicari.'
                                ]
                            ],
                            'required' => ['anime_id']
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = $this->gemini->generateContent($request->prompt, $tools);

            if (!isset($response['candidates'][0]['content']['parts'])) {
                return response()->json([
                    'status' => 'error',
                    'prompt' => $request->prompt,
                    'result' => 'Terjadi kesalahan pada respons API Gemini atau tidak ada kandidat yang tersedia.'
                ]);
            }

            $call = $response['candidates'][0]['content']['parts'][0]['function_call'] ?? null;

            if ($call) {
                $functionName = $call['name'];
                $arguments = (array) $call['args'];

                if ($functionName === 'get_anime_full_data' && isset($arguments['anime_id'])) {
                    $animeData = $this->jikanService->getAnimeFullData($arguments['anime_id']);

                    if (isset($animeData['error'])) {
                        return response()->json([
                            'status' => 'error',
                            'prompt' => $request->prompt,
                            'result' => $animeData['error']
                        ]);
                    }

                    $relevantData = [
                        'title' => $animeData['title'] ?? 'Tidak ada judul',
                        'synopsis' => substr($animeData['synopsis'] ?? '', 0, 500) . (strlen($animeData['synopsis'] ?? '') > 500 ? '...' : ''),
                        'episodes' => $animeData['episodes'] ?? 'Tidak ada jumlah episode',
                        'score' => $animeData['score'] ?? 'Tidak ada skor'
                    ];

                    $newPrompt = 'Saya mengambil data berikut tentang anime: ' . json_encode($relevantData) . '. Buatlah ringkasan yang ramah pengguna dari data tersebut.';
                    $finalResponse = $this->gemini->generateContent($newPrompt);
                    
                    return response()->json([
                        'status' => 'success',
                        'prompt' => $request->prompt,
                        'result' => $finalResponse['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada respons teks dari Gemini.'
                    ]);
                }
            }

            $resultText = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada respons teks dari Gemini.';
            
            return response()->json([
                'status' => 'success',
                'prompt' => $request->prompt,
                'result' => $resultText,
            ]);
        } catch (RequestException $e) {
            return response()->json([
                'status' => 'error',
                'prompt' => $request->prompt,
                'result' => 'Gagal terhubung ke API Gemini atau Jikan. Mungkin ada masalah koneksi atau waktu tunggu (timeout).'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'prompt' => $request->prompt,
                'result' => 'Terjadi kesalahan tak terduga: ' . $e->getMessage()
            ]);
        }
    }
}