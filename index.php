<?php
// index.php - Controller principale aggiornato

// Verifica che ci sia una richiesta POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Controlla se i dati sono stati inviati tramite form o direttamente come JSON
    if (isset($_POST['json_data'])) {
        $jsonData = $_POST['json_data'];
    } elseif (isset($_POST['character-json'])) {
        $jsonData = $_POST['character-json'];
    } else {
        // Ottieni il JSON dalla richiesta POST (raw data)
        $jsonData = file_get_contents('php://input');
    }

    // Tracking statistiche per generazione manuale
    require_once 'includes/StatsService.php';
    $startTime = microtime(true);
    $statsService = new StatsService();

    try {
        // Decodifica il JSON
        $characterData = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);

        // Verifica errori nella decodifica
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON non valido: ' . json_last_error_msg());
        }

        $processingTime = round((microtime(true) - $startTime) * 1000);

        // Log statistiche successo
        $statsService->logGeneration([
                'generation_type' => 'manual',
                'character_class' => $characterData['class'] ?? null,
                'character_level' => $characterData['level'] ?? null,
                'character_race' => $characterData['race'] ?? null,
                'is_spellcaster' => $characterData['isSpellcaster'] ?? false,
                'processing_time_ms' => $processingTime,
                'success' => true,
                'description_length' => strlen($jsonData)
        ]);

        // Includi le classi necessarie
        require_once 'includes/Character.php';
        require_once 'includes/Spellcaster.php';
        require_once 'includes/Renderer.php';

        // Crea l'oggetto personaggio
        $character = new Character($characterData);

        // Verifica se è un lanciatore di incantesimi
        $isSpellcaster = $character->isSpellcaster();

        // Crea il renderer
        $renderer = new Renderer();

        // Visualizza l'header della pagina
        include 'templates/header.php';

        // Visualizza la scheda del personaggio
        echo $renderer->renderCharacterSheet($character);

        // Se è un lanciatore di incantesimi, visualizza anche la scheda degli incantesimi
        if ($isSpellcaster) {
            $spellcaster = new Spellcaster($characterData);
            echo $renderer->renderSpellSheet($spellcaster, $character);
        }

        // Visualizza il footer della pagina
        include 'templates/footer.php';

    } catch (Exception $e) {
        $processingTime = round((microtime(true) - $startTime) * 1000);

        // Log statistiche errore
        $statsService->logGeneration([
                'generation_type' => 'manual',
                'processing_time_ms' => $processingTime,
                'success' => false,
                'error_message' => $e->getMessage(),
                'description_length' => strlen($jsonData ?? '')
        ]);

        include 'templates/header.php';
        echo '<div class="error-container">
                <div class="error-card">
                    <div class="error-icon">⚠️</div>
                    <h2>Errore nella Generazione</h2>
                    <p class="error-message">' . htmlspecialchars($e->getMessage()) . '</p>
                    <div class="error-actions">
                        <a href="index.php" class="btn-primary">🔄 Torna al Form</a>
                    </div>
                </div>
              </div>';
        include 'templates/footer.php';
    }

} else {
    // Se non è una richiesta POST, mostra il form unificato
    include 'templates/header.php';

    // Gestisci eventuali errori dalla generazione AI
    if (isset($_GET['error'])) {
        echo '<div class="error-banner">
                <div class="container">
                    <div class="error-message">
                        <span class="error-icon">⚠️</span>
                        <div class="error-text">
                            <strong>Errore nella generazione AI:</strong>
                            <span>' . htmlspecialchars(urldecode($_GET['error'])) . '</span>
                        </div>
                    </div>
                </div>
              </div>';
    }

    include 'templates/unified_form.php'; // Usa il nuovo form unificato
    include 'templates/footer.php';
}
?>

<style>
    .error-banner {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 15px 0;
        margin-bottom: 20px;
    }

    .error-message {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .error-icon {
        font-size: 1.5rem;
    }

    .error-text strong {
        display: block;
        margin-bottom: 5px;
    }
</style>