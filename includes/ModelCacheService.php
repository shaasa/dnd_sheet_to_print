<?php
declare(strict_types=1);

require_once __DIR__ . '/EnvLoader.php';

class ModelCacheService {
    private const CACHE_FILE = __DIR__ . '/../data/models_cache.json';
    private const CACHE_TTL_SECONDS = 30 * 24 * 60 * 60; // 30 giorni
    private const MODELS_URL = 'https://openrouter.ai/api/v1/models';

    // Modelli consigliati per generazione JSON strutturato con istruzioni complesse
    private const RECOMMENDED = [
        'meta-llama/llama-3.3-70b-instruct:free',
        'nousresearch/hermes-3-llama-3.1-405b:free',
        'qwen/qwen3-next-80b-a3b-instruct:free',
        'openai/gpt-oss-120b:free',
        'openai/gpt-oss-20b:free',
        'google/gemma-4-31b-it:free',
    ];

    private const FALLBACK_MODELS = [
        ['id' => 'meta-llama/llama-3.3-70b-instruct:free',         'name' => 'Meta Llama 3.3 70B Instruct (free)',    'recommended' => true],
        ['id' => 'nousresearch/hermes-3-llama-3.1-405b:free',      'name' => 'Hermes 3 Llama 3.1 405B (free)',       'recommended' => true],
        ['id' => 'google/gemma-4-31b-it:free',                     'name' => 'Google Gemma 4 31B (free)',             'recommended' => true],
        ['id' => 'qwen/qwen3-next-80b-a3b-instruct:free',          'name' => 'Qwen3 80B (free)',                      'recommended' => true],
        ['id' => 'meta-llama/llama-3.2-3b-instruct:free',          'name' => 'Meta Llama 3.2 3B (free)',              'recommended' => false],
    ];

    public static function getModels(): array {
        if (self::isCacheValid()) {
            $cached = self::readCache();
            if (!empty($cached)) {
                return $cached;
            }
        }

        try {
            $models = self::fetchFromApi();
            self::writeCache($models);
            return $models;
        } catch (Exception $e) {
            error_log('ModelCacheService: aggiornamento cache fallito: ' . $e->getMessage());
            // Usa cache scaduta se disponibile, altrimenti fallback
            $cached = self::readCache();
            return !empty($cached) ? $cached : self::FALLBACK_MODELS;
        }
    }

    private static function isCacheValid(): bool {
        if (!file_exists(self::CACHE_FILE)) {
            return false;
        }
        $mtime = filemtime(self::CACHE_FILE);
        return $mtime !== false && (time() - $mtime) < self::CACHE_TTL_SECONDS;
    }

    private static function readCache(): array {
        if (!file_exists(self::CACHE_FILE)) {
            return [];
        }
        $content = file_get_contents(self::CACHE_FILE);
        if ($content === false) {
            return [];
        }
        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            return $data['models'] ?? [];
        } catch (JsonException) {
            return [];
        }
    }

    private static function writeCache(array $models): void {
        $payload = json_encode(
            ['updated_at' => date('c'), 'models' => $models],
            JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
        );
        file_put_contents(self::CACHE_FILE, $payload, LOCK_EX);
    }

    private static function fetchFromApi(): array {
        EnvLoader::load();
        $apiKey = $_ENV['OPENROUTER_API_KEY'] ?? null;

        $headers = ['Content-Type: application/json'];
        if ($apiKey) {
            $headers[] = 'Authorization: Bearer ' . $apiKey;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => self::MODELS_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT      => 'PHP-DND-Generator/1.0',
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || !empty($curlError)) {
            throw new Exception('Errore cURL: ' . $curlError);
        }
        if ($httpCode !== 200) {
            throw new Exception("HTTP {$httpCode} da OpenRouter models API");
        }

        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return self::extractFreeModels($data['data'] ?? []);
    }

    private static function extractFreeModels(array $allModels): array {
        $free = [];
        foreach ($allModels as $model) {
            $promptPrice     = $model['pricing']['prompt']     ?? '1';
            $completionPrice = $model['pricing']['completion'] ?? '1';

            if ((float)$promptPrice === 0.0 && (float)$completionPrice === 0.0) {
                $id   = $model['id'];
                $free[] = [
                    'id'          => $id,
                    'name'        => $model['name'] ?? $id,
                    'recommended' => in_array($id, self::RECOMMENDED, true),
                ];
            }
        }

        usort($free, fn($a, $b) => strcmp($a['name'], $b['name']));

        return !empty($free) ? $free : self::FALLBACK_MODELS;
    }
}