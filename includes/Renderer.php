
<?php
// includes/renderer.php - Gestisce il rendering delle schede

class Renderer {
    public function renderCharacterSheet($character): false|string
    {
        ob_start();
        include 'templates/character-sheet.php';
        return ob_get_clean();
    }

    public function renderSpellSheet($spellcaster, $character): false|string
    {
        ob_start();
        include 'templates/spell-sheet.php';
        return ob_get_clean();
    }
}