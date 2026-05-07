<?php
declare(strict_types=1);

require_once __DIR__ . '/EnvLoader.php';

class OpenRouterService {
    private const API_URL = 'https://openrouter.ai/api/v1/chat/completions';

    private ?string $apiKey;
    private string $model;
    private float $temperature;
    private int $maxTokens;
    private string $siteUrl;
    private string $siteName;
    private bool $debugMode;

    public function __construct(bool $debugMode = false, ?string $model = null) {
        EnvLoader::load();

        $this->apiKey      = $_ENV['OPENROUTER_API_KEY'] ?? null;
        $this->model       = $model ?? $_ENV['OPENROUTER_MODEL'] ?? 'google/gemini-2.0-flash-exp:free';
        $this->temperature = (float)($_ENV['OPENROUTER_TEMPERATURE'] ?? 0.7);
        $this->maxTokens   = (int)($_ENV['OPENROUTER_MAX_TOKENS'] ?? 2048);
        $this->siteUrl     = $_ENV['OPENROUTER_SITE_URL'] ?? 'http://localhost';
        $this->siteName    = $_ENV['OPENROUTER_SITE_NAME'] ?? 'DnD Sheet Generator';
        $this->debugMode   = $debugMode || ($_ENV['DEBUG_MODE'] ?? 'false') === 'true';

        if (empty($this->apiKey)) {
            throw new Exception(
                'API Key di OpenRouter mancante. ' .
                'Aggiungi OPENROUTER_API_KEY nel file .env. ' .
                'Ottieni una chiave gratuita su https://openrouter.ai/keys'
            );
        }
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function generateCharacterJSON(string $description, string $scoreMethod = 'standard_array'): array {
        $prompt = $this->buildPrompt($description, $scoreMethod);

        $responseText = $this->callAPI($prompt);

        $jsonString = $this->extractJSON($responseText);

        return json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);
    }

    private function scoreMethodRule(string $method): string {
        return match($method) {
            'point_buy'      => "Metodo POINT BUY: ogni caratteristica parte da 8. Hai 27 punti da spendere con questa tabella costi: 9=1pt, 10=2pt, 11=3pt, 12=4pt, 13=5pt, 14=7pt, 15=9pt. Tutti i valori PRIMA dei bonus razziali devono essere compresi tra 8 e 15, e il totale punti spesi deve essere esattamente 27.",
            'rolled'         => "Metodo LANCIO DADI (4d6 scarta il dado più basso): i valori possono variare da 3 a 18. Scegli punteggi che sembrino naturalmente tirati, con qualche valore alto (16-18), qualche medio (12-14) e qualche più basso (8-11). Non devono essere tutti uguali né troppo bilanciati.",
            default          => "Metodo STANDARD ARRAY: devi usare ESATTAMENTE e solo i sei valori 15, 14, 13, 12, 10, 8 (prima dei bonus razziali). Ogni valore va usato una volta sola, distribuito tra le sei caratteristiche a scelta.",
        };
    }

    private function buildPrompt(string $description, string $scoreMethod = 'standard_array'): string {
        $templateJSON = file_get_contents(__DIR__ . '/../json_template.json');

        if ($templateJSON === false) {
            throw new Exception('Impossibile leggere il file json_template.json');
        }

        $scoreRule = $this->scoreMethodRule($scoreMethod);

        return "Crea un JSON per un personaggio D&D 5e 2014 seguendo esattamente questa struttura.
Compila tutti i campi con dati appropriati per questo personaggio: {$description}

Usa questa struttura JSON esatta (sostituisci solo i valori, mantieni tutti i nomi dei campi identici):

{$templateJSON}

REGOLE IMPORTANTI:
- Rispondi SOLO con il JSON valido, senza altre spiegazioni o testo
- Usa SOLO razze e classi del D&D 5e originale (qualsiasi supplemento ufficiale pubblicato prima della revisione 2024)
  * Razze legali (esempi): Umano, Elfo (Alto/Bosco/Drow), Nano (Collina/Montagna), Halfling (Piedelesto/Robusto), Dragonide, Gnomo (Bosco/Roccia), Mezzelfo, Mezzorco, Tiefling, Aarakocra, Aasimar, Firbolg, Goliath, Kenku, Lizardfolk, Tabaxi, Triton, Tortle, Changeling, Warforged, Shifter, Goblin, Hobgoblin, Bugbear, Kobold, Orco, Yuan-ti e altre razze da supplementi ufficiali pre-2024
  * Classi legali: Barbaro, Bardo, Chierico, Druido, Guerriero, Monaco, Paladino, Ranger, Ladro, Stregone, Warlock, Mago, Artefice
  * NON usare le versioni rivedute della revisione 2024 (nuovo PHB 2024)
- Se è un incantatore (Mago, Chierico, Stregone, Bardo, Druido, Paladino, Ranger, Warlock), imposta \"isSpellcaster\": true
- Usa nomi italiani per skills e saving throws (es: \"atletica\", \"furtività\", \"percezione\")
- PUNTEGGI CARATTERISTICA — {$scoreRule}
- Calcola correttamente modificatori delle caratteristiche: (punteggio - 10) / 2 arrotondato per difetto
- Calcola Classe Armatura, Punti Ferita e bonus in base alle regole D&D 5e 2014
- Assegna equipaggiamento appropriato per classe e background
- Crea attacchi realistici con bonus di attacco e danni corretti
- Scrivi personalità in italiano con dettagli interessanti e coerenti
- Per gli incantesimi usa nomi italiani (es: \"Palla di Fuoco\", \"Cura Ferite\")";
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    private function callAPI(string $prompt): string {
        $data = [
            'model'    => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $this->temperature,
            'max_tokens'  => $this->maxTokens,
        ];

        if ($this->debugMode) {
            error_log('DEBUG OPENROUTER API CALL:');
            error_log('Model: ' . $this->model);
            error_log('Request Data: ' . json_encode($data, JSON_PRETTY_PRINT));
        }

        $lastException = null;
        $maxAttempts   = 2;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return $this->doRequest($data);
            } catch (Exception $e) {
                $lastException = $e;
                if ($attempt < $maxAttempts && $this->isRetryable($e)) {
                    if ($this->debugMode) {
                        error_log("Tentativo {$attempt} fallito, retry tra 3s: " . $e->getMessage());
                    }
                    sleep(3);
                } else {
                    break;
                }
            }
        }

        throw $lastException;
    }

    private function isRetryable(Exception $e): bool {
        $msg = $e->getMessage();
        return str_contains($msg, '429') || str_contains($msg, 'Provider returned error');
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    private function doRequest(array $data): string {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => self::API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($data, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'HTTP-Referer: ' . $this->siteUrl,
                'X-Title: ' . $this->siteName,
            ],
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT      => 'PHP-DND-Generator/1.0',
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($this->debugMode) {
            error_log('HTTP Status: ' . $httpCode);
            error_log('Raw Response: ' . ($response !== false ? substr($response, 0, 500) : 'FALSE'));
        }

        if ($response === false || !empty($curlError)) {
            throw new Exception('Errore cURL verso OpenRouter: ' . $curlError);
        }

        $responseData = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (isset($responseData['error'])) {
            $msg  = $responseData['error']['message'] ?? 'Errore sconosciuto';
            $code = $responseData['error']['code'] ?? $httpCode;

            if ($code === 429 || $httpCode === 429) {
                throw new Exception("Il modello selezionato è momentaneamente sovraccarico (429). Riprova tra qualche secondo o scegli un modello diverso.");
            }

            throw new Exception("Errore API OpenRouter [{$code}]: {$msg}");
        }

        if ($httpCode !== 200) {
            throw new Exception("HTTP {$httpCode} da OpenRouter. Response: " . substr($response, 0, 300));
        }

        if (!isset($responseData['choices'][0]['message']['content'])) {
            throw new Exception('Risposta OpenRouter con struttura inaspettata: ' . substr($response, 0, 300));
        }

        return $responseData['choices'][0]['message']['content'];
    }

    /**
     * @throws Exception
     */
    private function extractJSON(string $response): string {
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/\s*```$/m', '', $response);

        preg_match('/\{[\s\S]*\}/m', $response, $matches);

        if (empty($matches)) {
            throw new Exception('Nessun JSON trovato nella risposta di OpenRouter. Response: ' . substr($response, 0, 300));
        }

        return trim($matches[0]);
    }

    public static function isConfigured(): bool {
        EnvLoader::load();
        return !empty($_ENV['OPENROUTER_API_KEY']);
    }

    public function getConfigInfo(): array {
        return [
            'model'        => $this->model,
            'temperature'  => $this->temperature,
            'maxTokens'    => $this->maxTokens,
            'apiConfigured' => !empty($this->apiKey),
            'debugMode'    => $this->debugMode,
        ];
    }

    public function testConnection(): array {
        try {
            $responseText = $this->callAPI('Rispondi solo con il JSON: {"test": "ok"}');
            return ['success' => true, 'response' => $responseText, 'config' => $this->getConfigInfo()];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage(), 'config' => $this->getConfigInfo()];
        }
    }
}