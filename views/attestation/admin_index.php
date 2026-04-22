<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-white" style="text-shadow: 2px 2px 4px #000;">Gestion des attestations</h1>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attestation</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mention</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date upload</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($etudiants as $e): ?>
                <tr>
                    <td class="px-6 py-4"><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($e['email']) ?></td>
                    <td class="px-6 py-4">
                        <?php if ($e['fichier']): ?>
                            <a href="<?= BASE_URL ?>/<?= $e['fichier'] ?>" target="_blank" class="text-blue-600 hover:underline">Voir</a>
                        <?php else: ?>
                            <span class="text-gray-400">Aucune</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4"><?= htmlspecialchars($e['mention'] ?? '-') ?></td>
                    <td class="px-6 py-4"><?= $e['date_upload'] ? date('d/m/Y', strtotime($e['date_upload'])) : '-' ?></td>
                    <td class="px-6 py-4">
                        <a href="<?= BASE_URL ?>/attestation/upload/<?= $e['id'] ?>" class="text-blue-600 hover:underline"><?= $e['fichier'] ? 'Modifier' : 'Uploader' ?></a>
                        <?php if ($e['fichier']): ?>
                            | <a href="<?= BASE_URL ?>/attestation/delete/<?= $e['id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Supprimer cette attestation ?')">Supprimer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>