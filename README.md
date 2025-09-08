
# ... contenuto esistente ...

## 🤖 Configurazione AI (Google Gemini)

### Setup Rapido

1. **Copia il file di configurazione:**
   ```bash
   cp .env.example .env
   ```

2. **Ottieni una API Key gratuita:**
    - Vai su https://makersuite.google.com/app/apikey
    - Accedi con Google
    - Clicca "Create API Key"
    - Copia la chiave generata

3. **Configura il file .env:**
   ```bash
   # Modifica il file .env
   GEMINI_API_KEY=la_tua_chiave_api_qui
   ```

4. **Verifica la configurazione (opzionale):**
   ```bash
   php setup.php
   ```

### Funzionalità AI

Una volta configurato, gli utenti potranno:
- ✅ Descrivere il personaggio in linguaggio naturale
- ✅ Generare automaticamente il JSON completo
- ✅ Ottenere schede pronte per la stampa
- ✅ Usare il servizio completamente gratis (fino ai limiti di Google Gemini)

### Limiti Gratuiti Google Gemini

- **15 richieste al minuto**
- **1500 richieste al giorno**
- **1 milione di token al mese**

Per la maggior parte degli utilizzi questi limiti sono più che sufficienti!

## ... resto del README esistente ...