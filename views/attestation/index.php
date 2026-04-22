<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Gestion des attestations</h1>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4"><?= $_SESSION['flash_success'] ?></div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4"><?= $_SESSION['flash_error'] ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Numéro</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mémoire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date émission</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fichier</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($attestations as $a): ?>
                <tr>
                    <td class="px-6 py-4"><?= htmlspecialchars($a['numero']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($a['titre']) ?></td>
                    <td class="px-6 py-4"><?= $a['type'] ?></td>
                    <td class="px-6 py-4"><?= date('d/m/Y', strtotime($a['date_emission'])) ?></td>
                    <td class="px-6 py-4">
                        <a href="<?= BASE_URL ?>/attestation/telecharger/<?= $a['id_memoire'] ?>" class="text-blue-600 hover:underline">
                            <i class="fas fa-download"></i> Télécharger
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($attestations)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucune attestation générée.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>