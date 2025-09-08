<?php
// setup.php - Script di configurazione iniziale

echo "🎯 Setup Generatore Schede D&D 5e\n";
echo "=====================================\n\n";

// Verifica se .env esiste
if (!file_exists('.env')) {
    echo "📄 Creazione file .env...\n";

    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        echo "✅ File .env creato da .env.example\n";
    } else {
        // Crea .env base
        $envContent = "# Google Gemini API Configuration\n";
        $envContent .= "GEMINI_API_KEY=your_gemini_api_key_here\n";
        $envContent .= "GEMINI_MODEL=gemini-pro\n";
        $envContent .= "GEMINI_TEMPERATURE=0.7\n";
        $envContent .= "GEMINI_MAX_TOKENS=2048\n";

        file_put_contents('.env', $envContent);
        echo "✅ File .env creato\n";
    }

    echo "\n⚠️  IMPORTANTE: Modifica il file .env e inserisci la tua API key di Google Gemini\n";
    echo "   Ottieni una chiave gratuita su: https://makersuite.google.com/app/apikey\n\n";
} else {
    echo "✅ File .env già presente\n\n";
}

// Verifica configurazione
require_once 'includes/GeminiService.php';

try {
    if (GeminiService::isConfigured()) {
        echo "✅ Google Gemini configurato correttamente\n";

        // Test della connessione (opzionale)
        $testDescription = "Guerriero Umano livello 1";
        echo "🧪 Test connessione API...\n";

        $gemini = new GeminiService();
        $config = $gemini->getConfigInfo();
        echo "   - Modello: {$config['model']}\n";
        echo "   - Temperature: {$config['temperature']}\n";
        echo "   - Max Tokens: {$config['maxTokens']}\n";

        echo "\n🎉 Setup completato! Il generatore è pronto all'uso.\n";
        echo "   Avvia con: php -S localhost:8000\n";

    } else {
        echo "❌ API Key di Google Gemini non configurata\n";
        echo "   Modifica il file .env e aggiungi la tua GEMINI_API_KEY\n";
    }
} catch (Exception $e) {
    echo "⚠️  Avvertimento: " . $e->getMessage() . "\n";
}

echo "\n";
