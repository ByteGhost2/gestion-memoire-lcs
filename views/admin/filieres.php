<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Gestion des filières</h1>

    <!-- Formulaire d'ajout -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Ajouter une filière</h2>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div class="flex space-x-2">
                <input type="text" name="nom" placeholder="Nom de la filière" required class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                <button type="submit" class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-2 px-6 rounded-lg transition">
                    <i class="fas fa-plus mr-2"></i>Ajouter
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($filieres as $f): ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <td class="px-6 py-4 text-gray-900 dark:text-white"><?= $f['id'] ?></td>
                    <td class="px-6 py-4">
                        <form method="POST" action="" class="inline">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                            <input type="text" name="nom" value="<?= htmlspecialchars($f['nom']) ?>" class="px-2 py-1 border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-white text-sm">
                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 ml-1" title="Modifier"><i class="fas fa-save"></i></button>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <a href="<?= BASE_URL ?>/admin/supprimerFiliere/<?= $f['id'] ?>" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300" onclick="return confirm('Supprimer cette filière ?')" title="Supprimer"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($filieres)): ?>
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">Aucune filière enregistrée.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>