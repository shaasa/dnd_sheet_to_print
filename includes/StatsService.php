<?php
// includes/StatsService.php - Servizio per statistiche utilizzo

class StatsService {
    private $dbPath;
    private $pdo;

    public function __construct() {
        $this->dbPath = __DIR__ . '/../data/stats.db';
        $this->ensureDataDirectory();
        $this->connect();
        $this->createTables();
    }

    private function ensureDataDirectory() {
        $dataDir = dirname($this->dbPath);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
    }

    private function connect() {
        try {
            $this->pdo = new PDO('sqlite:' . $this->dbPath);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Errore connessione database statistiche: ' . $e->getMessage());
        }
    }

    private function createTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS usage_stats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            generation_type VARCHAR(20) NOT NULL, -- 'ai', 'manual', 'api'
            character_class VARCHAR(50),
            character_level INTEGER,
            character_race VARCHAR(50),
            is_spellcaster BOOLEAN DEFAULT 0,
            processing_time_ms INTEGER,
            success BOOLEAN DEFAULT 1,
            error_message TEXT,
            user_ip VARCHAR(45),
            user_agent TEXT,
            description_length INTEGER
        );
        
        CREATE INDEX IF NOT EXISTS idx_timestamp ON usage_stats(timestamp);
        CREATE INDEX IF NOT EXISTS idx_generation_type ON usage_stats(generation_type);
        CREATE INDEX IF NOT EXISTS idx_success ON usage_stats(success);
        
        CREATE TABLE IF NOT EXISTS daily_summaries (
            date DATE PRIMARY KEY,
            total_generations INTEGER DEFAULT 0,
            ai_generations INTEGER DEFAULT 0,
            manual_generations INTEGER DEFAULT 0,
            api_generations INTEGER DEFAULT 0,
            success_rate DECIMAL(5,2),
            avg_processing_time_ms INTEGER,
            unique_ips INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        ";

        $this->pdo->exec($sql);
    }

    /**
     * Registra una generazione di personaggio
     */
    public function logGeneration($data) {
        $sql = "INSERT INTO usage_stats (
            generation_type, character_class, character_level, character_race, 
            is_spellcaster, processing_time_ms, success, error_message, 
            user_ip, user_agent, description_length
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $data['generation_type'] ?? 'unknown',
            $data['character_class'] ?? null,
            $data['character_level'] ?? null,
            $data['character_race'] ?? null,
            $data['is_spellcaster'] ?? 0,
            $data['processing_time_ms'] ?? null,
            $data['success'] ?? 1,
            $data['error_message'] ?? null,
            $this->getUserIP(),
            $this->getUserAgent(),
            $data['description_length'] ?? null
        ]);

        // Aggiorna statistiche giornaliere
        $this->updateDailySummary();
    }

    /**
     * Ottieni statistiche generali
     */
    public function getOverallStats() {
        $sql = "
        SELECT 
            COUNT(*) as total_generations,
            COUNT(CASE WHEN generation_type = 'ai' THEN 1 END) as ai_generations,
            COUNT(CASE WHEN generation_type = 'manual' THEN 1 END) as manual_generations,
            COUNT(CASE WHEN generation_type = 'api' THEN 1 END) as api_generations,
            COUNT(CASE WHEN success = 1 THEN 1 END) * 100.0 / COUNT(*) as success_rate,
            AVG(processing_time_ms) as avg_processing_time,
            COUNT(DISTINCT user_ip) as unique_users,
            COUNT(CASE WHEN is_spellcaster = 1 THEN 1 END) as spellcaster_count
        FROM usage_stats
        WHERE timestamp >= date('now', '-30 days')";

        return $this->pdo->query($sql)->fetch();
    }

    /**
     * Statistiche per classe di personaggio
     */
    public function getClassStats($days = 30) {
        $sql = "
        SELECT 
            character_class,
            COUNT(*) as count,
            COUNT(CASE WHEN success = 1 THEN 1 END) * 100.0 / COUNT(*) as success_rate
        FROM usage_stats 
        WHERE timestamp >= date('now', '-{$days} days')
        AND character_class IS NOT NULL
        GROUP BY character_class 
        ORDER BY count DESC
        LIMIT 10";

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Statistiche giornaliere
     */
    public function getDailyStats($days = 7) {
        $sql = "
        SELECT 
            date(timestamp) as date,
            COUNT(*) as total,
            COUNT(CASE WHEN generation_type = 'ai' THEN 1 END) as ai_count,
            COUNT(CASE WHEN generation_type = 'manual' THEN 1 END) as manual_count,
            AVG(processing_time_ms) as avg_time
        FROM usage_stats 
        WHERE timestamp >= date('now', '-{$days} days')
        GROUP BY date(timestamp) 
        ORDER BY date DESC";

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Statistiche errori più comuni
     */
    public function getErrorStats($days = 30) {
        $sql = "
        SELECT 
            error_message,
            COUNT(*) as count,
            COUNT(*) * 100.0 / (SELECT COUNT(*) FROM usage_stats WHERE success = 0 AND timestamp >= date('now', '-{$days} days')) as percentage
        FROM usage_stats 
        WHERE success = 0 
        AND timestamp >= date('now', '-{$days} days')
        AND error_message IS NOT NULL
        GROUP BY error_message 
        ORDER BY count DESC
        LIMIT 5";

        return $this->pdo->query($sql)->fetchAll();
    }

    private function updateDailySummary() {
        $today = date('Y-m-d');

        $sql = "
        INSERT OR REPLACE INTO daily_summaries (
            date, total_generations, ai_generations, manual_generations, 
            api_generations, success_rate, avg_processing_time_ms, 
            unique_ips, updated_at
        )
        SELECT 
            date(timestamp) as date,
            COUNT(*) as total_generations,
            COUNT(CASE WHEN generation_type = 'ai' THEN 1 END) as ai_generations,
            COUNT(CASE WHEN generation_type = 'manual' THEN 1 END) as manual_generations,
            COUNT(CASE WHEN generation_type = 'api' THEN 1 END) as api_generations,
            COUNT(CASE WHEN success = 1 THEN 1 END) * 100.0 / COUNT(*) as success_rate,
            AVG(processing_time_ms) as avg_processing_time_ms,
            COUNT(DISTINCT user_ip) as unique_ips,
            CURRENT_TIMESTAMP
        FROM usage_stats 
        WHERE date(timestamp) = ?
        GROUP BY date(timestamp)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$today]);
    }

    private function getUserIP() {
        return $_SERVER['HTTP_CF_CONNECTING_IP']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_X_REAL_IP']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'unknown';
    }

    private function getUserAgent() {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }
}

