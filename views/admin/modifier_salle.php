<?php include 'views/layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Modifier une salle</h1>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($salle['nom']) ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capacité</label>
            <input type="number" name="capacite" min="1" value="<?= $salle['capacite'] ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Équipement</label>
            <input type="text" name="equipement" value="<?= htmlspecialchars($salle['equipement'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4 flex items-center">
            <input type="checkbox" name="active" id="active" <?= $salle['active'] ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            <label for="active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</label>
        </div>
        <div class="flex space-x-2">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">Enregistrer</button>
            <a href="<?= BASE_URL ?>/admin/salles" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition">Annuler</a>
        </div>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>