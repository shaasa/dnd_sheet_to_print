<?php
// includes/EnvLoader.php - Caricatore semplice per file .env

class EnvLoader {

    public static function load($filePath = null): bool
    {
        if ($filePath === null) {
            $filePath = __DIR__ . '/../.env';
        }

        if (!file_exists($filePath)) {
            // Non è un errore critico se il file .env non esiste
            // Le variabili potrebbero essere già impostate nel sistema
            return false;
        }

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

        return true;
    }
}