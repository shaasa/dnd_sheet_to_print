<?php
// includes/EnvLoader.php - Caricatore semplice per file .env

class EnvLoader {

    /**
     * Carica il primo .env trovato tra i percorsi candidati.
     *
     * Ordine di ricerca (quando $filePath non è specificato):
     *   1. Variabile d'ambiente APP_ENV_PATH (override esplicito)
     *   2. UN LIVELLO SOPRA la docroot — es. /var/www/vhosts/dominio/.env
     *      mentre la docroot è .../httpdocs. In produzione il file sta QUI:
     *      così non è raggiungibile via web in nessun modo, a prescindere
     *      dall'.htaccess. (vedi furto chiave del 27/06/2026)
     *   3. Dentro al progetto — usato in sviluppo locale (Herd).
     *
     * Le variabili già presenti nell'ambiente NON vengono sovrascritte,
     * quindi si possono anche impostare via env var del pool PHP-FPM.
     */
    public static function load($filePath = null): bool
    {
        $candidates = $filePath !== null
            ? [$filePath]
            : [
                getenv('APP_ENV_PATH') ?: null,   // override esplicito
                __DIR__ . '/../../.env',          // fuori dalla docroot (produzione)
                __DIR__ . '/../.env',             // dentro al progetto (sviluppo)
            ];

        foreach ($candidates as $path) {
            if ($path && is_file($path)) {
                self::parseFile($path);
                return true;
            }
        }

        // Non è un errore critico se nessun .env esiste:
        // le variabili potrebbero essere già impostate nel sistema.
        return false;
    }

    private static function parseFile(string $filePath): void
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignora commenti
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            // Cerca il pattern KEY=VALUE
            if (str_contains($line, '=')) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Rimuovi le virgolette se presenti
                if (preg_match('/^["\'](.*)["\']/s', $value, $matches)) {
                    $value = $matches[1];
                }

                // Imposta la variabile d'ambiente se non è già impostata
                if (!array_key_exists($key, $_ENV)) {
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }
}
