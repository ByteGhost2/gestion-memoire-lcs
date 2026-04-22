<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Mes attestations</h1>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Numéro</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mémoire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attestations as $a): ?>
                <tr>
                    <td class="px-6 py-4"><?= htmlspecialchars($a['numero']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($a['titre']) ?></td>
                    <td class="px-6 py-4"><?= date('d/m/Y', strtotime($a['date_emission'])) ?></td>
                    <td class="px-6 py-4">
                        <a href="<?= BASE_URL ?>/attestation/telecharger/<?= $a['id_memoire'] ?>?etudiant=<?= $_SESSION['user']['id'] ?>" class="text-blue-600 hover:underline">
                            <i class="fas fa-download mr-1"></i>Télécharger
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($attestations)): ?>
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune attestation disponible.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>