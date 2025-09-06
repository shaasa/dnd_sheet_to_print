<!-- templates/form.php -->
<div class="form-container">
    <h1>Generatore Scheda Personaggio D&D 5e</h1>
    <p>Invia i dati del tuo personaggio in formato JSON tramite una richiesta POST a questa pagina.</p>

    <form id="character-form" method="POST" action="">
        <div class="form-group">
            <label for="character-json">Inserisci i dati del personaggio in formato JSON:</label>
            <textarea id="character-json" name="character-json" rows="25" cols="80" required></textarea>
        </div>
        <div class="form-group">
            <button type="submit">Genera Scheda</button>
            <button type="button" id="fill-example">Usa Esempio</button>
        </div>
    </form>

    <div class="json-example-container">
        <div class="json-header">
            <h2>Esempio di formato JSON (con nomi italiani):</h2>
            <button id="copy-json" class="copy-button" title="Copia JSON negli appunti">
                📋 Copia
            </button>
        </div>
        <pre id="json-example"></pre>
    </div>

    <script>
        // Esempio completo con NOMI ITALIANI per skills e tiri salvezza
        const exampleData = {
            name: "Lior",
            class: "Chierico",
            level: 1,
            race: "Elfo del Sole",
            background: "Acolito",
            alignment: "Neutrale Buono",
            abilityScores: {
                strength: 10,
                dexterity: 14,
                constitution: 12,
                intelligence: 11,
                wisdom: 16,
                charisma: 13
            },
            hp: {
                max: 9,
                current: 9,
                temporary: 0
            },
            armorClass: 17,
            speed: 30,
            initiative: 2,
            proficiencyBonus: 2,
            inspiration: false,
            savingThrows: [
                "saggezza",
                "carisma"
            ],
            skills: [
                "intuizione",
                "religione",
                "medicina"
            ],
            languages: [
                "Comune",
                "Elfico",
                "Celestiale"
            ],
            proficiencies: [
                "Armature leggere",
                "Armature medie",
                "Scudi",
                "Armi semplici"
            ],
            equipment: [
                "Mazza",
                "Scudo",
                "Armatura a scaglie",
                "Simbolo sacro",
                "Kit da sacerdote",
                "Abiti comuni",
                "10 monete d'oro"
            ],
            attacks: [
                {
                    name: "Mazza",
                    bonus: "+2",
                    damage: "1d6+0 contundente"
                }
            ],
            features: [
                {
                    name: "Dominio della Vita",
                    description: "Aggiungi +2 + livello incantesimo a ogni incantesimo di cura."
                },
                {
                    name: "Incantesimi del Dominio",
                    description: "Benedizione, Cura Ferite sono sempre preparati."
                }
            ],
            personality: {
                traits: "Parlo con calma anche nelle situazioni più difficili.",
                ideals: "Carità. Aiutare chi ha bisogno è il mio dovere sacro.",
                bonds: "Il tempio dove ho imparato tutto è la mia vera casa.",
                flaws: "Mi fido troppo facilmente delle persone."
            },
            hitDie: 8,
            combatStatus: {
                hitDiceUsed: 0,
                deathSaves: {
                    successes: 0,
                    failures: 0
                }
            },
            isSpellcaster: true,
            spellcasting: {
                class: "Chierico",
                ability: "wisdom",
                slots: {
                    1: 2
                },
                slotsUsed: {
                    1: 0
                },
                cantrips: [
                    "Luce",
                    "Tocco Gelido",
                    "Taumaturgia"
                ],
                cantripDetails: {
                    "Luce": "Un oggetto brilla di luce per 1 ora.",
                    "Tocco Gelido": "Infligge 1d8 danni necrotici e impedisce di curarsi per 1 turno.",
                    "Taumaturgia": "Crea piccoli effetti magici come voce tonante o finestre che sbattono."
                },
                level1: [
                    "Cura Ferite",
                    "Benedizione"
                ],
                level1Details: {
                    "Cura Ferite": "Cura 1d8 + modificatore Saggezza. Tocco.",
                    "Benedizione": "Tre creature aggiungono 1d4 ai tiri per colpire e salvezza. Concentrazione."
                },
                prepared: [
                    "Cura Ferite",
                    "Benedizione"
                ]
            }
        };

        const jsonString = JSON.stringify(exampleData, null, 2);
        document.getElementById('json-example').textContent = jsonString;

        // Riempi il textarea con l'esempio quando si clicca sul pulsante
        document.getElementById('fill-example').addEventListener('click', function() {
            document.getElementById('character-json').value = jsonString;
        });

        // Copia il JSON negli appunti
        document.getElementById('copy-json').addEventListener('click', function() {
            navigator.clipboard.writeText(jsonString).then(function() {
                const button = document.getElementById('copy-json');
                const originalText = button.textContent;
                button.textContent = '✅ Copiato!';
                button.style.background = '#28a745';

                setTimeout(function() {
                    button.textContent = originalText;
                    button.style.background = '';
                }, 2000);
            }).catch(function() {
                // Fallback per browser che non supportano clipboard API
                const textArea = document.createElement('textarea');
                textArea.value = jsonString;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                const button = document.getElementById('copy-json');
                const originalText = button.textContent;
                button.textContent = '✅ Copiato!';
                button.style.background = '#28a745';

                setTimeout(function() {
                    button.textContent = originalText;
                    button.style.background = '';
                }, 2000);
            });
        });

        // Gestione dell'invio del form (SENZA apertura automatica della stampa)
        document.getElementById('character-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const jsonText = document.getElementById('character-json').value;

            // Verifica che il JSON sia valido
            try {
                JSON.parse(jsonText);
            } catch (error) {
                alert('JSON non valido: ' + error.message);
                return;
            }

            // Crea un elemento hidden per il form
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'json_data';
            input.value = jsonText;
            this.appendChild(input);

            // Invia il form SENZA aprire la stampa
            this.submit();
        });
    </script>

    <style>
        .form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            font-family: Arial, sans-serif;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            resize: vertical;
        }

        button {
            background-color: #007cba;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        button:hover {
            background-color: #005a87;
        }

        .json-example-container {
            position: relative;
        }

        .json-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .copy-button {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .copy-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .copy-button:active {
            transform: translateY(0);
        }

        pre {
            background-color: #f5f5f5;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 11px;
            line-height: 1.4;
            position: relative;
        }

        h1 {
            color: #333;
            text-align: center;
        }

        h2 {
            color: #666;
            border-bottom: 2px solid #007cba;
            padding-bottom: 5px;
            margin: 0;
        }
    </style>
</div>