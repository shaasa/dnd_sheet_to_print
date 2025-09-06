// js/script.js

document.addEventListener('DOMContentLoaded', function() {
    // Preparazione per la stampa
    function preparePrintLayout() {
        // Assicurarsi che tutto sia visualizzato correttamente per la stampa
        const sheets = document.querySelectorAll('.character-sheet, .spell-sheet');

        sheets.forEach(sheet => {
            // Regolazioni speciali prima della stampa se necessario
        });
    }

    // Ascolta SOLO il pulsante di stampa specifico (con onclick="window.print()")
    // NON più tutti i pulsanti generici

    // Ascolta anche l'evento di stampa del browser
    window.addEventListener('beforeprint', preparePrintLayout);
});