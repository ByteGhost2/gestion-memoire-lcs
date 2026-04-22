<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-white" style="text-shadow: 2px 2px 4px #000;">Gestion de la bibliothèque numérique</h1>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auteur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Année</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fichier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($memoires as $m): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><?= $m['id'] ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($m['titre']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></td>
                    <td class="px-6 py-4"><?= date('Y', strtotime($m['date_soumission'])) ?></td>
                    <td class="px-6 py-4">
                        <?php if ($m['fichier']): ?>
                            <a href="<?= BASE_URL ?>/<?= $m['fichier'] ?>" target="_blank" class="text-blue-600 hover:underline">Voir</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <a href="<?= BASE_URL ?>/admin/togglePublic/<?= $m['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Retirer ce mémoire de la bibliothèque ?')">Masquer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>