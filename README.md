# Generatore Schede D&D 5e

Un generatore di schede personaggio per Dungeons & Dragons 5a Edizione con interfaccia completamente in italiano, ottimizzato per la stampa e con possibilità di generazione automatica tramite intelligenza artificiale.

## Caratteristiche principali

- Doppio metodo di generazione: automatica (AI) o manuale tramite JSON
- Interfaccia e terminologia completamente in italiano (abilità, caratteristiche, tiri salvezza)
- Schede pronte per la stampa con layout professionale
- Gestione intelligente degli incantatori: la scheda incantesimi appare solo quando il personaggio è davvero un incantatore
- Statistiche d’uso integrate (successo, tempi, classi popolari)
- Possibilità di uso gratuito della generazione AI (entro i limiti del fornitore)

## Requisiti

- PHP 8.0 o superiore
- Server web oppure server integrato di PHP
- Connessione internet solo se si utilizza la generazione AI

## Installazione e avvio rapido

- Clona o scarica il progetto in locale
- (Opzionale per AI) Crea il file di configurazione e inserisci la tua chiave API di Google Gemini
- Avvia il server (anche quello integrato di PHP va bene)
- Apri il browser e visita l’indirizzo locale del server
- Esegui lo script di setup per verificare la configurazione se vuoi usare la generazione AI

## Uso dell’applicazione

- Generazione automatica (AI):
    - Seleziona la modalità AI dall’interfaccia
    - Descrivi il personaggio in linguaggio naturale (classe, razza, livello, background, personalità)
    - Avvia la generazione e attendi la scheda pronta per la stampa

- Generazione manuale:
    - Seleziona la modalità manuale
    - Genera il JSON con il tuo strumento preferito (es. ChatGPT) seguendo il template indicato nell’app
    - Incolla il JSON nell’app e genera la scheda

- Stampa:
    - Usa il pulsante “Stampa Scheda”
    - I controlli e i pulsanti vengono nascosti automaticamente durante la stampa

## Note sugli incantatori

- La scheda degli incantesimi compare solo se il personaggio è effettivamente un incantatore
- Se è presente un flag esplicito che indica che non è un incantatore, la scheda incantesimi non viene mostrata
- Se i dati degli incantesimi sono vuoti o mancanti, la scheda incantesimi non viene mostrata

## Suggerimenti per una buona generazione

- Specifica chiaramente classe, razza e livello
- Indica il background e alcuni tratti caratteriali
- Se è un incantatore, includi anche la classe di lancio e l’approccio alla magia
- Per la modalità manuale, segui la struttura JSON indicata nell’interfaccia

## Limiti del servizio AI

- La generazione automatica tramite AI utilizza un servizio esterno con limiti gratuiti giornalieri e mensili
- Se superi i limiti, puoi continuare a usare la modalità manuale senza alcuna restrizione

## Risoluzione dei problemi

- La scheda incantesimi compare quando non dovrebbe:
    - Verifica che il personaggio non sia marcato come incantatore
    - Assicurati che i dati degli incantesimi non contengano informazioni sostanziali
- Errore sul JSON:
    - Usa la funzione di validazione integrata prima di generare la scheda
    - Verifica che la struttura segua il template indicato nell’interfaccia
- Problemi di stampa:
    - Usa un browser aggiornato
    - Imposta margini ridotti o minimi
    - Controlla anteprima e orientamento del foglio
- AI non funziona:
    - Verifica la configurazione iniziale
    - Controlla di non aver superato i limiti gratuiti del servizio
    - Usa la generazione manuale in alternativa

## Statistiche

- Il sistema può raccogliere statistiche aggregate d’uso: numero di generazioni, tempi medi, tasso di successo, classi più popolari
- Esiste una pagina di consultazione dedicata alle statistiche di base

## Personalizzazione

- Puoi modificare i testi e l’aspetto dell’interfaccia
- È possibile adattare labels, traduzioni e formattazione secondo le tue preferenze
- I template di scheda possono essere personalizzati per stili e contenuti

## Roadmap (sintesi)

- Supporto avanzato per archetipi e sottoclassi
- Miglioramenti alla stampa e all’esportazione
- Opzioni di salvataggio ed export/import dei personaggi
- Localizzazione ampliata
- Ottimizzazioni lato performance e strumenti di debug

## Contribuire

- Sono benvenuti suggerimenti, segnalazioni e miglioramenti
- Mantieni uno stile chiaro, aggiorna la documentazione quando aggiungi funzionalità
- Discuti le proposte prima di grandi modifiche per allineare obiettivi e stile del progetto

## Licenza

- Il progetto è rilasciato con una licenza permissiva
- Puoi usarlo, modificarlo e distribuirlo secondo i termini della licenza inclusa nel repository

## Ringraziamenti

- Grazie alla community italiana di D&D per suggerimenti e test
- Riconoscimento ai creatori e detentori dei diritti di Dungeons & Dragons 5a Edizione
- Grazie ai servizi di AI utilizzati per la generazione automatica