<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tableau de bord enseignant</h1>
        <a href="<?= BASE_URL ?>/enseignant/calendrier" class="bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white font-bold py-2 px-4 rounded-lg transition transform hover:scale-105">
            <i class="fas fa-calendar-alt mr-2"></i>Voir calendrier
        </a>
    </div>

    <?php if (isset($_GET['evaluated'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">Évaluation enregistrée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['terminee'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">Soutenance marquée comme terminée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['archive'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">Soutenance archivée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['supprime'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">Soutenance supprimée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['finalise'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">Soutenance finalisée. Le mémoire est maintenant soutenu.</div>
    <?php endif; ?>

    <!-- Mémoires encadrés sans soutenance -->
    <?php if (!empty($memoires_encadres)): ?>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Mémoires à suivre</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Titre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Étudiant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                        </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($memoires_encadres as $m): ?>
                    
                            <td class="px-6 py-4"><?= htmlspecialchars($m['titre']) ?> </td>
                            <td class="px-6 py-4"><?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?> </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= $m['statut'] == 'valide' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' ?>">
                                    <?= $m['statut'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="<?= BASE_URL ?>/memoire/voir/<?= $m['id'] ?>" class="text-blue-600 hover:underline">Voir</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Soutenances planifiées -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Horaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mémoire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Étudiant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Salle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Évaluation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </thead>
                <tbody>
                    <?php foreach ($soutenances as $s): 
                        $roles = [];
                        if ($s['id_encadreur'] == $_SESSION['user']['id']) $roles[] = 'Encadreur';
                        if ($s['est_jury'] > 0) $roles[] = 'Jury';
                        $roleTexte = implode(' / ', $roles);
                    ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap"><?= date('d/m/Y', strtotime($s['date'])) ?> </td>
                        <td class="px-6 py-4 whitespace-nowrap"><?= substr($s['heure_debut'],0,5) ?> - <?= substr($s['heure_fin'],0,5) ?> </td>
                        <td class="px-6 py-4"><?= htmlspecialchars($s['titre']) ?> </td>
                        <td class="px-6 py-4"><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?> </td>
                        <td class="px-6 py-4"><?= $s['salle'] ?> </td>
                        <td class="px-6 py-4"><?= $roleTexte ?> </td>
                        <td class="px-6 py-4">
                            <?php if ($s['statut'] == 'terminee'): ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Terminée</span>
                            <?php elseif ($s['statut'] == 'archive'): ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Archivée</span>
                            <?php else: ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Planifiée</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($s['deja_evalue'] > 0): ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Évalué</span>
                            <?php else: ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">En attente</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap space-x-2">
                            <a href="<?= BASE_URL ?>/enseignant/telechargerMemoire/<?= $s['id'] ?>" class="text-blue-600 dark:text-blue-400" title="Télécharger"><i class="fas fa-download"></i></a>
                            <a href="<?= BASE_URL ?>/enseignant/evaluer/<?= $s['id'] ?>" class="text-green-600 dark:text-green-400" title="Évaluer"><i class="fas fa-star"></i></a>
                            <?php if ($s['statut'] != 'terminee' && $s['statut'] != 'archive'): ?>
                                <a href="<?= BASE_URL ?>/enseignant/terminerSoutenance/<?= $s['id'] ?>" class="text-purple-600 dark:text-purple-400" onclick="return confirm('Marquer comme terminée ?')"><i class="fas fa-check-circle"></i></a>
                            <?php endif; ?>
                            <?php if ($s['est_president'] && $s['statut'] != 'terminee' && $s['statut'] != 'archive'): ?>
                                <a href="<?= BASE_URL ?>/enseignant/finaliserSoutenance/<?= $s['id'] ?>" class="text-green-600 dark:text-green-400" onclick="return confirm('Finaliser cette soutenance ? Tous les membres ont-ils évalué ?')"><i class="fas fa-check-double"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($soutenances) && empty($memoires_encadres)): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucune donnée à afficher.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>