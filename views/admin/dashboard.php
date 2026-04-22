<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Tableau de bord Administrateur</h1>

    <?php
    $pdo = Db::getInstance();

    // Mémoires validés sans soutenance
    $valides_sans_soutenance = $pdo->prepare("
        SELECT m.id, m.titre, u.nom, u.prenom
        FROM memoires m
        JOIN utilisateurs u ON m.id_etudiant = u.id
        WHERE m.statut = 'valide'
        AND NOT EXISTS (SELECT 1 FROM soutenances s WHERE s.id_memoire = m.id)
    ");
    $valides_sans_soutenance->execute();
    $valides_sans_soutenance = $valides_sans_soutenance->fetchAll();

    // Mémoires soutenus sans attestation
    $soutenus_sans_attestation = $pdo->prepare("
        SELECT m.id, m.titre, u.nom, u.prenom
        FROM memoires m
        JOIN utilisateurs u ON m.id_etudiant = u.id
        WHERE m.statut = 'soutenu'
        AND NOT EXISTS (SELECT 1 FROM attestations a WHERE a.id_memoire = m.id)
    ");
    $soutenus_sans_attestation->execute();
    $sans_attest = $soutenus_sans_attestation->fetchAll();
    ?>

    <?php if (!empty($valides_sans_soutenance)): ?>
        <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-400 p-4 mb-6 rounded">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-yellow-800 dark:text-yellow-200">Mémoires prêts pour la soutenance</h3>
                    <div class="mt-2">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach ($valides_sans_soutenance as $m): ?>
                            <li>
                                <a href="<?= BASE_URL ?>/memoire/voir/<?= $m['id'] ?>" class="text-blue-600 hover:underline">
                                    <?= htmlspecialchars($m['titre']) ?> - <?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-3">
                            <a href="<?= BASE_URL ?>/admin/planifierSoutenance" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
                                <i class="fas fa-calendar-plus mr-2"></i> Planifier maintenant
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($sans_attest)): ?>
        <div class="bg-orange-50 dark:bg-orange-900 border-l-4 border-orange-400 p-4 mb-6 rounded">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-file-alt text-orange-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-orange-800 dark:text-orange-200">Attestations à téléverser</h3>
                    <ul class="list-disc list-inside space-y-1 mt-2">
                        <?php foreach ($sans_attest as $m): ?>
                        <li>
                            <a href="<?= BASE_URL ?>/attestation/upload/<?= $m['id'] ?>" class="text-blue-600 hover:underline">
                                <?= htmlspecialchars($m['titre']) ?> - <?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div><p class="text-lg opacity-90">Utilisateurs</p><p class="text-4xl font-bold"><?= $stats['utilisateurs'] ?></p></div>
                <i class="fas fa-users text-5xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div><p class="text-lg opacity-90">Mémoires</p><p class="text-4xl font-bold"><?= $stats['memoires'] ?></p></div>
                <i class="fas fa-file-alt text-5xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div><p class="text-lg opacity-90">Soutenances</p><p class="text-4xl font-bold"><?= $stats['soutenances'] ?></p></div>
                <i class="fas fa-calendar-check text-5xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
            <div class="flex items-center justify-between">
                <div><p class="text-lg opacity-90">Mémoires soutenus</p><p class="text-4xl font-bold"><?= $stats['memoires_soutenus'] ?></p></div>
                <i class="fas fa-graduation-cap text-5xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Derniers mémoires et dernières soutenances -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6">
            <h2 class="text-xl font-bold mb-4">Derniers mémoires déposés</h2>
            <ul class="divide-y">
                <?php foreach ($derniers_memoires as $m): ?>
                <li class="py-2 flex justify-between">
                    <span><?= htmlspecialchars($m['titre']) ?> (<?= $m['prenom'] ?> <?= $m['nom'] ?>)</span>
                    <a href="<?= BASE_URL ?>/memoire/voir/<?= $m['id'] ?>" class="text-blue-600 hover:underline">Voir</a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6">
            <h2 class="text-xl font-bold mb-4">Dernières soutenances</h2>
            <ul class="divide-y">
                <?php foreach ($dernieres_soutenances as $s): ?>
                <li class="py-2 flex justify-between">
                    <span><?= htmlspecialchars($s['titre']) ?> (<?= $s['prenom'] ?> <?= $s['nom'] ?>) - <?= date('d/m/Y', strtotime($s['date'])) ?></span>
                    <a href="<?= BASE_URL ?>/admin/gererJury/<?= $s['id'] ?>" class="text-blue-600 hover:underline">Gérer jury</a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6">
        <h2 class="text-xl font-bold mb-4">Actions rapides</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?= BASE_URL ?>/admin/utilisateurs" class="bg-blue-500 hover:bg-blue-700 text-white text-center py-3 rounded transition">Utilisateurs</a>
            <a href="<?= BASE_URL ?>/admin/memoires" class="bg-green-500 hover:bg-green-700 text-white text-center py-3 rounded transition">Mémoires</a>
            <a href="<?= BASE_URL ?>/admin/planifierSoutenance" class="bg-purple-500 hover:bg-purple-700 text-white text-center py-3 rounded transition">Planifier</a>
            <a href="<?= BASE_URL ?>/admin/statistiques" class="bg-yellow-500 hover:bg-yellow-700 text-white text-center py-3 rounded transition">Statistiques</a>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>