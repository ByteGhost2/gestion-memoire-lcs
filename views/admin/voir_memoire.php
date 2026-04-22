<?php
// Récupération des données complémentaires nécessaires
$pdo = Db::getInstance();

// Récupérer le nom de l'encadreur si présent
$encadreur_nom = '';
if ($memoire['id_encadreur']) {
    $stmtEnc = $pdo->prepare("SELECT nom, prenom FROM utilisateurs WHERE id = ?");
    $stmtEnc->execute([$memoire['id_encadreur']]);
    $enc = $stmtEnc->fetch(PDO::FETCH_ASSOC);
    if ($enc) {
        $encadreur_nom = $enc['prenom'] . ' ' . $enc['nom'];
    }
}

// Récupérer les attestations par étudiant
$stmtAtt = $pdo->prepare("
    SELECT a.*, u.nom, u.prenom 
    FROM attestations a
    JOIN utilisateurs u ON a.id_etudiant = u.id
    WHERE a.id_memoire = ?
");
$stmtAtt->execute([$memoire['id']]);
$attestations = $stmtAtt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Détails du mémoire</h1>
        <a href="<?= BASE_URL ?>/admin/memoires" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i>Retour
        </a>
    </div>

    <!-- En-tête du mémoire -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($memoire['titre']) ?></h2>
            <span class="mt-2 md:mt-0 px-3 py-1 rounded-full text-sm font-semibold 
                <?= $memoire['statut'] == 'soutenu' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                    ($memoire['statut'] == 'valide' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                    ($memoire['statut'] == 'en_cours' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300')) ?>">
                <?= $memoire['statut'] ?>
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Étudiant principal :</span> <?= htmlspecialchars($memoire['prenom'] . ' ' . $memoire['nom']) ?></p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Email :</span> <?= htmlspecialchars($memoire['email']) ?></p>
                <?php if ($memoire['id_encadreur']): ?>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Encadreur :</span> <?= htmlspecialchars($encadreur_nom) ?></p>
                <?php else: ?>
                    <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Encadreur :</span> Non assigné</p>
                <?php endif; ?>
            </div>
            <div>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Date de soumission :</span> <?= date('d/m/Y', strtotime($memoire['date_soumission'])) ?></p>
                <p class="text-gray-700 dark:text-gray-300"><span class="font-semibold">Mots-clés :</span> <?= htmlspecialchars($memoire['mots_cles'] ?? '-') ?></p>
            </div>
        </div>

        <!-- Description du thème -->
        <?php if (!empty($memoire['titre'])): ?>
            <div class="mb-4">
                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Thème proposé</h3>
                <p class="text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($memoire['titre'])) ?></p>
            </div>
        <?php endif; ?>

        <!-- Résumé -->
        <div class="mb-4">
            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Résumé</h3>
            <p class="text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($memoire['resume'])) ?></p>
        </div>

        <!-- Fichier actuel -->
        <?php if (!empty($memoire['fichier'])): ?>
        <div class="mb-4">
            <a href="<?= BASE_URL ?>/<?= $memoire['fichier'] ?>" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white rounded-lg transition">
                <i class="fas fa-file-pdf mr-2"></i> Télécharger la version finale (v<?= $memoire['version_actuelle'] ?>)
            </a>
        </div>
        <?php endif; ?>

        <!-- Actions d'administration -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4 flex flex-wrap gap-2">
            

            <form method="POST" action="<?= BASE_URL ?>/admin/assignerEncadreur" class="flex items-center space-x-2">
                <input type="hidden" name="id_memoire" value="<?= $memoire['id'] ?>">
                <label for="id_encadreur" class="text-sm font-medium text-gray-700 dark:text-gray-300">Encadreur :</label>
                <select name="id_encadreur" id="id_encadreur" class="border rounded p-2 dark:bg-gray-700 dark:text-white">
                    <option value="">-- Aucun --</option>
                    <?php foreach ($encadreurs as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= ($e['id'] == $memoire['id_encadreur']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">Assigner</button>
            </form>
        </div>

        <!-- Attestations -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Attestations de soutenance</h3>
            <?php if (empty($attestations)): ?>
                <p class="text-gray-600 dark:text-gray-400">Aucune attestation téléversée.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($attestations as $att): ?>
                        <li class="flex justify-between items-center">
                            <span><?= htmlspecialchars($att['prenom'] . ' ' . $att['nom']) ?></span>
                            <div>
                                <a href="<?= BASE_URL ?>/attestation/telecharger/<?= $memoire['id'] ?>?etudiant=<?= $att['id_etudiant'] ?>" class="text-blue-600 hover:underline mr-2">Télécharger</a>
                                <a href="<?= BASE_URL ?>/attestation/supprimer/<?= $memoire['id'] ?>/<?= $att['id_etudiant'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Supprimer cette attestation ?')">Supprimer</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ($memoire['statut'] == 'soutenu'): ?>
                <div class="mt-3">
                    <a href="<?= BASE_URL ?>/attestation/upload/<?= $memoire['id'] ?>" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm inline-block">
                        <i class="fas fa-upload mr-1"></i> Ajouter une attestation pour un étudiant
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function updateStatut(id, statut) {
    if (!statut) return;
    fetch('<?= BASE_URL ?>/admin/updateMemoireStatut', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&statut=' + statut
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
    });
}
</script>
<?php include 'views/layouts/footer.php'; ?>