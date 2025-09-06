
<?php
// includes/character.php - Gestisce i dati del personaggio

class Character {
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getName() {
        return $this->data['name'] ?? 'Sconosciuto';
    }

    public function getClass() {
        return $this->data['class'] ?? 'Sconosciuto';
    }

    public function getLevel() {
        return $this->data['level'] ?? 1;
    }

    public function getRace() {
        return $this->data['race'] ?? 'Sconosciuto';
    }

    public function getBackground() {
        return $this->data['background'] ?? 'Sconosciuto';
    }

    public function getAlignment() {
        return $this->data['alignment'] ?? 'Neutrale';
    }

    public function getPlayerName() {
        return $this->data['playerName'] ?? '';
    }

    public function getExperiencePoints() {
        return $this->data['experiencePoints'] ?? 0;
    }

    public function getAbilityScores() {
        return $this->data['abilityScores'] ?? [
            'strength' => 10,
            'dexterity' => 10,
            'constitution' => 10,
            'intelligence' => 10,
            'wisdom' => 10,
            'charisma' => 10
        ];
    }

    public function getAbilityModifier($ability) {
        $scores = $this->getAbilityScores();
        $score = $scores[$ability] ?? 10;
        return floor(($score - 10) / 2);
    }

    public function getProficiencyBonus() {
        return $this->data['proficiencyBonus'] ?? (ceil($this->getLevel() / 4) + 1);
    }

    public function hasInspiration() {
        return $this->data['inspiration'] ?? false;
    }

    public function getSavingThrows() {
        return $this->data['savingThrows'] ?? [];
    }

    public function getSkills() {
        return $this->data['skills'] ?? [];
    }

    public function getLanguages() {
        return $this->data['languages'] ?? [];
    }

    public function getProficiencies() {
        return $this->data['proficiencies'] ?? [];
    }

    public function getHP() {
        return [
            'max' => $this->data['hp']['max'] ?? 0,
            'current' => $this->data['hp']['current'] ?? ($this->data['hp']['max'] ?? 0),
            'temporary' => $this->data['hp']['temporary'] ?? 0
        ];
    }

    public function getArmorClass() {
        return $this->data['armorClass'] ?? 10;
    }

    public function getInitiative() {
        return $this->data['initiative'] ?? $this->getAbilityModifier('dexterity');
    }

    public function getSpeed() {
        return $this->data['speed'] ?? 30;
    }

    public function getHitDie() {
        if (isset($this->data['hitDie'])) {
            return $this->data['hitDie'];
        }

        $class = strtolower($this->getClass());
        $hitDiceByClass = [
            'barbaro' => 12,
            'guerriero' => 10,
            'paladino' => 10,
            'ranger' => 10,
            'bardo' => 8,
            'chierico' => 8,
            'druido' => 8,
            'monaco' => 8,
            'warlock' => 8,
            'mago' => 6,
            'stregone' => 6
        ];

        return $hitDiceByClass[$class] ?? 8;
    }

    public function getHitDiceUsed() {
        return $this->data['combatStatus']['hitDiceUsed'] ?? 0;
    }

    public function getDeathSaves() {
        return [
            'successes' => $this->data['combatStatus']['deathSaves']['successes'] ?? 0,
            'failures' => $this->data['combatStatus']['deathSaves']['failures'] ?? 0
        ];
    }

    public function getAttacks() {
        return $this->data['attacks'] ?? [];
    }

    public function getEquipment() {
        return $this->data['equipment'] ?? [];
    }

    public function getFeatures() {
        return $this->data['features'] ?? [];
    }

    public function getPersonality() {
        return [
            'traits' => $this->data['personality']['traits'] ?? '',
            'ideals' => $this->data['personality']['ideals'] ?? '',
            'bonds' => $this->data['personality']['bonds'] ?? '',
            'flaws' => $this->data['personality']['flaws'] ?? ''
        ];
    }

    public function isSpellcaster() {
        return (isset($this->data['spellcasting']) && !empty($this->data['spellcasting'])) ||
            (isset($this->data['isSpellcaster']) && $this->data['isSpellcaster'] === true);
    }

    public function getFullData() {
        return $this->data;
    }
}