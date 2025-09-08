<?php
declare(strict_types=1);

// generate_with_ai.php - Con tracking statistiche e debug dettagliato

require_once 'includes/GeminiService.php';
require_once 'includes/Character.php';
require_once 'includes/Spellcaster.php';
require_once 'includes/Renderer.php';
require_once 'includes/StatsService.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startTime = microtime(true);
    $statsService = new StatsService();

    // Abilita debug in sviluppo (rimuovi per produzione)
    $debugMode = ($_GET['debug'] ?? 'false') === 'true';

    try {
        $description = trim($_POST['character_description'] ?? '');

        if (empty($description)) {
            throw new Exception('La descrizione del personaggio è obbligatoria');
        }

        if (strlen($description) < 20) {
            throw new Exception('Descrizione troppo breve. Fornisci almeno classe, razza e livello del personaggio.');
        }

        // Genera il JSON usando Google Gemini
        $geminiService = new GeminiService($debugMode);
        $characterData = $geminiService->generateCharacterJSON($description);

        $processingTime = round((microtime(true) - $startTime) * 1000);

        // Log statistiche successo
        $statsService->logGeneration([
            'generation_type' => 'ai',
            'character_class' => $characterData['class'] ?? null,
            'character_level' => $characterData['level'] ?? null,
            'character_race' => $characterData['race'] ?? null,
            'is_spellcaster' => $characterData['isSpellcaster'] ?? false,
            'processing_time_ms' => $processingTime,
            'success' => true,
            'description_length' => strlen($description)
        ]);

        // Crea il personaggio e rendering come al solito
        $character = new Character($characterData);
        $isSpellcaster = $character->isSpellcaster();
        $renderer = new Renderer();

        // Visualizza la scheda generata
        include 'templates/header.php';

        // Mostra un messaggio di successo
        echo '<div class="ai-success-banner no-print">
                <div class="container">
                    <div class="success-message">
                        <span class="success-icon">✨</span>
                        <div class="success-text">
                            <strong>Personaggio generato con successo!</strong>
                            <small>Creato automaticamente da: "' . htmlspecialchars($description) . '"</small>
                        </div>
                    </div>
                </div>
              </div>';

        echo $renderer->renderCharacterSheet($character);

        if ($isSpellcaster) {
            $spellcaster = new Spellcaster($characterData);
            echo $renderer->renderSpellSheet($spellcaster, $character);
        }

        include 'templates/footer.php';

    } catch (Exception $e) {
        $processingTime = round((microtime(true) - $startTime) * 1000);

        // Log statistiche errore
        $statsService->logGeneration([
            'generation_type' => 'ai',
            'processing_time_ms' => $processingTime,
            'success' => false,
            'error_message' => $e->getMessage(),
            'description_length' => strlen($description ?? '')
        ]);

        // Log dettagliato per debug
        if ($debugMode) {
            error_log('🚨 ERRORE GENERAZIONE AI: ' . $e->getMessage());
            error_log('📝 Descrizione: ' . ($description ?? 'N/A'));
        }

        $errorMessage = urlencode($e->getMessage());
        header("Location: index.php?error=" . $errorMessage);
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}

// Stili per il banner di successo
echo '<style>
.ai-success-banner {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: white;
    padding: 20px 0;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

.success-message {
    display: flex;
    align-items: center;
    gap: 15px;
}

.success-icon {
    font-size: 2rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.success-text strong {
    display: block;
    font-size: 1.2rem;
    margin-bottom: 5px;
}

.success-text small {
    opacity: 0.9;
    font-style: italic;
}

@media print {
    @page {
        size: A4;
        margin: 0; /* Margini zero per stampa borderless */
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 8pt;
        line-height: 1.1;
        color: black;
        background: white;
        margin: 0;
        padding: 5mm; /* Padding interno per evitare che il contenuto tocchi i bordi */
    }

    .no-print {
        display: none !important;
    }

    .page-break {
        page-break-before: always;
    }

    form.charsheet,
    form.spellsheet {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
    }
}

</style>';
