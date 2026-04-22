<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white" style="text-shadow: 2px 2px 4px #000;">Gestion des mémoires</h1>
        <?php if ($_SESSION['user']['role'] == 'etudiant'): ?>
            <a href="<?= BASE_URL ?>/memoire/soumettre" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">Soumettre un sujet</a>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Titre</th>
                    <?php if ($_SESSION['user']['role'] != 'etudiant'): ?>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                    <?php endif; ?>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($memoires as $m): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><?= htmlspecialchars($m['titre']) ?></td>
                    <?php if ($_SESSION['user']['role'] != 'etudiant'): ?>
                        <td class="px-6 py-4"><?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></td>
                    <?php endif; ?>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?= $m['statut'] == 'valide' ? 'bg-green-100 text-green-800' : ($m['statut'] == 'en_cours' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') ?>">
                            <?= $m['statut'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4"><?= date('d/m/Y', strtotime($m['created_at'])) ?></td>
                    <td class="px-6 py-4">
                        <a href="<?= BASE_URL ?>/memoire/voir/<?= $m['id'] ?>" class="text-blue-600 hover:text-blue-900">Voir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>