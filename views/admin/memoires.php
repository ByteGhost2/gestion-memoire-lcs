<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-white" style="text-shadow: 2px 2px 4px #000;">Gestion des mémoires</h1>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date soumission</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Encadreur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($memoires as $m): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><?= $m['id'] ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($m['titre']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?= $m['statut'] == 'valide' ? 'bg-green-100 text-green-800' : ($m['statut'] == 'en_cours' ? 'bg-yellow-100 text-yellow-800' : ($m['statut'] == 'soumis' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) ?>">
                            <?= $m['statut'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4"><?= date('d/m/Y', strtotime($m['date_soumission'])) ?></td>
                    <td class="px-6 py-4"><?= $m['id_encadreur'] ? 'ID '.$m['id_encadreur'] : '-' ?></td>
                    <td class="px-6 py-4">
                        <a href="<?= BASE_URL ?>/admin/voirMemoire/<?= $m['id'] ?>" class="text-blue-600 hover:text-blue-900">Voir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>