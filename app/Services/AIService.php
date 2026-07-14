<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private const BASE_URL = 'https://openagentic.id/api/v1';

    /**
     * Urutan fallback model — digunakan dari atas ke bawah.
     */
    private const MODEL_FALLBACK = [
        'claude-sonnet-4.5',
        'claude-sonnet-4.5-1m',
        'claude-sonnet-4.5-thinking',
        'deepseek-v4-flash',
        'minimax-m2.5',
    ];

    private string $apiKey;

    public function __construct()
    {
        $key = Setting::get('ai_api_key');

        if (! $key) {
            throw new \RuntimeException('OpenAgentic API key belum dikonfigurasi di Settings.');
        }

        $this->apiKey = $key;
    }

    // ── Public API ─────────────────────────────────────────────────────────────

    /**
     * Kirim chat completion dengan fallback otomatis.
     *
     * @param  array<array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options  — temperature, max_tokens, dsb.
     * @return array{content: string, model_used: string}
     *
     * @throws \RuntimeException jika semua model gagal
     */
    public function chat(array $messages, array $options = []): array
    {
        $lastException = null;

        foreach (self::MODEL_FALLBACK as $model) {
            try {
                Log::info("[AIService] Mencoba model: {$model}");

                $response = $this->callChatEndpoint($model, $messages, $options);

                Log::info("[AIService] Berhasil dengan model: {$model}");

                return [
                    'content'    => $response,
                    'model_used' => $model,
                ];
            } catch (\Exception $e) {
                Log::warning("[AIService] Model {$model} gagal: {$e->getMessage()}");
                $lastException = $e;
                // lanjut ke model berikutnya
            }
        }

        throw new \RuntimeException(
            'Semua model AI gagal. Error terakhir: ' . $lastException?->getMessage(),
            0,
            $lastException
        );
    }

    /**
     * Helper: kirim satu pesan teks sederhana.
     *
     * @return array{content: string, model_used: string}
     */
    public function ask(string $prompt, array $options = []): array
    {
        return $this->chat([
            ['role' => 'user', 'content' => $prompt],
        ], $options);
    }

    // ── Private ────────────────────────────────────────────────────────────────

    /**
     * @throws \RuntimeException|\Illuminate\Http\Client\RequestException|ConnectionException
     */
    private function callChatEndpoint(string $model, array $messages, array $options): string
    {
        $payload = array_merge([
            'model'      => $model,
            'messages'   => $messages,
            'max_tokens' => 4096,
        ], $options);

        $response = Http::withToken($this->apiKey)
            ->timeout(30)
            ->post(self::BASE_URL . '/chat/completions', $payload);

        if ($response->serverError()) {
            throw new \RuntimeException(
                "Server error {$response->status()} dari model {$model}"
            );
        }

        if ($response->clientError()) {
            throw new \RuntimeException(
                "Client error {$response->status()}: " . $response->body()
            );
        }

        $raw  = $response->body();
        // OpenAgentic kadang tambah "data: [DONE]" di akhir body (SSE format)
        // — potong sebelum JSON parse
        $json = trim(preg_replace('/data:\s*\[DONE\]\s*$/', '', trim($raw)));
        $data = json_decode($json, true);

        $content = data_get($data, 'choices.0.message.content');

        // Beberapa thinking model (deepseek) taruh jawaban di reasoning_content
        // saat max_tokens terlalu kecil — ambil dari sana sebagai fallback
        if (empty($content)) {
            $content = data_get($data, 'choices.0.message.reasoning_content');
        }

        if (! $content) {
            throw new \RuntimeException("Respons kosong dari model {$model}");
        }

        return $content;
    }
}
