<?php
// index.php - Riceve i dati del personaggio e li visualizza formattati per la stampa

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

    // Decodifica il JSON
    $characterData = json_decode($jsonData, true);

    // Verifica errori nella decodifica
    if (json_last_error() !== JSON_ERROR_NONE) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'JSON non valido: ' . json_last_error_msg()]);
        exit;
    }

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

} else {
    // Se non è una richiesta POST, mostra un form di esempio o un messaggio
    include 'templates/header.php';
    include 'templates/form.php';
    include 'templates/footer.php';
}