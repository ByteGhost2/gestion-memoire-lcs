<?php include 'views/layouts/header.php'; ?>
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Modifier le thème</h2>
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php foreach ($errors as $e): ?>
                <p><?= $e ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="titre">Titre du mémoire</label>
            <input type="text" name="titre" id="titre" value="<?= htmlspecialchars($memoire['titre']) ?>" required class="shadow border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="description">Description du thème</label>
            <textarea name="description" id="description" rows="5" required class="shadow border rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($memoire['theme_description']) ?></textarea>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition transform hover:scale-105">
                Envoyer la modification
            </button>
            <a href="<?= BASE_URL ?>/memoire/voir/<?= $memoire['id'] ?>" class="text-blue-500 hover:text-blue-800">Annuler</a>
        </div>
    </form>
    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
        Après soumission, votre encadreur devra à nouveau valider le thème.
    </p>
</div>
<?php include 'views/layouts/footer.php'; ?>