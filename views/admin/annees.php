<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Gestion des années universitaires</h1>

    <!-- Formulaire d'ajout -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Ajouter une année</h2>
        <form method="POST" action="" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Libellé</label>
                <input type="text" name="libelle" placeholder="ex: 2025-2026" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date début</label>
                <input type="date" name="date_debut" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date fin</label>
                <input type="date" name="date_fin" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div class="flex items-center">
                <input type="checkbox" name="active" id="active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <label for="active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</label>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-2 px-4 rounded-lg transition">
                    <i class="fas fa-save mr-2"></i>Ajouter
                </button>
            </div>
        </form>
    </div>

    <!-- Liste -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Libellé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Début</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Active</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($annees as $a): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-6 py-4 text-gray-900 dark:text-white"><?= $a['id'] ?></td>
                    <td class="px-6 py-4 text-gray-900 dark:text-white"><?= htmlspecialchars($a['libelle']) ?></td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300"><?= date('d/m/Y', strtotime($a['date_debut'])) ?></td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300"><?= date('d/m/Y', strtotime($a['date_fin'])) ?></td>
                    <td class="px-6 py-4">
                        <?php if ($a['active']): ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Oui</span>
                        <?php else: ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Non</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 space-x-2">
                        <?php if (!$a['active']): ?>
                            <a href="<?= BASE_URL ?>/admin/setAnneeActive/<?= $a['id'] ?>" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300" title="Activer"><i class="fas fa-check-circle"></i></a>
                        <?php endif; ?>
                        <!-- On peut ajouter modification/suppression si nécessaire -->
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($annees)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucune année universitaire.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>