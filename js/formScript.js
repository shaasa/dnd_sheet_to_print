function showMethod(method) {
    // Nascondi la selezione metodo
    document.querySelector('.method-selection').style.display = 'none';

    // Mostra la sezione appropriata
    if (method === 'ai') {
        document.getElementById('ai-section').style.display = 'block';
        document.getElementById('manual-section').style.display = 'none';
    } else {
        document.getElementById('manual-section').style.display = 'block';
        document.getElementById('ai-section').style.display = 'none';
    }
}

function showMethodSelection() {
    // Mostra la selezione metodo
    document.querySelector('.method-selection').style.display = 'block';

    // Nascondi le sezioni
    document.getElementById('ai-section').style.display = 'none';
    document.getElementById('manual-section').style.display = 'none';
}

function useExample(element) {
    const text = element.textContent.split(': ')[1];
    document.getElementById('character_description').value = text;
    element.style.background = 'rgba(76, 175, 80, 0.3)';
    setTimeout(() => {
        element.style.background = 'rgba(60, 60, 60, 0.4)';
    }, 1000);
}

// NUOVA FUNZIONE: Per sostituire il testo nel prompt ChatGPT
function useExampleInPrompt(element) {
    const description = element.textContent.split(': ')[1];
    const promptElement = document.getElementById('chatgpt-prompt');
    const promptText = promptElement.textContent;

    // Trova e sostituisci la parte tra parentesi quadre
    const updatedPrompt = promptText.replace(
        /\[DESCRIVI QUI IL TUO PERSONAGGIO[^\]]*\]/,
        description
    );

    // Aggiorna il prompt
    promptElement.innerHTML = '<pre>' + updatedPrompt + '</pre>';

    // Feedback visivo
    element.style.background = 'rgba(102, 126, 234, 0.3)'; // Colore diverso per distinguere
    setTimeout(() => {
        element.style.background = 'rgba(60, 60, 60, 0.4)';
    }, 1500);

    // Mostra messaggio di conferma
    showValidation('Esempio inserito nel prompt! Ora copialo e usalo in ChatGPT.', 'success');

    // Scroll automatico al prompt
    promptElement.scrollIntoView({behavior: 'smooth', block: 'center'});
}

// Gestione form AI
document.addEventListener('DOMContentLoaded', function () {
    const aiForm = document.getElementById('ai-form');
    if (aiForm) {
        aiForm.addEventListener('submit', function (e) {
            const btn = document.getElementById('generate-ai-btn');
            const btnText = btn.querySelector('.btn-text');
            const spinner = btn.querySelector('.spinner');

            btnText.style.display = 'none';
            spinner.style.display = 'inline';
            btn.disabled = true;
        });
    }
});

// Funzioni esistenti di form.php
function copyPrompt() {
    const promptText = document.getElementById('chatgpt-prompt').textContent;
    navigator.clipboard.writeText(promptText).then(function () {
        const btn = document.querySelector('.copy-btn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '✅ Copiato!';
        btn.style.background = 'linear-gradient(135deg, #28a745 0%, #20c997 100%)';

        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';

            // Reset del prompt al testo originale dopo la copia
            resetPromptToOriginal();
        }, 3000);
    }).catch(function () {
        showValidation('Errore nella copia. Seleziona manualmente il testo.', 'error');
    });
}

function resetPromptToOriginal() {
    const promptElement = document.getElementById('chatgpt-prompt');
    const originalPrompt = `Crea un JSON per un personaggio D&D 5e seguendo esattamente questa struttura. Compila tutti i campi con dati appropriati per un personaggio [DESCRIVI QUI IL TUO PERSONAGGIO - es: "Guerriero Umano di livello 3, veterano di guerra con personalità stoica"].

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
- Scrivi personality in italiano con dettagli interessanti`;

    promptElement.innerHTML = '<pre>' + originalPrompt + '</pre>';
}


function loadExample() {
    fetch('json_template.json')
        .then(response => {
            if (!response.ok) throw new Error('File non trovato');
            return response.json();
        })
        .then(data => {
            document.getElementById('json_data').value = JSON.stringify(data, null, 2);
            showValidation('Esempio caricato con successo!', 'success');
        })
        .catch(error => {
            showValidation('Errore nel caricamento dell\'esempio: ' + error.message, 'error');
        });
}

function validateJSON() {
    const jsonText = document.getElementById('json_data').value.trim();
    if (!jsonText) {
        showValidation('Inserisci del JSON da validare', 'error');
        return;
    }

    try {
        const parsed = JSON.parse(jsonText);
        showValidation('JSON valido! Pronto per la generazione.', 'success');
    } catch (error) {
        showValidation('JSON non valido: ' + error.message, 'error');
    }
}

function showValidation(message, type) {
    const validation = document.getElementById('json-validation');
    if (validation) {
        validation.textContent = message;
        validation.className = 'validation-message ' + type;
        validation.style.display = 'block';
        setTimeout(() => {
            validation.style.display = 'none';
        }, 5000);
    }
}

// Auto-nasconde la validazione quando si modifica il testo
document.addEventListener('DOMContentLoaded', function () {
    const jsonData = document.getElementById('json_data');
    if (jsonData) {
        jsonData.addEventListener('input', function () {
            const validation = document.getElementById('json-validation');
            if (validation) {
                validation.style.display = 'none';
            }
        });
    }
});