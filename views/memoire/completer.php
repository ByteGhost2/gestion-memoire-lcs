<?php include 'views/layouts/header.php'; ?>
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Compléter votre mémoire</h2>
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
            <?php foreach ($errors as $e): ?>
                <p><?= $e ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="<?= BASE_URL ?>/memoire/completer/<?= $memoire['id'] ?>" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="resume">Résumé du mémoire</label>
            <textarea name="resume" id="resume" rows="6" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white"><?= htmlspecialchars($memoire['resume'] ?? '') ?></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="mots_cles">Mots-clés (séparés par des virgules)</label>
            <input type="text" name="mots_cles" id="mots_cles" value="<?= htmlspecialchars($memoire['mots_cles'] ?? '') ?>" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="fichier">Fichier PDF du mémoire <span class="text-red-500">*</span></label>
            <input type="file" name="fichier" accept=".pdf" required class="w-full">
            <p class="text-sm text-gray-500 mt-1">Taille max : 20 Mo. Uniquement PDF.</p>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="couverture">Image de couverture (format JPG/PNG) <span class="text-red-500">*</span></label>
            <input type="file" name="couverture" accept="image/jpeg,image/png,image/jpg" required class="w-full">
            <p class="text-sm text-gray-500 mt-1">Cette image servira de miniature dans la bibliothèque. Obligatoire.</p>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition transform hover:scale-105">
                Envoyer le mémoire
            </button>
            <a href="<?= BASE_URL ?>/memoire/voir/<?= $memoire['id'] ?>" class="text-blue-500 hover:text-blue-800">Annuler</a>
        </div>
    </form>
    <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
        Vous ne pourrez modifier ces informations qu'en déposant une nouvelle version.
    </p>
</div>
<?php include 'views/layouts/footer.php'; ?>