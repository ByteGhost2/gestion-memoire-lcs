<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Gestion des critères d'évaluation</h1>

    <!-- Formulaire d'ajout -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Ajouter un critère</h2>
        <form method="POST" action="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom du critère</label>
                    <input type="text" name="nom" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poids (coefficient)</label>
                    <input type="number" name="poids" step="0.01" min="0" value="1.00" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
            </div>
            <div class="mt-4 flex items-center">
                <input type="checkbox" name="actif" id="actif" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                <label for="actif" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Actif</label>
            </div>
            <button type="submit" class="mt-4 bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-2 px-6 rounded-lg transition transform hover:scale-105">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </form>
    </div>

    <!-- Liste des critères -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Poids</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($criteres as $c): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-6 py-4 text-gray-900 dark:text-white"><?= $c['id'] ?></td>
                    <td class="px-6 py-4 text-gray-900 dark:text-white"><?= htmlspecialchars($c['nom']) ?></td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-300"><?= htmlspecialchars($c['description'] ?? '') ?></td>
                    <td class="px-6 py-4 text-gray-900 dark:text-white"><?= $c['poids'] ?></td>
                    <td class="px-6 py-4">
                        <?php if ($c['actif']): ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Oui</span>
                        <?php else: ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Non</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="<?= BASE_URL ?>/admin/modifierCritere/<?= $c['id'] ?>" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300" title="Modifier"><i class="fas fa-edit"></i></a>
                        <a href="<?= BASE_URL ?>/admin/supprimerCritere/<?= $c['id'] ?>" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300" onclick="return confirm('Supprimer ce critère ?')" title="Supprimer"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($criteres)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucun critère défini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>