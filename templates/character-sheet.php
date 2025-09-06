<?php
// Questo template riceve $character come variabile
$abilityScores = $character->getAbilityScores();
$hp = $character->getHP();
$deathSaves = $character->getDeathSaves();

// Traduzione delle caratteristiche in italiano
$abilityTranslations = [
    'strength' => 'Forza',
    'dexterity' => 'Destrezza',
    'constitution' => 'Costituzione',
    'intelligence' => 'Intelligenza',
    'wisdom' => 'Saggezza',
    'charisma' => 'Carisma'
];

// Mappatura bidirezionale per le caratteristiche (italiano <-> inglese)
$abilityMapping = [
    'forza' => 'strength',
    'destrezza' => 'dexterity',
    'costituzione' => 'constitution',
    'intelligenza' => 'intelligence',
    'saggezza' => 'wisdom',
    'carisma' => 'charisma',
    // Anche inglese -> inglese per compatibilità
    'strength' => 'strength',
    'dexterity' => 'dexterity',
    'constitution' => 'constitution',
    'intelligence' => 'intelligence',
    'wisdom' => 'wisdom',
    'charisma' => 'charisma'
];

// Traduzione delle abilità in italiano
$skillTranslations = [
    'acrobatics' => 'Acrobazia',
    'animal handling' => 'Addestrare Animali',
    'arcana' => 'Arcano',
    'athletics' => 'Atletica',
    'deception' => 'Inganno',
    'history' => 'Storia',
    'insight' => 'Intuizione',
    'intimidation' => 'Intimidire',
    'investigation' => 'Indagare',
    'medicine' => 'Medicina',
    'nature' => 'Natura',
    'perception' => 'Percezione',
    'performance' => 'Intrattenere',
    'persuasion' => 'Persuasione',
    'religion' => 'Religione',
    'sleight of hand' => 'Rapidità di Mano',
    'stealth' => 'Furtività',
    'survival' => 'Sopravvivenza'
];

// Mappatura bidirezionale per le skills (italiano <-> inglese)
$skillMapping = [
    // Italiano -> Inglese
    'acrobazia' => 'acrobatics',
    'addestrare animali' => 'animal handling',
    'arcano' => 'arcana',
    'atletica' => 'athletics',
    'inganno' => 'deception',
    'storia' => 'history',
    'intuizione' => 'insight',
    'intimidire' => 'intimidation',
    'indagare' => 'investigation',
    'medicina' => 'medicine',
    'natura' => 'nature',
    'percezione' => 'perception',
    'intrattenere' => 'performance',
    'persuasione' => 'persuasion',
    'religione' => 'religion',
    'rapidità di mano' => 'sleight of hand',
    'furtività' => 'stealth',
    'sopravvivenza' => 'survival',
    // Anche versioni abbreviate italiane
    'intuito' => 'insight',
    // Inglese -> Inglese per compatibilità
    'acrobatics' => 'acrobatics',
    'animal handling' => 'animal handling',
    'arcana' => 'arcana',
    'athletics' => 'athletics',
    'deception' => 'deception',
    'history' => 'history',
    'insight' => 'insight',
    'intimidation' => 'intimidation',
    'investigation' => 'investigation',
    'medicine' => 'medicine',
    'nature' => 'nature',
    'perception' => 'perception',
    'performance' => 'performance',
    'persuasion' => 'persuasion',
    'religion' => 'religion',
    'sleight of hand' => 'sleight of hand',
    'stealth' => 'stealth',
    'survival' => 'survival'
];

$skills = [
    'acrobatics' => 'dexterity',
    'animal handling' => 'wisdom',
    'arcana' => 'intelligence',
    'athletics' => 'strength',
    'deception' => 'charisma',
    'history' => 'intelligence',
    'insight' => 'wisdom',
    'intimidation' => 'charisma',
    'investigation' => 'intelligence',
    'medicine' => 'wisdom',
    'nature' => 'intelligence',
    'perception' => 'wisdom',
    'performance' => 'charisma',
    'persuasion' => 'charisma',
    'religion' => 'intelligence',
    'sleight of hand' => 'dexterity',
    'stealth' => 'dexterity',
    'survival' => 'wisdom'
];

// Funzione per verificare se una skill è competente (supporta italiano e inglese)
function isSkillProficient($skill, $characterSkills, $skillMapping) {
    $normalizedSkill = strtolower($skill);

    foreach ($characterSkills as $charSkill) {
        $normalizedCharSkill = strtolower($charSkill);

        // Confronto diretto
        if ($normalizedCharSkill === $normalizedSkill) {
            return true;
        }

        // Confronto tramite mappatura
        if (isset($skillMapping[$normalizedCharSkill])) {
            if ($skillMapping[$normalizedCharSkill] === $normalizedSkill) {
                return true;
            }
        }

        // Confronto inverso (se il JSON usa inglese e stiamo cercando italiano)
        if (isset($skillMapping[$normalizedSkill])) {
            if ($skillMapping[$normalizedSkill] === $normalizedCharSkill) {
                return true;
            }
        }
    }

    return false;
}

// Funzione per verificare se un tiro salvezza è competente (supporta italiano e inglese)
function isSavingThrowProficient($ability, $characterSaves, $abilityMapping) {
    $normalizedAbility = strtolower($ability);

    foreach ($characterSaves as $charSave) {
        $normalizedCharSave = strtolower($charSave);

        // Confronto diretto
        if ($normalizedCharSave === $normalizedAbility) {
            return true;
        }

        // Confronto tramite mappatura
        if (isset($abilityMapping[$normalizedCharSave])) {
            if ($abilityMapping[$normalizedCharSave] === $normalizedAbility) {
                return true;
            }
        }

        // Confronto inverso
        if (isset($abilityMapping[$normalizedAbility])) {
            if ($abilityMapping[$normalizedAbility] === $normalizedCharSave) {
                return true;
            }
        }
    }

    return false;
}
?>

<!-- Pulsante di stampa (nascosto quando si stampa) -->
<div class="print-controls no-print">
    <button onclick="window.print()" class="print-button">🖨️ Stampa Scheda</button>
</div>

<form class="charsheet">
    <header>
        <section class="charname">
            <label for="charname">Nome del Personaggio</label>
            <div class="char-value"><?= htmlspecialchars($character->getName()) ?></div>
        </section>
        <section class="misc">
            <ul>
                <li>
                    <label for="classlevel">Classe e Livello</label>
                    <div class="char-value"><?= htmlspecialchars($character->getClass()) ?> <?= htmlspecialchars($character->getLevel()) ?></div>
                </li>
                <li>
                    <label for="background">Background</label>
                    <div class="char-value"><?= htmlspecialchars($character->getBackground()) ?></div>
                </li>
                <li>
                    <label for="race">Razza</label>
                    <div class="char-value"><?= htmlspecialchars($character->getRace()) ?></div>
                </li>
                <li>
                    <label for="alignment">Allineamento</label>
                    <div class="char-value"><?= htmlspecialchars($character->getAlignment()) ?></div>
                </li>
            </ul>
        </section>
    </header>
    <main>
        <section>
            <section class="attributes">
                <div class="scores">
                    <ul>
                        <?php foreach ($abilityScores as $ability => $score): ?>
                            <li>
                                <div class="score">
                                    <label for="<?= $ability ?>score"><?= $abilityTranslations[$ability] ?></label>
                                    <div class="ability-value"><?= $score ?></div>
                                </div>
                                <div class="modifier">
                                    <div class="modifier-value"><?= $character->getAbilityModifier($ability) >= 0 ? '+' : '' ?><?= $character->getAbilityModifier($ability) ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="attr-applications">
                    <div class="inspiration box">
                        <div class="label-container">
                            <label for="inspiration">Ispirazione</label>
                        </div>
                        <div class="checkbox-display <?= $character->hasInspiration() ? 'checked' : '' ?>"></div>
                    </div>
                    <div class="proficiencybonus box">
                        <div class="label-container">
                            <label for="proficiencybonus">Bonus di Competenza</label>
                        </div>
                        <div class="bonus-value"><?= $character->getProficiencyBonus() >= 0 ? '+' : '' ?><?= $character->getProficiencyBonus() ?></div>
                    </div>
                    <div class="saves list-section box">
                        <ul>
                            <?php foreach ($abilityScores as $ability => $score):
                                $proficient = isSavingThrowProficient($ability, $character->getSavingThrows(), $abilityMapping);
                                $mod = $character->getAbilityModifier($ability);
                                if ($proficient) {
                                    $mod += $character->getProficiencyBonus();
                                }
                                ?>
                                <li>
                                    <label for="<?= $ability ?>-save"><?= $abilityTranslations[$ability] ?></label>
                                    <div class="save-value"><?= $mod >= 0 ? '+' : '' ?><?= $mod ?></div>
                                    <div class="checkbox-display <?= $proficient ? 'checked' : '' ?>"></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="label">
                            Tiri Salvezza
                        </div>
                    </div>
                    <div class="skills list-section box">
                        <ul>
                            <?php foreach ($skills as $skill => $ability):
                                $proficient = isSkillProficient($skill, $character->getSkills(), $skillMapping);
                                $mod = $character->getAbilityModifier($ability);
                                if ($proficient) {
                                    $mod += $character->getProficiencyBonus();
                                }
                                ?>
                                <li>
                                    <label for="<?= str_replace(' ', '', $skill) ?>"><?= $skillTranslations[$skill] ?> <span class="skill">(<?= substr($abilityTranslations[$ability], 0, 3) ?>)</span></label>
                                    <div class="skill-value"><?= $mod >= 0 ? '+' : '' ?><?= $mod ?></div>
                                    <div class="checkbox-display <?= $proficient ? 'checked' : '' ?>"></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="label">
                            Abilità
                        </div>
                    </div>
                </div>
            </section>
            <div class="passive-perception box">
                <div class="label-container">
                    <label for="passiveperception">Saggezza Passiva (Percezione)</label>
                </div>
                <div class="passive-value"><?= 10 + $character->getAbilityModifier('wisdom') + (isSkillProficient('perception', $character->getSkills(), $skillMapping) ? $character->getProficiencyBonus() : 0) ?></div>
            </div>
            <div class="otherprofs box textblock">
                <div class="proficiencies-content">
                    <?php
                    $allProficiencies = array_merge($character->getLanguages(), $character->getProficiencies());
                    echo htmlspecialchars(implode(', ', $allProficiencies));
                    ?>
                </div>
                <label for="otherprofs">Altre Competenze e Linguaggi</label>
            </div>
        </section>
        <section>
            <section class="combat">
                <div class="armorclass">
                    <div>
                        <div class="combat-value"><?= $character->getArmorClass() ?></div>
                        <label for="ac">Classe Armatura</label>
                    </div>
                </div>
                <div class="initiative">
                    <div>
                        <div class="combat-value"><?= $character->getInitiative() >= 0 ? '+' : '' ?><?= $character->getInitiative() ?></div>
                        <label for="initiative">Iniziativa</label>
                    </div>
                </div>
                <div class="speed">
                    <div>
                        <div class="combat-value"><?= $character->getSpeed() ?></div>
                        <label for="speed">Velocità</label>
                    </div>
                </div>
                <div class="hp">
                    <div class="regular">
                        <div class="max">
                            <div class="hp-value"><?= $hp['max'] ?></div>
                            <label for="maxhp">Punti Ferita Massimi</label>
                        </div>
                        <div class="current">
                            <div class="hp-current-value"><?= $hp['current'] ?></div>
                            <label for="currenthp">Punti Ferita Attuali</label>
                        </div>
                    </div>
                    <div class="temporary">
                        <div class="temp-hp-value"><?= $hp['temporary'] ?></div>
                        <label for="temphp">Punti Ferita Temporanei</label>
                    </div>
                </div>
                <div class="hitdice">
                    <div>
                        <div class="total">
                            <div class="dice-value"><?= $character->getLevel() ?>d<?= $character->getHitDie() ?></div>
                            <label for="totalhd">Totale</label>
                        </div>
                        <div class="remaining">
                            <div class="dice-remaining-value"><?= max(0, $character->getLevel() - $character->getHitDiceUsed()) ?></div>
                            <label for="remaininghd">Dadi Vita Rimanenti</label>
                        </div>
                    </div>
                </div>
                <div class="deathsaves">
                    <div>
                        <div class="label">
                            <label>Tiri Salvezza contro Morte</label>
                        </div>
                        <div class="marks">
                            <div class="deathsuccesses">
                                <label>Successi</label>
                                <div class="bubbles">
                                    <?php for ($i = 1; $i <= 3; $i++): ?>
                                        <div class="bubble <?= $i <= $deathSaves['successes'] ? 'filled' : '' ?>"></div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="deathfails">
                                <label>Fallimenti</label>
                                <div class="bubbles">
                                    <?php for ($i = 1; $i <= 3; $i++): ?>
                                        <div class="bubble <?= $i <= $deathSaves['failures'] ? 'filled' : '' ?>"></div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="attacksandspellcasting">
                <div>
                    <label for="attacksandspellcasting">Attacchi e Incantesimi</label>
                    <div class="attacks-content">
                        <?php foreach ($character->getAttacks() as $attack): ?>
                            <div class="attack-line">
                                <span class="attack-name"><?= htmlspecialchars($attack['name'] ?? '') ?></span>
                                <span class="attack-bonus"><?= htmlspecialchars($attack['bonus'] ?? '') ?></span>
                                <span class="attack-damage"><?= htmlspecialchars($attack['damage'] ?? '') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <section class="equipment">
                <div>
                    <label for="equipment">Equipaggiamento</label>
                    <div class="equipment-content">
                        <?php foreach ($character->getEquipment() as $item): ?>
                            <div class="equipment-item"><?= htmlspecialchars($item) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </section>
        <section>
            <section class="fluff">
                <div class="personality">
                    <div class="personality-content"><?= htmlspecialchars($character->getPersonality()['traits']) ?></div>
                    <label for="personality">Tratti della Personalità</label>
                </div>
                <div class="ideals">
                    <div class="ideals-content"><?= htmlspecialchars($character->getPersonality()['ideals']) ?></div>
                    <label for="ideals">Ideali</label>
                </div>
                <div class="bonds">
                    <div class="bonds-content"><?= htmlspecialchars($character->getPersonality()['bonds']) ?></div>
                    <label for="bonds">Legami</label>
                </div>
                <div class="flaws">
                    <div class="flaws-content"><?= htmlspecialchars($character->getPersonality()['flaws']) ?></div>
                    <label for="flaws">Difetti</label>
                </div>
            </section>
            <section class="features">
                <div>
                    <div class="features-content">
                        <?php foreach ($character->getFeatures() as $feature): ?>
                            <div class="feature">
                                <strong><?= htmlspecialchars($feature['name']) ?>:</strong>
                                <?= htmlspecialchars($feature['description']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <label for="features">Caratteristiche e Tratti</label>
                </div>
            </section>
        </section>
    </main>
</form>