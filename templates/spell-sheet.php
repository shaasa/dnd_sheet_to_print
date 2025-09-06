<?php
// Questo template riceve $spellcaster e $character come variabili

$abilityTranslations = [
    'intelligence' => 'Intelligenza',
    'wisdom' => 'Saggezza',
    'charisma' => 'Carisma'
];
?>
<div class="page-break"></div>
<form class="spellsheet">
    <header>
        <section class="spellcaster-info">
            <h1>Scheda degli Incantesimi</h1>
            <div class="spell-stats">
                <div class="spell-stat">
                    <label>Classe da Incantatore</label>
                    <div class="spell-value"><?= htmlspecialchars($spellcaster->getSpellcastingClass()) ?></div>
                </div>
                <div class="spell-stat">
                    <label>Caratteristica da Incantatore</label>
                    <div class="spell-value"><?= $abilityTranslations[$spellcaster->getSpellcastingAbility()] ?? ucfirst($spellcaster->getSpellcastingAbility()) ?></div>
                </div>
                <div class="spell-stat">
                    <label>CD Tiro Salvezza</label>
                    <div class="spell-value"><?= $spellcaster->getSpellSaveDC($character ?? null) ?></div>
                </div>
                <div class="spell-stat">
                    <label>Bonus di Attacco</label>
                    <div class="spell-value">
                        <?php
                        $bonus = $spellcaster->getSpellAttackBonus($character ?? null);
                        echo ($bonus >= 0 ? '+' : '') . $bonus;
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </header>

    <main>
        <?php
        $cantrips = $spellcaster->getCantrips();
        $cantripDetails = $spellcaster->getCantripDetails();
        ?>
        <?php if (!empty($cantrips)): ?>
            <section class="cantrips-section">
                <div class="spell-level-header">
                    <h2>Trucchetti (Livello 0)</h2>
                </div>
                <div class="spell-list">
                    <?php foreach ($cantrips as $cantrip): ?>
                        <div class="spell-item">
                            <div class="spell-name"><?= htmlspecialchars($cantrip) ?></div>
                            <?php if (isset($cantripDetails[$cantrip])): ?>
                                <div class="spell-description"><?= htmlspecialchars($cantripDetails[$cantrip]) ?></div>
                            <?php else: ?>
                                <div class="spell-description-blank"></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php
        $spellsByLevel = $spellcaster->getSpellsByLevel();
        $spellDetailsByLevel = $spellcaster->getSpellDetailsByLevel();
        $preparedSpells = $spellcaster->getPreparedSpells();
        $slots = $spellcaster->getSpellSlots();
        $slotsUsed = $spellcaster->getSlotsUsed();

        for ($level = 1; $level <= 9; $level++):
            if (empty($spellsByLevel[$level])) continue;
            ?>
            <section class="spell-level-section">
                <div class="spell-level-header">
                    <h2>Incantesimi di <?= $level ?>° Livello</h2>
                    <?php if (isset($slots[$level]) && $slots[$level] > 0): ?>
                        <div class="spell-slots">
                            <label>Slot Incantesimo:</label>
                            <div class="slots-container">
                                <?php
                                $totalSlots = $slots[$level];
                                $usedSlots = $slotsUsed[$level] ?? 0;
                                for ($i = 0; $i < $totalSlots; $i++):
                                    $isUsed = $i < $usedSlots;
                                    ?>
                                    <div class="slot-circle <?= $isUsed ? 'used' : '' ?>"></div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="spell-list">
                    <?php foreach ($spellsByLevel[$level] as $spell):
                        $isPrepared = in_array($spell, $preparedSpells);
                        $spellDetails = $spellDetailsByLevel[$level][$spell] ?? null;
                        ?>
                        <div class="spell-item">
                            <div class="spell-header">
                                <div class="spell-checkbox <?= $isPrepared ? 'checked' : '' ?>"></div>
                                <div class="spell-name"><?= htmlspecialchars($spell) ?></div>
                            </div>
                            <?php if ($spellDetails): ?>
                                <div class="spell-description"><?= htmlspecialchars($spellDetails) ?></div>
                            <?php else: ?>
                                <div class="spell-description-blank"></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endfor; ?>

        <?php if (isset($character) && $character): ?>
            <section class="spell-info-section">
                <div class="spell-preparation-info">
                    <h3>Informazioni Incantesimi</h3>
                    <p><strong>Incantesimi Preparati Massimi:</strong> <?= $spellcaster->getMaxPreparedSpells($character) ?></p>
                    <p><strong>Incantesimi Attualmente Preparati:</strong> <?= count($preparedSpells) ?></p>

                    <?php
                    $totalSlotsUsed = array_sum($slotsUsed);
                    $totalSlots = array_sum($slots);
                    ?>
                    <p><strong>Slot Incantesimo Utilizzati:</strong> <?= $totalSlotsUsed ?> / <?= $totalSlots ?></p>
                </div>
            </section>
        <?php endif; ?>
    </main>
</form>