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

## Utilizzo

1. **Accedi all'applicazione** tramite browser
2. **Compila il form** con i dati del personaggio:
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
