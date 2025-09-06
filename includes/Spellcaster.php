<?php
// includes/spellcaster.php - Gestisce i dati degli incantesimi

class Spellcaster {
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getSpellcastingClass() {
        return $this->data['spellcasting']['class'] ?? $this->data['class'] ?? 'Sconosciuto';
    }

    public function getSpellcastingAbility() {
        return $this->data['spellcasting']['ability'] ?? 'intelligence';
    }

    public function getSpellSaveDC($character) {
        if (!$character) {
            return 10;
        }
        $abilityMod = $character->getAbilityModifier($this->getSpellcastingAbility());
        return 8 + $character->getProficiencyBonus() + $abilityMod;
    }

    public function getSpellAttackBonus($character) {
        if (!$character) {
            return 0;
        }
        $abilityMod = $character->getAbilityModifier($this->getSpellcastingAbility());
        return $character->getProficiencyBonus() + $abilityMod;
    }

    public function getSpellSlots() {
        return $this->data['spellcasting']['slots'] ?? [];
    }

    public function getSlotsUsed() {
        return $this->data['spellcasting']['slotsUsed'] ?? [];
    }

    public function getCantrips() {
        return $this->data['spellcasting']['cantrips'] ?? [];
    }

    public function getCantripDetails() {
        return $this->data['spellcasting']['cantripDetails'] ?? [];
    }

    public function getSpellsByLevel() {
        $spells = [];
        for ($i = 1; $i <= 9; $i++) {
            $spells[$i] = $this->data['spellcasting']['level' . $i] ?? [];
        }
        return $spells;
    }

    public function getSpellDetailsByLevel() {
        $spells = [];
        for ($i = 1; $i <= 9; $i++) {
            $spells[$i] = $this->data['spellcasting']['level' . $i . 'Details'] ?? [];
        }
        return $spells;
    }

    public function getPreparedSpells() {
        return $this->data['spellcasting']['prepared'] ?? [];
    }

    public function getSpellsKnown() {
        return $this->data['spellcasting']['known'] ?? [];
    }

    public function getMaxPreparedSpells($character) {
        if (!$character) {
            return 0;
        }

        $abilityMod = $character->getAbilityModifier($this->getSpellcastingAbility());
        $level = $character->getLevel();

        return max(1, $level + $abilityMod);
    }

    // Metodo per ottenere dettagli di un singolo incantesimo
    public function getSpellDetails($spellName, $level = 0) {
        if ($level === 0) {
            $cantripDetails = $this->getCantripDetails();
            return $cantripDetails[$spellName] ?? null;
        }

        $spellDetails = $this->getSpellDetailsByLevel();
        return $spellDetails[$level][$spellName] ?? null;
    }
}