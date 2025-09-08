<?php
// admin/stats.php - Dashboard statistiche

require_once '../includes/StatsService.php';

$statsService = new StatsService();
$overallStats = $statsService->getOverallStats();
$classStats = $statsService->getClassStats();
$dailyStats = $statsService->getDailyStats();
$errorStats = $statsService->getErrorStats();

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Statistiche - Generatore D&D</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #1a1a1a;
            color: #e8e8e8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(40, 40, 40, 0.8);
            border-radius: 12px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .stat-card h3 {
            color: #4CAF50;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .big-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        .chart-container {
            background: rgba(40, 40, 40, 0.8);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        th {
            color: #4CAF50;
            font-weight: 600;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin: 5px 0;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #45a049);
            border-radius: 10px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>📊 Statistiche Utilizzo - Generatore D&D</h1>

    <!-- Statistiche Generali -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>📈 Generazioni Totali</h3>
            <div class="big-number"><?= number_format($overallStats['total_generations']) ?></div>
            <small>Ultimi 30 giorni</small>
        </div>

        <div class="stat-card">
            <h3>🤖 Generazioni AI</h3>
            <div class="big-number"><?= number_format($overallStats['ai_generations']) ?></div>
            <small><?= round(($overallStats['ai_generations'] / max($overallStats['total_generations'], 1)) * 100, 1) ?>% del totale</small>
        </div>

        <div class="stat-card">
            <h3>✅ Tasso di Successo</h3>
            <div class="big-number"><?= round($overallStats['success_rate'], 1) ?>%</div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $overallStats['success_rate'] ?>%"></div>
            </div>
        </div>

        <div class="stat-card">
            <h3>👥 Utenti Unici</h3>
            <div class="big-number"><?= number_format($overallStats['unique_users']) ?></div>
            <small>IP distinti</small>
        </div>
    </div>

    <!-- Classi Più Popolari -->
    <div class="chart-container">
        <h3>🎭 Classi Più Popolari</h3>
        <table>
            <thead>
            <tr>
                <th>Classe</th>
                <th>Generazioni</th>
                <th>Successo</th>
                <th>Popolarità</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($classStats as $class): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($class['character_class']) ?></strong></td>
                    <td><?= $class['count'] ?></td>
                    <td><?= round($class['success_rate'], 1) ?>%</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= ($class['count'] / max(array_column($classStats, 'count'))) * 100 ?>%"></div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Statistiche Giornaliere -->
    <div class="chart-container">
        <h3>📅 Utilizzo Ultimi 7 Giorni</h3>
        <table>
            <thead>
            <tr>
                <th>Data</th>
                <th>Totale</th>
                <th>AI</th>
                <th>Manuale</th>
                <th>Tempo Medio (ms)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($dailyStats as $day): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($day['date'])) ?></td>
                    <td><strong><?= $day['total'] ?></strong></td>
                    <td><?= $day['ai_count'] ?></td>
                    <td><?= $day['manual_count'] ?></td>
                    <td><?= $day['avg_time'] ? round($day['avg_time']) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($errorStats)): ?>
        <!-- Errori Comuni -->
        <div class="chart-container">
            <h3>⚠️ Errori Più Comuni</h3>
            <table>
                <thead>
                <tr>
                    <th>Errore</th>
                    <th>Occorrenze</th>
                    <th>Percentuale</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($errorStats as $error): ?>
                    <tr>
                        <td><?= htmlspecialchars(substr($error['error_message'], 0, 100)) ?></td>
                        <td><?= $error['count'] ?></td>
                        <td><?= round($error['percentage'], 1) ?>%</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>