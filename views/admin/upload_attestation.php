<?php include 'views/layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-8">
    <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Téléverser une attestation</h1>
    <p class="mb-4 text-gray-700 dark:text-gray-300">
        Mémoire : <strong><?= htmlspecialchars($memoire_titre) ?></strong>
    </p>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="id_etudiant">Étudiant</label>
            <select name="id_etudiant" id="id_etudiant" required class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-white">
                <option value="">-- Sélectionner l'étudiant --</option>
                <?php foreach ($etudiants as $e): ?>
                    <option value="<?= $e['id'] ?>">
                        <?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?> (<?= $e['role'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Fichier PDF de l'attestation</label>
            <input type="file" name="fichier" accept=".pdf" required class="w-full">
            <p class="text-sm text-gray-500 mt-1">Taille max : 20 Mo</p>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Téléverser</button>
            <a href="<?= BASE_URL ?>/admin/voirMemoire/<?= $id_memoire ?>" class="text-gray-600 dark:text-gray-400 hover:underline">Annuler</a>
        </div>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>