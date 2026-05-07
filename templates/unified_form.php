<?php
require_once __DIR__ . '/../includes/ModelCacheService.php';
require_once __DIR__ . '/../includes/EnvLoader.php';
EnvLoader::load();
$availableModels  = ModelCacheService::getModels();
$defaultModel     = $_ENV['OPENROUTER_MODEL'] ?? 'google/gemini-2.0-flash-exp:free';
?>
<!-- Form unificato con selezione del metodo di generazione -->
<link rel="stylesheet" href="css/form.css">
<div class="container">
    <div class="form-header">
        <h1>Generatore Schede D&D 5e 2014</h1>
        <p class="subtitle">Crea schede personaggio professionali pronte per la stampa</p>
    </div>

    <!-- Sezione Selezione Metodo -->
    <div class="method-selection">
        <h2>Scegli il Metodo di Generazione</h2>
        <p>Seleziona come vuoi creare il tuo personaggio D&D:</p>

        <div class="method-cards">
            <div class="method-card ai-card" onclick="showMethod('ai')">
                <div class="card-icon">🤖</div>
                <h3>Generazione Automatica</h3>
                <p>Descrivi il personaggio e lascia che l'AI generi tutto automaticamente</p>
                <ul class="features-list">
                    <li>Veloce e semplice</li>
                    <li>Completamente gratuito</li>
                    <li>Alimentato da OpenRouter</li>
                </ul>
                <button type="button" class="select-method-btn ai-btn">
                    🚀 Usa Generazione AI
                </button>
            </div>

            <div class="method-card manual-card" onclick="showMethod('manual')">
                <div class="card-icon">📝</div>
                <h3>Generazione Manuale</h3>
                <p>Usa ChatGPT per generare il JSON e poi incollalo nel form</p>
                <ul class="features-list">
                    <li>Controllo completo</li>
                    <li>Qualità ChatGPT</li>
                    <li>Flessibilità massima</li>
                </ul>
                <button type="button" class="select-method-btn manual-btn">
                    📋 Usa ChatGPT + Form
                </button>
            </div>
        </div>
    </div>

    <!-- Sezione AI (nascosta inizialmente) -->
    <div id="ai-section" class="generation-section" style="display: none;">
        <div class="section-header">
            <h2>Generazione della Scheda</h2>
            <button class="back-btn" onclick="showMethodSelection()">← Torna alla Selezione</button>
        </div>

        <div class="ai-form-container">
            <p>Descrivi il tuo personaggio in dettaglio e l'intelligenza artificiale creerà automaticamente la scheda completa pronta per la stampa:</p>

            <form method="POST" action="generate_with_ai.php" id="ai-form">
                <div class="form-group">
                    <label for="ai_model">
                        <strong>Modello AI:</strong>
                        <small>Tutti i modelli disponibili sono gratuiti</small>
                    </label>
                    <small class="model-legend">✦ consigliato per generazione schede D&D</small>
                    <select name="ai_model" id="ai_model">
                        <?php foreach ($availableModels as $model): ?>
                            <option value="<?= htmlspecialchars($model['id']) ?>"
                                <?= $model['id'] === $defaultModel ? 'selected' : '' ?>>
                                <?= ($model['recommended'] ?? false) ? '✦ ' : '' ?><?= htmlspecialchars($model['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="score_method">
                        <strong>Metodo di assegnazione caratteristiche:</strong>
                        <span class="info-tooltip">
                            <i class="fa-solid fa-circle-info info-icon"></i>
                            <span class="tooltip-content">
                                <strong>Standard Array</strong>
                                <p>Assegni liberamente i sei valori fissi 15, 14, 13, 12, 10, 8 alle caratteristiche. Ogni valore va usato una sola volta. Metodo semplice e bilanciato, ideale per chi vuole un personaggio solido senza affidarsi alla fortuna.</p>
                                <strong>Point Buy</strong>
                                <p>Tutte le caratteristiche partono da 8 e hai 27 punti da spendere. Il costo aumenta in modo non lineare: arrivare a 14 costa 7 punti, a 15 ne costa 9. Nessun valore può superare 15 prima dei bonus razziali. Metodo che garantisce il massimo controllo sulla build.</p>
                                <strong>Lancio Dadi (4d6)</strong>
                                <p>Per ogni caratteristica si lanciano 4d6 e si scarta il dado più basso, ripetendo sei volte. I valori possono variare da 3 a 18 e non saranno mai perfettamente bilanciati. Metodo più imprevedibile: può produrre personaggi eccezionali o mediocri.</p>
                            </span>
                        </span>
                    </label>
                    <select name="score_method" id="score_method">
                        <option value="standard_array">Standard Array (15, 14, 13, 12, 10, 8)</option>
                        <option value="point_buy">Point Buy (27 punti, max 15 per caratteristica)</option>
                        <option value="rolled">Lancio dadi (4d6, scarta il dado più basso)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="character_description">
                        <strong>Descrizione del Personaggio:</strong>
                        <small>Includi almeno: classe, razza, livello, background e personalità</small>
                    </label>
                    <textarea
                            name="character_description"
                            id="character_description"
                            rows="6"
                            placeholder="Es: Guerriero Umano di livello 3, veterano di guerra con personalità stoica e specializzato in armi a due mani. Background: Soldato. È coraggioso ma diffidente verso la magia..."
                            required></textarea>
                </div>

                <div class="examples-section">
                    <h4>Esempi di descrizioni:</h4>
                    <div class="example-grid">
                        <div class="example-card" onclick="useExample(this)">
                            <strong>Guerriero:</strong> Guerriero Umano di livello 3, veterano di guerra con personalità
                            stoica e specializzato in armi a due mani
                        </div>
                        <div class="example-card" onclick="useExample(this)">
                            <strong>Mago:</strong> Mago Elfo di livello 4, studioso di antica magia arcana,
                            specializzato in incantesimi di evocazione
                        </div>
                        <div class="example-card" onclick="useExample(this)">
                            <strong>Ladro:</strong> Ladro Halfling di livello 2, orfano di strada agile e furbo, esperto
                            in furtività e raggiri
                        </div>
                        <div class="example-card" onclick="useExample(this)">
                            <strong>Chierico:</strong> Chierico Nano di livello 1, devoto del dio della forgia, dominio
                            della Vita, background accolito
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary ai-generate-btn" id="generate-ai-btn">
                        <span class="btn-text">🚀 Genera Scheda Automaticamente</span>
                        <span class="spinner" style="display: none;">⏳ Generazione in corso...</span>
                    </button>
                </div>
            </form>

            <div class="ai-info">
                <small>Generazione AI gratuita via <a href="https://openrouter.ai" target="_blank" rel="noopener">OpenRouter</a></small>
            </div>
        </div>
    </div>


    <!-- Sezione Manual (nascosta inizialmente) -->
    <div id="manual-section" class="generation-section" style="display: none;">
        <div class="section-header">
            <h2>Generazione Assistita con ChatGPT</h2>
            <button class="back-btn" onclick="showMethodSelection()">← Torna alla Selezione</button>
        </div>

        <div class="manual-form-container">
            <p>Per creare rapidamente il JSON del tuo personaggio, copia questo prompt e usalo con ChatGPT:</p>

            <!-- NUOVA SEZIONE: Esempi Cliccabili per il Prompt -->
            <div class="examples-section">
                <h4>Esempi di descrizioni (clicca per inserire nel prompt):</h4>
                <div class="example-grid">
                    <div class="example-card manual-example" onclick="useExampleInPrompt(this)">
                        <strong>Guerriero:</strong> Guerriero Umano di livello 3, veterano di guerra con personalità
                        stoica e specializzato in armi a due mani
                    </div>
                    <div class="example-card manual-example" onclick="useExampleInPrompt(this)">
                        <strong>Mago:</strong> Mago Elfo di livello 4, studioso di antica magia arcana, specializzato in
                        incantesimi di evocazione
                    </div>
                    <div class="example-card manual-example" onclick="useExampleInPrompt(this)">
                        <strong>Ladro:</strong> Ladro Halfling di livello 2, orfano di strada agile e furbo, esperto in
                        furtività e raggiri
                    </div>
                    <div class="example-card manual-example" onclick="useExampleInPrompt(this)">
                        <strong>Chierico:</strong> Chierico Nano di livello 1, devoto del dio della forgia, dominio
                        della Vita, background accolito
                    </div>
                    <div class="example-card manual-example" onclick="useExampleInPrompt(this)">
                        <strong>Barbaro:</strong> Barbaro Mezz'orco di livello 2, guerriero tribale selvaggio con un'ira
                        incontrollabile
                    </div>
                    <div class="example-card manual-example" onclick="useExampleInPrompt(this)">
                        <strong>Bardo:</strong> Bardo Halfling di livello 3, musicista viaggiatore carismatico e
                        narratore di storie
                    </div>
                </div>
            </div>

            <div class="prompt-container">
                <div class="prompt-header">
                    <h3>Prompt per ChatGPT</h3>
                    <button class="copy-btn" onclick="copyPrompt()" title="Copia prompt">📋 Copia Prompt</button>
                </div>
                <div class="prompt-box" id="chatgpt-prompt">
                    <pre>Crea un JSON per un personaggio D&D 5e 2014 seguendo esattamente questa struttura. Compila tutti i campi con dati appropriati per un personaggio [DESCRIVI QUI IL TUO PERSONAGGIO - es: "Guerriero Umano di livello 3, veterano di guerra con personalità stoica"].

Usa questa struttura JSON esatta (sostituisci solo i valori, mantieni tutti i nomi dei campi identici):

{
  "name": "",
  "class": "",
  "level": 1,
  "race": "",
  "background": "",
  "alignment": "",
  "abilityScores": {
    "strength": 10,
    "dexterity": 10,
    "constitution": 10,
    "intelligence": 10,
    "wisdom": 10,
    "charisma": 10
  },
  "hp": {
    "max": 8,
    "current": 8,
    "temporary": 0
  },
  "armorClass": 10,
  "speed": 30,
  "initiative": 0,
  "proficiencyBonus": 2,
  "inspiration": false,
  "savingThrows": [],
  "skills": [],
  "languages": ["Comune"],
  "proficiencies": [],
  "equipment": [],
  "attacks": [],
  "features": [],
  "personality": {
    "traits": "",
    "ideals": "",
    "bonds": "",
    "flaws": ""
  },
  "hitDie": 8,
  "combatStatus": {
    "hitDiceUsed": 0,
    "deathSaves": {
      "successes": 0,
      "failures": 0
    }
  },
  "isSpellcaster": false,
  "spellcasting": {
    "class": "",
    "ability": "intelligence",
    "slots": {},
    "slotsUsed": {},
    "cantrips": [],
    "cantripDetails": {},
    "level1": [],
    "level1Details": {},
    "prepared": []
  }
}

IMPORTANTE:
- Se è un incantatore, imposta "isSpellcaster": true e compila la sezione spellcasting
- Usa nomi italiani per skills e saving throws (es: "forza", "saggezza", "atletica", "furtività")
- Calcola correttamente modificatori, CA, HP e bonus in base alle regole D&D 5e 2014
- Assegna equipment appropriato per classe e background
- Crea attacks realistici con bonus e danni corretti
- Scrivi personality in italiano con dettagli interessanti</pre>
                </div>
            </div>

            <div class="instructions">
                <h4>Come procedere:</h4>
                <ol>
                    <li><strong>Usa un esempio</strong> cliccando su una delle card sopra (opzionale)</li>
                    <li><strong>Copia il prompt</strong> cliccando sul pulsante "Copia Prompt"</li>
                    <li><strong>Incolla in ChatGPT</strong> e invia la richiesta</li>
                    <li><strong>Copia il JSON generato</strong> e incollalo nel form qui sotto</li>
                    <li><strong>Genera la scheda</strong> e stampa!</li>
                </ol>
            </div>

            <form method="POST" action="index.php">
                <div class="form-group">
                    <label for="json_data">
                        <strong>JSON del Personaggio:</strong>
                        <small>Incolla qui il JSON generato</small>
                    </label>
                    <textarea
                            name="json_data"
                            id="json_data"
                            rows="20"
                            placeholder="Incolla qui il JSON del tuo personaggio..."
                            required></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="loadExample()">
                        📄 Carica Esempio
                    </button>
                    <button type="button" class="btn-secondary" onclick="validateJSON()">
                        ✅ Valida JSON
                    </button>
                    <button type="submit" class="btn-primary">
                        🖨️ Genera Scheda
                    </button>
                </div>

                <div id="json-validation" class="validation-message"></div>
            </form>
        </div>
    </div>
</div>

<!-- Template Section -->
<div class="template-section">
    <h2>Template e Documentazione</h2>
    <div class="template-info">
        <div class="info-card">
            <h3>File Template</h3>
            <p>Per vedere la struttura completa del JSON con tutti i campi disponibili:</p>
            <a href="json_template.json" target="_blank" class="btn-link">
                Visualizza json_template.json
            </a>
        </div>

        <div class="info-card">
            <h3>API REST</h3>
            <p>Puoi anche inviare il JSON direttamente via API:</p>
            <code>curl -X POST -H "Content-Type: application/json" -d
                @personaggio.json <?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?></code>
        </div>
    </div>
</div>
</div>


<script src="js/formScript.js"></script>