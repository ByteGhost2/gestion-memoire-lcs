<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mes indisponibilités</h1>
        <a href="<?= BASE_URL ?>/disponibilite/ajouter" class="bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-2 px-4 rounded-lg">
            <i class="fas fa-plus mr-2"></i>Ajouter
        </a>
    </div>

    <?php if (isset($_GET['added'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">Indisponibilité ajoutée.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">Indisponibilité supprimée.</div>
    <?php endif; ?>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4"><?= $_SESSION['flash_error'] ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Heure début</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Heure fin</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Motif</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($dispos as $d): ?>
                <tr>
                    <td class="px-6 py-4"><?= date('d/m/Y', strtotime($d['date'])) ?></td>
                    <td class="px-6 py-4"><?= substr($d['heure_debut'],0,5) ?></td>
                    <td class="px-6 py-4"><?= substr($d['heure_fin'],0,5) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($d['motif']) ?></td>
                    <td class="px-6 py-4">
                        <a href="<?= BASE_URL ?>/disponibilite/supprimer/<?= $d['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Supprimer cette indisponibilité ?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($dispos)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune indisponibilité renseignée.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>