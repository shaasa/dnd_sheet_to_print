<?php
declare(strict_types=1);

// includes/GeminiService.php - Servizio Google Gemini corretto

require_once __DIR__ . '/EnvLoader.php';

class GeminiService {
    private ?string $apiKey;
    private string $apiUrl;
    private string $model;
    private float $temperature;
    private int $maxTokens;
    private bool $debugMode;

    public function __construct(bool $debugMode = false) {
        // Carica le variabili d'ambiente
        EnvLoader::load();

        // Configura i parametri
        $this->apiKey = $_ENV['GEMINI_API_KEY'] ?? null;
        $this->model = $_ENV['GEMINI_MODEL'] ?? 'gemini-2.0-flash'; // Usa il modello aggiornato
        $this->temperature = (float)($_ENV['GEMINI_TEMPERATURE'] ?? 0.7);
        $this->maxTokens = (int)($_ENV['GEMINI_MAX_TOKENS'] ?? 2048);
        $this->debugMode = $debugMode || ($_ENV['DEBUG_MODE'] ?? 'false') === 'true';

        // URL corretto dalla documentazione
        $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/' . $this->model . ':generateContent';

        if (empty($this->apiKey)) {
            throw new Exception(
                'API Key di Google Gemini mancante. ' .
                'Crea un file .env nella root del progetto con GEMINI_API_KEY=your_key_here. ' .
                'Ottieni una chiave gratuita su https://makersuite.google.com/app/apikey'
            );
        }
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    public function generateCharacterJSON(string $description): array {
        $prompt = $this->buildPrompt($description);

        $response = $this->callGeminiAPI($prompt);

        // Estrai il JSON dalla risposta
        $jsonString = $this->extractJSON($response);

        // Valida e ritorna i dati
        $characterData = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON generato non valido: ' . json_last_error_msg());
        }

        return $characterData;
    }

    private function buildPrompt(string $description): string {
        $templateJSON = file_get_contents(__DIR__ . '/../json_template.json');

        if ($templateJSON === false) {
            throw new Exception('Impossibile leggere il file json_template.json');
        }

        return "Crea un JSON per un personaggio D&D 5e seguendo esattamente questa struttura. 
Compila tutti i campi con dati appropriati per questo personaggio: {$description}

Usa questa struttura JSON esatta (sostituisci solo i valori, mantieni tutti i nomi dei campi identici):

{$templateJSON}

REGOLE IMPORTANTI:
- Rispondi SOLO con il JSON valido, senza altre spiegazioni o testo
- Se è un incantatore (Mago, Chierico, Stregone, Bardo, Druido, Paladino, Ranger), imposta \"isSpellcaster\": true
- Usa nomi italiani per skills e saving throws (es: \"atletica\", \"furtività\", \"percezione\")
- Calcola correttamente modificatori delle caratteristiche: (punteggio - 10) / 2 arrotondato per difetto
- Calcola Classe Armatura, Punti Ferita e bonus in base alle regole D&D 5e
- Assegna equipaggiamento appropriato per classe e background
- Crea attacchi realistici con bonus di attacco e danni corretti
- Scrivi personalità in italiano con dettagli interessanti e coerenti
- Per gli incantesimi usa nomi italiani (es: \"Palla di Fuoco\", \"Cura Ferite\")";
    }

    /**
     * @throws Exception
     * @throws JsonException
     */
    private function callGeminiAPI(string $prompt): string {
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $this->temperature,
                'topP' => 0.8,
                'maxOutputTokens' => $this->maxTokens,
            ]
        ];

        // Debug info per sviluppo
        if ($this->debugMode) {
            error_log('DEBUG GEMINI API CALL:');
            error_log('URL: ' . $this->apiUrl);
            error_log('API Key (masked): ' . $this->maskApiKey($this->apiKey));
            error_log('Model: ' . $this->model);
            error_log('Request Data: ' . json_encode($data, JSON_PRETTY_PRINT));
        }

        // Usa cURL invece di file_get_contents per migliore controllo
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data, JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-goog-api-key: ' . $this->apiKey  // Header corretto dalla documentazione
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_VERBOSE => $this->debugMode,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'PHP-DND-Generator/1.0'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlInfo = curl_getinfo($ch);

        curl_close($ch);

        if ($this->debugMode) {
            error_log('HTTP Status: ' . $httpCode);
            error_log('cURL Info: ' . json_encode($curlInfo, JSON_PRETTY_PRINT));
            error_log('Raw Response: ' . ($response !== false ? substr($response, 0, 500) . '...' : 'FALSE'));
        }

        if ($response === false || !empty($curlError)) {
            $errorContext = [
                'url' => $this->apiUrl,
                'api_key_masked' => $this->maskApiKey($this->apiKey),
                'http_code' => $httpCode,
                'curl_error' => $curlError,
                'curl_info' => $curlInfo
            ];

            throw new Exception(
                'Errore nella chiamata API di Google Gemini. ' .
                'HTTP Code: ' . $httpCode . '. ' .
                'cURL Error: ' . $curlError . '. ' .
                'Debug Info: ' . json_encode($errorContext, JSON_PRETTY_PRINT)
            );
        }

        if ($httpCode !== 200) {
            throw new Exception(
                'HTTP Error ' . $httpCode . ' dalla API Gemini. ' .
                'Response: ' . substr($response, 0, 300) . '... ' .
                'API Key masked: ' . $this->maskApiKey($this->apiKey)
            );
        }

        try {
            $responseData = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new Exception(
                'Risposta API Gemini non è JSON valido. ' .
                'Raw Response: ' . substr($response, 0, 200) . '... ' .
                'Error: ' . $e->getMessage()
            );
        }

        if ($this->debugMode) {
            error_log('Parsed Response: ' . json_encode($responseData, JSON_PRETTY_PRINT));
        }

        // Gestione errori API specifici
        if (isset($responseData['error'])) {
            $error = $responseData['error'];
            $errorMessage = $error['message'] ?? 'Errore sconosciuto';
            $errorCode = $error['code'] ?? 'unknown';
            $errorStatus = $error['status'] ?? 'unknown';

            $debugInfo = [
                'api_key_masked' => $this->maskApiKey($this->apiKey),
                'url' => $this->apiUrl,
                'model' => $this->model,
                'error_code' => $errorCode,
                'error_status' => $errorStatus,
                'full_error' => $error
            ];

            throw new Exception(
                "Errore API Gemini: {$errorMessage} (Code: {$errorCode}, Status: {$errorStatus}). " .
                "Debug Info: " . json_encode($debugInfo, JSON_PRETTY_PRINT)
            );
        }

        // Verifica struttura risposta
        if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $debugInfo = [
                'api_key_masked' => $this->maskApiKey($this->apiKey),
                'response_structure' => array_keys($responseData),
                'candidates' => $responseData['candidates'] ?? 'missing',
                'full_response' => $responseData
            ];

            throw new Exception(
                'Risposta non valida da Google Gemini - struttura inaspettata. ' .
                'Debug Info: ' . json_encode($debugInfo, JSON_PRETTY_PRINT)
            );
        }

        return $responseData['candidates'][0]['content']['parts'][0]['text'];
    }

    /**
     * @throws Exception
     */
    private function extractJSON(string $response): string {
        // Rimuovi eventuali backticks markdown
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/\s*```$/m', '', $response);

        // Cerca il JSON nella risposta
        preg_match('/\{[\s\S]*\}/m', $response, $matches);

        if (empty($matches)) {
            throw new Exception(
                'Nessun JSON trovato nella risposta di Gemini. ' .
                'Response ricevuta: ' . substr($response, 0, 300) . '...'
            );
        }

        return trim($matches[0]);
    }

    private function maskApiKey(?string $apiKey): string {
        if ($apiKey === null) {
            return 'NULL';
        }

        $length = strlen($apiKey);
        if ($length <= 8) {
            return str_repeat('*', $length);
        }

        return substr($apiKey, 0, 4) . str_repeat('*', $length - 8) . substr($apiKey, -4);
    }

    /**
     * Verifica se il servizio è configurato correttamente
     */
    public static function isConfigured(): bool {
        EnvLoader::load();
        return !empty($_ENV['GEMINI_API_KEY']);
    }

    /**
     * Ottiene informazioni sulla configurazione
     */
    public function getConfigInfo(): array {
        return [
            'model' => $this->model,
            'temperature' => $this->temperature,
            'maxTokens' => $this->maxTokens,
            'apiConfigured' => !empty($this->apiKey),
            'debugMode' => $this->debugMode,
            'apiUrl' => $this->apiUrl
        ];
    }

    /**
     * Test di connessione per debug
     */
    public function testConnection(): array {
        try {
            $testPrompt = "Rispondi solo con: {\"test\": \"ok\"}";
            $response = $this->callGeminiAPI($testPrompt);

            return [
                'success' => true,
                'response' => $response,
                'config' => $this->getConfigInfo()
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'config' => $this->getConfigInfo()
            ];
        }
    }
}