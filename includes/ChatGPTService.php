
<?php
// includes/ChatGPTService.php - Servizio per interagire con OpenAI API

class ChatGPTService {
    private mixed $apiKey;
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    /**
     * @throws Exception
     */
    public function __construct() {
        // Carica API key da variabile d'ambiente o file di configurazione
        $this->apiKey = $_ENV['OPENAI_API_KEY'] ?? $this->loadApiKeyFromConfig();

        if (empty($this->apiKey)) {
            throw new Exception('API Key di OpenAI mancante');
        }
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function generateCharacterJSON($description) {
        $prompt = $this->buildPrompt($description);

        $response = $this->callChatGPT($prompt);

        // Estrai il JSON dalla risposta
        $jsonString = $this->extractJSON($response);

        // Valida e ritorna i dati
        $characterData = json_decode($jsonString, true, 512, JSON_THROW_ON_ERROR);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON generato non valido: ' . json_last_error_msg());
        }

        return $characterData;
    }

    private function buildPrompt($description): string
    {
        $templateJSON = file_get_contents(__DIR__ . '/../json_template.json');

        return "Crea un JSON per un personaggio D&D 5e seguendo esattamente questa struttura. 
Compila tutti i campi con dati appropriati per questo personaggio: {$description}

Usa questa struttura JSON esatta (sostituisci solo i valori, mantieni tutti i nomi dei campi identici):

{$templateJSON}

IMPORTANTE:
- Rispondi SOLO con il JSON valido, senza altre spiegazioni
- Se è un incantatore, imposta \"isSpellcaster\": true e compila la sezione spellcasting
- Usa nomi italiani per skills e saving throws
- Calcola correttamente modificatori, CA, HP e bonus in base alle regole D&D 5e
- Assegna equipment appropriato per classe e background
- Crea attacks realistici con bonus e danni corretti
- Scrivi personality in italiano con dettagli interessanti";
    }

    /**
     * @throws JsonException
     */
    private function callChatGPT($prompt) {
        $data = [
            'model' => 'gpt-3.5-turbo', // o gpt-4 per risultati migliori
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 2000,
            'temperature' => 0.7
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $this->apiKey
                ],
                'content' => json_encode($data, JSON_THROW_ON_ERROR)
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($this->apiUrl, false, $context);

        if ($response === false) {
            throw new Exception('Errore nella chiamata API di OpenAI');
        }

        $responseData = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        if (isset($responseData['error'])) {
            throw new Exception('Errore API OpenAI: ' . $responseData['error']['message']);
        }

        return $responseData['choices'][0]['message']['content'];
    }

    private function extractJSON($response): string
    {
        // Cerca il JSON nella risposta (potrebbero esserci spiegazioni extra)
        preg_match('/\{.*\}/s', $response, $matches);

        if (empty($matches)) {
            throw new Exception('Nessun JSON trovato nella risposta di ChatGPT');
        }

        return $matches[0];
    }

    private function loadApiKeyFromConfig() {
        // Carica da file di configurazione se le variabili d'ambiente non sono disponibili
        $configFile = __DIR__ . '/../config/openai.php';
        if (file_exists($configFile)) {
            $config = include $configFile;
            return $config['api_key'] ?? null;
        }
        return null;
    }
}
