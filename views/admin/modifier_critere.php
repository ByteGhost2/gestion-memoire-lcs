<?php include 'views/layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Modifier un critère</h1>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($critere['nom']) ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Poids</label>
            <input type="number" name="poids" step="0.01" value="<?= $critere['poids'] ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($critere['description'] ?? '') ?></textarea>
        </div>
        <div class="mb-4 flex items-center">
            <input type="checkbox" name="actif" id="actif" <?= $critere['actif'] ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <label for="actif" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Actif</label>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">Enregistrer</button>
            <a href="<?= BASE_URL ?>/admin/criteres" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">Annuler</a>
        </div>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>