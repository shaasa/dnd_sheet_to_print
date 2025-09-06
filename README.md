# Schedadnd - Generatore di Schede per D&D

Un'applicazione web PHP per generare e stampare schede personaggio per Dungeons & Dragons, con supporto per incantatori e gestione degli incantesimi.

## Descrizione

Schedadnd è un progetto web sviluppato in PHP che permette di:
- Creare schede personaggio personalizzate per D&D
- Gestire personaggi incantatori con liste di incantesimi
- Generare schede formattate e pronte per la stampa
- Interfaccia web intuitiva per l'inserimento dati

## Tecnologie Utilizzate

- **PHP 8.4+** - Linguaggio backend
- **HTML5** - Markup strutturale
- **CSS3** - Stili e layout responsive
- **JavaScript** - Interattività frontend


## Installazione

### Prerequisiti

- PHP 8.4 o versione superiore
- Server web (Apache/Nginx) oppure server di sviluppo PHP
- Browser web moderno

### Installazione Locale

1. **Clona il repository:**
   ```bash
   git clone <url-repository>
   cd Schedadnd
   ```

2. **Avvia il server di sviluppo PHP:**
   ```bash
   php -S localhost:8000
   ```

3. **Apri il browser e naviga su:**
   ```
   http://localhost:8000
   ```

### Installazione su Server Web

1. **Carica i file sul server:**
    - Carica tutti i file nella directory root del tuo dominio o in una sottocartella

2. **Verifica i permessi:**
   ```bash
   chmod 755 index.php
   chmod -R 755 includes/
   chmod -R 755 templates/
   ```

3. **Accedi tramite browser:**
   ```
   http://tuodominio.com/schedadnd
   ```

## Utilizzo pagina web

1. **Accedi all'applicazione** tramite browser
2. **Inserisci il JSON** con i dati del personaggio:
    - Informazioni base (nome, classe, livello, ecc.)
    - Statistiche e abilità
    - Per incantatori: aggiungi gli incantesimi
3. **Genera la scheda** cliccando sul pulsante di invio
4. **Stampa o salva** la scheda generata

## Funzionalità

- ✅ Creazione schede personaggio standard
- ✅ Supporto per classi incantatrici
- ✅ Gestione liste incantesimi
- ✅ Layout ottimizzato per la stampa
- ✅ Interfaccia responsive
- ✅ Validazione dati lato client

## 📄 Struttura JSON di Input

L'applicazione accetta i dati del personaggio tramite richieste POST in formato JSON.

### 📋 Template di Esempio

Per facilitare la creazione dei dati del personaggio, è disponibile un file template completo:

- **[`json_template.json`](./json_template.json)** - Struttura completa con tutti i campi supportati

Questo file contiene un esempio di personaggio completamente configurato con:
- ✅ Tutti i campi base del personaggio
- ✅ Struttura completa per incantatori
- ✅ Esempi di attacchi ed equipaggiamento
- ✅ Tratti della personalità
- ✅ Commenti esplicativi per ogni sezione

### 🚀 Come Utilizzare il Template

1. **Copia il file template:**
   ```bash
   cp json_template.json mio_personaggio.json
   ```

2. **Modifica i valori** nel file `mio_personaggio.json` con i dati del tuo personaggio

3. **Invia i dati** tramite:
    - **Form Web**: Copia e incolla il contenuto nel campo textarea
    - **API POST**: Usa il file JSON direttamente
    - **cURL**: `curl -X POST -d @mio_personaggio.json -H "Content-Type: application/json" http://localhost:8000/index.php`

### 📝 Campi Principali

#### Obbligatori:
- `name` - Nome del personaggio (stringa)
- `class` - Classe del personaggio (stringa)
- `level` - Livello del personaggio (numero)
- `abilities` - Oggetto con i 6 punteggi delle caratteristiche

#### Opzionali:
- `spellcasting` - Dati per incantatori (oggetto complesso)
- `attacks` - Lista attacchi (array)
- `equipment` - Equipaggiamento (array di stringhe)
- `personality` - Tratti caratteriali (oggetto)
- `features` - Caratteristiche di classe (array)

### ⚠️ Note Importanti

- Il JSON deve essere sintatticamente valido
- I valori numerici devono essere numeri, non stringhe
- I campi booleani usano `true`/`false`
- Consulta il file `json_template.json` per la struttura completa e gli esempi


## 🙏 Crediti e Riconoscimenti

### Template HTML/CSS Originale
La struttura HTML e gli stili CSS per le schede personaggio sono basati sul lavoro di:
- **Autore**: Brandon Fulljames
- **Fonte**: [D&D 5e Character Sheet](https://codepen.io/evertras/full/YVVeMd/)
- **Piattaforma**: CodePen
- **Licenza**: Utilizzato e adattato con riconoscimento

### Modifiche e Adattamenti
Il template originale è stato adattato e esteso per questo progetto con:
- ✅ Integrazione con backend PHP
- ✅ Sistema di rendering dinamico
- ✅ Supporto per schede incantatori
- ✅ Localizzazione in italiano
- ✅ Ottimizzazioni per la stampa
- ✅ Gestione dati tramite JSON

### Componenti Sviluppati
I seguenti componenti sono stati sviluppati specificamente per questo progetto:
- Sistema di classi PHP (Character, Spellcaster, Renderer)
- Template PHP dinamici
- Gestione JSON per input dati
- Scheda incantesimi personalizzata
- Script JavaScript per funzionalità aggiuntive

---

**Ringraziamo [Brandon Fulljames](https://codepen.io/evertras) per aver condiviso il suo eccellente lavoro sulla scheda D&D, che ha fornito una solida base per questo progetto.**

## Contributi

I contributi sono benvenuti! Per contribuire:

1. Fai un fork del progetto
2. Crea un branch per la tua feature (`git checkout -b feature/AmazingFeature`)
3. Commit le tue modifiche (`git commit -m 'Add some AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Apri una Pull Request

## Licenza

Questo progetto è distribuito sotto licenza MIT. Vedi il file `LICENSE` per maggiori dettagli.

## Supporto

Per supporto e segnalazioni bug, apri una issue su GitHub o contatta il team di sviluppo.

---

**Buon gioco con le tue schede D&D!**
