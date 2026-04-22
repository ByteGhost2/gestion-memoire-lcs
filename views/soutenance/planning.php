<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-8 text-gray-900 dark:text-white" data-aos="fade-up">
        <i class="fas fa-calendar-alt mr-3 text-purple-500"></i>Planning des soutenances
    </h1>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden" data-aos="fade-up">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Horaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Salle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mémoire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Étudiant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Jury</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php foreach ($soutenances as $s): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white"><?= date('d/m/Y', strtotime($s['date'])) ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-white"><?= substr($s['heure_debut'],0,5) ?> - <?= substr($s['heure_fin'],0,5) ?></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white"><?= $s['salle'] ?></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white"><?= htmlspecialchars($s['titre']) ?></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white"><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?></td>
                        <td class="px-6 py-4 text-gray-900 dark:text-white"><?= htmlspecialchars($s['membres_jury'] ?? 'Non défini') ?></td>
                        <td class="px-6 py-4">
                            <?php if ($s['statut'] == 'terminee'): ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Terminée</span>
                            <?php elseif ($s['statut'] == 'archive'): ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Archivée</span>
                            <?php else: ?>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Planifiée</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($soutenances)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucune soutenance trouvée.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>