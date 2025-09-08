<?php
declare(strict_types=1);

// admin/test_gemini.php - Test connessione Gemini

require_once '../includes/GeminiService.php';

try {
    $gemini = new GeminiService(true); // Debug attivo
    $result = $gemini->testConnection();

    echo '<pre>';
    echo "🧪 TEST CONNESSIONE GEMINI\n";
    echo "===========================\n\n";

    if ($result['success']) {
        echo "✅ CONNESSIONE RIUSCITA!\n\n";
        echo "📤 Risposta ricevuta:\n" . $result['response'] . "\n\n";
    } else {
        echo "❌ CONNESSIONE FALLITA!\n\n";
        echo "🚨 Errore:\n" . $result['error'] . "\n\n";
    }

    echo "⚙️ Configurazione:\n";
    print_r($result['config']);
    echo '</pre>';

} catch (Exception $e) {
    echo '<pre>';
    echo "💥 ERRORE FATALE:\n";
    echo $e->getMessage();
    echo '</pre>';
}

