<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8" data-aos="fade-up">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Gestion des soutenances</h1>
        <a href="<?= BASE_URL ?>/admin/planifierSoutenance" class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-2 px-4 rounded-lg transition transform hover:scale-105">
            <i class="fas fa-plus mr-2"></i>Planifier
        </a>
    </div>

    <?php if (isset($_GET['planned'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">Soutenance planifiée avec succès.</div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mémoire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Étudiant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Horaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Salle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($soutenances as $s): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white"><?= $s['id'] ?></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white"><?= htmlspecialchars($s['titre']) ?></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white"><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white"><?= date('d/m/Y', strtotime($s['date'])) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white"><?= substr($s['heure_debut'],0,5) ?> - <?= substr($s['heure_fin'],0,5) ?></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white"><?= $s['salle'] ?></td>
                        <td class="px-6 py-4">
                            <?php if ($s['statut'] == 'terminee'): ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Terminée</span>
                            <?php elseif ($s['statut'] == 'archive'): ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Archivée</span>
                            <?php else: ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Planifiée</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap space-x-2">
                            <a href="<?= BASE_URL ?>/admin/gererJury/<?= $s['id'] ?>" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="Gérer le jury">
                                <i class="fas fa-users-cog"></i>
                            </a>
                            <a href="<?= BASE_URL ?>/pv/generer/<?= $s['id'] ?>" class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300" title="Générer le PV" target="_blank">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <?php if ($s['statut'] != 'terminee' && $s['statut'] != 'archive'): ?>
                                <a href="<?= BASE_URL ?>/admin/terminerSoutenance/<?= $s['id'] ?>" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300" title="Marquer comme terminée" onclick="return confirm('Confirmer que la soutenance est terminée ?')">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($soutenances)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucune soutenance trouvée.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>