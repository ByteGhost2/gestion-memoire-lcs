<?php include 'views/layouts/header.php'; ?>
<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Ajouter une indisponibilité</h1>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4"><?= $_SESSION['flash_error'] ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
            <input type="date" name="date" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Heure début</label>
                <input type="time" name="heure_debut" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Heure fin</label>
                <input type="time" name="heure_fin" required class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Motif (optionnel)</label>
            <input type="text" name="motif" class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white" placeholder="ex: Réunion, congé...">
        </div>
        <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-2 px-4 rounded-lg">Enregistrer</button>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>