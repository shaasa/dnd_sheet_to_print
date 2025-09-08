<!-- templates/error.php - Template per la gestione errori -->
<div class="error-container">
    <div class="error-card">
        <div class="error-icon">⚠️</div>
        <h2>Errore nella Generazione</h2>
        <p class="error-message">
            <?= htmlspecialchars($e->getMessage() ?? 'Errore sconosciuto') ?>
        </p>
        <div class="error-actions">
            <a href="index.php" class="btn-primary">🔄 Torna al Form</a>
        </div>
    </div>
</div>

<style>
    .error-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 60vh;
        padding: 20px;
    }

    .error-card {
        background: rgba(220, 53, 69, 0.1);
        border: 2px solid rgba(220, 53, 69, 0.3);
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        max-width: 500px;
        width: 100%;
    }

    .error-icon {
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .error-card h2 {
        color: #dc3545;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .error-message {
        color: #e8e8e8;
        margin-bottom: 25px;
        font-size: 16px;
        line-height: 1.5;
    }

    .error-actions .btn-primary {
        text-decoration: none;
        display: inline-block;
        padding: 12px 24px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        font-weight: 600;
        transition: transform 0.3s ease;
    }

    .error-actions .btn-primary:hover {
        transform: translateY(-2px);
    }
</style>