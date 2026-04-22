<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Tableau de bord encadreur</h1>

    <?php
    $pdo = Db::getInstance();
    $id_encadreur = $_SESSION['user']['id'];

    // Sujets en attente de validation (statut 'soumis')
    $stmt = $pdo->prepare("
        SELECT m.*, u.nom, u.prenom
        FROM memoires m
        JOIN utilisateurs u ON m.id_etudiant = u.id
        WHERE m.id_encadreur = ? AND m.statut = 'soumis'
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$id_encadreur]);
    $soumis = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (!empty($soumis)): ?>
        <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-500 p-4 mb-6">
            <h2 class="text-xl font-bold text-yellow-800 dark:text-yellow-200">Sujets à valider</h2>
            <ul class="mt-2 space-y-2">
                <?php foreach ($soumis as $s): ?>
                <li class="flex justify-between items-center">
                    <span><strong><?= htmlspecialchars($s['titre']) ?></strong> - <?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?></span>
                    <a href="<?= BASE_URL ?>/memoire/validerSujet/<?= $s['id'] ?>" class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-1 px-3 rounded" onclick="return confirm('Valider ce sujet ?')">Valider</a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
    // Récupérer les soutenances assignées (jury ou encadreur)
    $sql = "SELECT s.*, m.titre, u.nom, u.prenom, u.id as etudiant_id,
                (SELECT COUNT(*) FROM evaluations WHERE id_soutenance = s.id AND id_utilisateur = ?) as deja_evalue,
                (SELECT COUNT(*) FROM jury WHERE id_soutenance = s.id AND id_utilisateur = ?) as est_jury,
                m.id_encadreur
            FROM soutenances s
            LEFT JOIN jury j ON s.id = j.id_soutenance
            JOIN memoires m ON s.id_memoire = m.id
            JOIN utilisateurs u ON m.id_etudiant = u.id
            WHERE (j.id_utilisateur = ? OR m.id_encadreur = ?)
            ORDER BY s.date DESC, s.heure_debut DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_encadreur, $id_encadreur, $id_encadreur, $id_encadreur]);
    $soutenances = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <h2 class="text-xl font-bold p-4 border-b">Soutenances à venir</h2>
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Horaire</th>
                    <th class="px-4 py-2">Mémoire</th>
                    <th class="px-4 py-2">Étudiant</th>
                    <th class="px-4 py-2">Salle</th>
                    <th class="px-4 py-2">Rôle</th>
                    <th class="px-4 py-2">Statut</th>
                    <th class="px-4 py-2">Évaluation</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($soutenances as $s): 
                    $roles = [];
                    if ($s['id_encadreur'] == $id_encadreur) $roles[] = 'Encadreur';
                    if ($s['est_jury'] > 0) $roles[] = 'Jury';
                    $roleTexte = implode(' / ', $roles);
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2"><?= date('d/m/Y', strtotime($s['date'])) ?></td>
                    <td class="px-4 py-2"><?= substr($s['heure_debut'],0,5) ?> - <?= substr($s['heure_fin'],0,5) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($s['titre']) ?></td>
                    <td class="px-4 py-2"><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?></td>
                    <td class="px-4 py-2"><?= $s['salle'] ?></td>
                    <td class="px-4 py-2"><?= $roleTexte ?></td>
                    <td class="px-4 py-2">
                        <?php if ($s['statut'] == 'terminee'): ?>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Terminée</span>
                        <?php elseif ($s['statut'] == 'archive'): ?>
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded">Archivée</span>
                        <?php else: ?>
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Planifiée</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2">
                        <?php if ($s['deja_evalue'] > 0): ?>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Évalué</span>
                        <?php else: ?>
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">En attente</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="<?= BASE_URL ?>/enseignant/telechargerMemoire/<?= $s['id'] ?>" class="text-blue-600"><i class="fas fa-download"></i></a>
                        <a href="<?= BASE_URL ?>/enseignant/evaluer/<?= $s['id'] ?>" class="text-green-600"><i class="fas fa-star"></i></a>
                        <?php if ($s['statut'] != 'terminee' && $s['statut'] != 'archive'): ?>
                            <a href="<?= BASE_URL ?>/enseignant/terminerSoutenance/<?= $s['id'] ?>" class="text-purple-600" onclick="return confirm('Marquer comme terminée ?')"><i class="fas fa-check-circle"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($soutenances)): ?>
                <tr><td colspan="9" class="text-center p-4">Aucune soutenance assignée.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>