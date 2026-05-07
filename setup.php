<?php
// setup.php - Script di configurazione iniziale

echo "🎯 Setup Generatore Schede D&D 5e 2014\n";
echo "========================================\n\n";

if (!file_exists('.env')) {
    echo "📄 Creazione file .env...\n";

    if (file_exists('.env_example')) {
        copy('.env_example', '.env');
        echo "✅ File .env creato da .env_example\n";
    } else {
        file_put_contents('.env', "OPENROUTER_API_KEY=your_openrouter_api_key_here\n");
        echo "✅ File .env creato\n";
    }

    echo "\n⚠️  IMPORTANTE: Modifica il file .env e inserisci la tua OPENROUTER_API_KEY\n";
    echo "   Ottieni una chiave gratuita su: https://openrouter.ai/keys\n\n";
} else {
    echo "✅ File .env già presente\n\n";
}

require_once 'includes/OpenRouterService.php';

try {
    if (OpenRouterService::isConfigured()) {
        echo "✅ OpenRouter configurato correttamente\n";

        $service = new OpenRouterService();
        $config  = $service->getConfigInfo();
        echo "   - Modello: {$config['model']}\n";
        echo "   - Temperature: {$config['temperature']}\n";
        echo "   - Max Tokens: {$config['maxTokens']}\n";

        echo "\n🎉 Setup completato! Il generatore è pronto all'uso.\n";
        echo "   Avvia con: php -S localhost:8000\n";
    } else {
        echo "❌ OPENROUTER_API_KEY non configurata\n";
        echo "   Modifica il file .env e aggiungi la tua chiave\n";
    }
} catch (Exception $e) {
    echo "⚠️  Avvertimento: " . $e->getMessage() . "\n";
}

echo "\n";