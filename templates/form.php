<!-- Form per l'inserimento dei dati JSON del personaggio -->
<div class="container">
    <div class="form-header">
        <h1>Generatore Schede D&D 5e</h1>
        <p class="subtitle">Crea schede personaggio professionali pronte per la stampa</p>
    </div>

    <!-- Sezione ChatGPT Prompt -->
    <div class="chatgpt-section">
        <h2>Generazione Assistita con ChatGPT</h2>
        <p>Per creare rapidamente il JSON del tuo personaggio, copia questo prompt e usalo con ChatGPT:</p>

        <div class="prompt-container">
            <div class="prompt-header">
                <h3>Prompt per ChatGPT</h3>
                <button class="copy-btn" onclick="copyPrompt()" title="Copia prompt">📋 Copia</button>
            </div>
            <div class="prompt-box" id="chatgpt-prompt">
                <pre>Crea un JSON per un personaggio D&D 5e seguendo esattamente questa struttura. Compila tutti i campi con dati appropriati per un personaggio [DESCRIVI QUI IL TUO PERSONAGGIO - es: "Guerriero Umano di livello 3, veterano di guerra con personalità stoica"].

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
- Calcola correttamente modificatori, CA, HP e bonus in base alle regole D&D 5e
- Assegna equipment appropriato per classe e background
- Crea attacks realistici con bonus e danni corretti
- Scrivi personality in italiano con dettagli interessanti</pre>
            </div>
        </div>

        <div class="instructions">
            <h3>Come usare:</h3>
            <ol>
                <li><strong>Copia il prompt</strong> cliccando sul pulsante sopra</li>
                <li><strong>Sostituisci la parte tra parentesi</strong> con la descrizione del tuo personaggio</li>
                <li><strong>Incolla in ChatGPT</strong> e invia la richiesta</li>
                <li><strong>Copia il JSON generato</strong> e incollalo nel form qui sotto</li>
                <li><strong>Genera la scheda</strong> e stampa!</li>
            </ol>
        </div>

        <div class="examples">
            <h3>Esempi di descrizioni:</h3>
            <div class="example-grid">
                <div class="example-card">
                    <strong>Guerriero:</strong><br>
                    "Guerriero Umano di livello 3, veterano di guerra con personalità stoica e specializzato in armi a due mani"
                </div>
                <div class="example-card">
                    <strong>Mago:</strong><br>
                    "Mago Elfo di livello 4, studioso di antica magia arcana, specializzato in incantesimi di evocazione"
                </div>
                <div class="example-card">
                    <strong>Ladro:</strong><br>
                    "Ladro Halfling di livello 2, orfano di strada agile e furbo, esperto in furtività e raggiri"
                </div>
                <div class="example-card">
                    <strong>Chierico:</strong><br>
                    "Chierico Nano di livello 1, devoto del dio della forgia, dominio della Vita, background accolito"
                </div>
            </div>
        </div>
    </div>

    <!-- Sezione Form JSON -->
    <div class="json-form-section">
        <h2>Inserisci i Dati del Personaggio</h2>
        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="json_data">
                    <strong>JSON del Personaggio:</strong>
                    <small>Incolla qui il JSON generato da ChatGPT o creato manualmente</small>
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

    <!-- Sezione Template -->
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
                <code>curl -X POST -H "Content-Type: application/json" -d @personaggio.json <?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?></code>
            </div>
        </div>
    </div>
</div>

<style>
    /* Dark Theme per Form */
    body {
        background: linear-gradient(135deg, #0c0c0c 0%, #1a1a1a 50%, #2d2d30 100%);
        min-height: 100vh;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        color: #e8e8e8;
    }

    .form-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 40px 0;
    }

    .form-header h1 {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 700;
    }

    .subtitle {
        color: #b3b3b3;
        font-size: 18px;
        font-weight: 300;
    }

    .chatgpt-section, .json-form-section, .template-section {
        background: rgba(40, 40, 40, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    .chatgpt-section h2, .json-form-section h2, .template-section h2 {
        color: #ffffff;
        font-size: 1.8rem;
        margin-bottom: 20px;
        font-weight: 600;
        position: relative;
    }

    .chatgpt-section h2::after, .json-form-section h2::after, .template-section h2::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }

    .prompt-container {
        background: rgba(20, 20, 20, 0.9);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        overflow: hidden;
        margin-bottom: 25px;
    }

    .prompt-header {
        background: rgba(60, 60, 60, 0.6);
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .prompt-header h3 {
        margin: 0;
        color: #ffffff;
        font-size: 16px;
        font-weight: 500;
    }

    .copy-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .copy-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .prompt-box {
        max-height: 400px;
        overflow-y: auto;
        padding: 20px;
    }

    .prompt-box::-webkit-scrollbar {
        width: 8px;
    }

    .prompt-box::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
    }

    .prompt-box::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }

    .prompt-box::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .prompt-box pre {
        margin: 0;
        font-family: 'Fira Code', 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.5;
        white-space: pre-wrap;
        color: #e8e8e8;
    }

    .instructions {
        background: rgba(102, 126, 234, 0.1);
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 25px;
        border: 1px solid rgba(102, 126, 234, 0.3);
    }

    .instructions h3 {
        margin-top: 0;
        margin-bottom: 15px;
        color: #ffffff;
        font-weight: 600;
    }

    .instructions ol {
        margin: 0;
        padding-left: 20px;
        color: #d1d1d1;
    }

    .instructions li {
        margin-bottom: 10px;
        line-height: 1.6;
    }

    .instructions strong {
        color: #ffffff;
    }

    .examples h3 {
        margin-bottom: 20px;
        color: #ffffff;
        font-weight: 600;
    }

    .example-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .example-card {
        background: rgba(60, 60, 60, 0.4);
        padding: 20px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 14px;
        line-height: 1.5;
        transition: all 0.3s ease;
    }

    .example-card:hover {
        background: rgba(60, 60, 60, 0.6);
        transform: translateY(-2px);
    }

    .example-card strong {
        color: #667eea;
        font-weight: 600;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        color: #ffffff;
        font-size: 16px;
    }

    .form-group label small {
        display: block;
        font-weight: 400;
        color: #b3b3b3;
        margin-top: 5px;
        font-size: 14px;
    }

    #json_data {
        width: 100%;
        padding: 20px;
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        font-family: 'Fira Code', 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.5;
        resize: vertical;
        min-height: 350px;
        background: rgba(20, 20, 20, 0.8);
        color: #e8e8e8;
        box-sizing: border-box;
    }

    #json_data:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.3);
    }

    #json_data::placeholder {
        color: #666;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 25px;
    }

    .btn-primary, .btn-secondary {
        padding: 15px 25px;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-1px);
    }

    .btn-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-block;
        background: rgba(102, 126, 234, 0.1);
    }

    .btn-link:hover {
        background: rgba(102, 126, 234, 0.2);
        transform: translateY(-1px);
    }

    .validation-message {
        margin-top: 15px;
        padding: 15px 20px;
        border-radius: 10px;
        display: none;
        font-weight: 500;
    }

    .validation-message.success {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.3);
    }

    .validation-message.error {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
        border: 1px solid rgba(220, 53, 69, 0.3);
    }

    .template-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 25px;
    }

    .info-card {
        background: rgba(60, 60, 60, 0.4);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        background: rgba(60, 60, 60, 0.6);
    }

    .info-card h3 {
        margin: 0 0 15px 0;
        color: #ffffff;
        font-weight: 600;
    }

    .info-card p {
        color: #d1d1d1;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .info-card code {
        background: rgba(20, 20, 20, 0.8);
        padding: 15px;
        border-radius: 8px;
        font-size: 12px;
        display: block;
        margin-top: 15px;
        word-break: break-all;
        color: #e8e8e8;
        font-family: 'Fira Code', 'Courier New', monospace;
        border: 1px solid rgba(255, 255, 255, 0.1);
        line-height: 1.4;
    }

    @media (max-width: 768px) {
        .container {
            padding: 15px;
        }

        .form-header h1 {
            font-size: 2rem;
        }

        .example-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .template-info {
            grid-template-columns: 1fr;
        }

        .chatgpt-section, .json-form-section, .template-section {
            padding: 20px;
        }
    }

    /* Animazioni */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chatgpt-section, .json-form-section, .template-section {
        animation: fadeInUp 0.6s ease-out;
    }

    .chatgpt-section {
        animation-delay: 0.1s;
    }

    .json-form-section {
        animation-delay: 0.2s;
    }

    .template-section {
        animation-delay: 0.3s;
    }
</style>

<script>
    function copyPrompt() {
        const promptText = document.getElementById('chatgpt-prompt').textContent;
        navigator.clipboard.writeText(promptText).then(function() {
            const btn = document.querySelector('.copy-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '✅ Copiato!';
            btn.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            }, 2000);
        }).catch(function() {
            showValidation('❌ Errore nella copia. Seleziona manualmente il testo.', 'error');
        });
    }

    function loadExample() {
        fetch('json_template.json')
            .then(response => {
                if (!response.ok) throw new Error('File non trovato');
                return response.json();
            })
            .then(data => {
                document.getElementById('json_data').value = JSON.stringify(data, null, 2);
                showValidation('✅ Esempio caricato con successo!', 'success');
            })
            .catch(error => {
                showValidation('❌ Errore nel caricamento dell\'esempio: ' + error.message, 'error');
            });
    }

    function validateJSON() {
        const jsonText = document.getElementById('json_data').value.trim();
        if (!jsonText) {
            showValidation('❌ Inserisci del JSON da validare', 'error');
            return;
        }

        try {
            const parsed = JSON.parse(jsonText);
            showValidation('✅ JSON valido! Pronto per la generazione.', 'success');
        } catch (error) {
            showValidation('❌ JSON non valido: ' + error.message, 'error');
        }
    }

    function showValidation(message, type) {
        const validation = document.getElementById('json-validation');
        validation.textContent = message;
        validation.className = 'validation-message ' + type;
        validation.style.display = 'block';
        setTimeout(() => {
            validation.style.display = 'none';
        }, 5000);
    }

    // Auto-nasconde la validazione quando si modifica il testo
    document.getElementById('json_data').addEventListener('input', function() {
        const validation = document.getElementById('json-validation');
        validation.style.display = 'none';
    });

    // Effetto focus migliorato per textarea
    document.getElementById('json_data').addEventListener('focus', function() {
        this.style.transform = 'scale(1.005)';
    });

    document.getElementById('json_data').addEventListener('blur', function() {
        this.style.transform = 'scale(1)';
    });
</script>