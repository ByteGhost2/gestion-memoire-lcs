<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-white" style="text-shadow: 2px 2px 4px #000;">Tableau de bord du jury</h1>

    <?php if (isset($_GET['evaluated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Évaluation enregistrée avec succès.</div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horaire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mémoire</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut évaluation</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($soutenances as $s): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><?= date('d/m/Y', strtotime($s['date'])) ?></td>
                    <td class="px-6 py-4"><?= substr($s['heure_debut'],0,5) ?> - <?= substr($s['heure_fin'],0,5) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($s['titre']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($s['prenom'] . ' ' . $s['nom']) ?></td>
                    <td class="px-6 py-4"><?= $s['salle'] ?></td>
                    <td class="px-6 py-4">
                        <?php if ($evaluations[$s['id']]): ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Évalué</span>
                        <?php else: ?>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="<?= BASE_URL ?>/jury/telechargerMemoire/<?= $s['id'] ?>" class="text-blue-600 hover:text-blue-900" target="_blank">📄 Mémoire</a>
                        <?php if (!$evaluations[$s['id']]): ?>
                            <a href="<?= BASE_URL ?>/jury/evaluer/<?= $s['id'] ?>" class="text-green-600 hover:text-green-900">✏️ Évaluer</a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/jury/evaluer/<?= $s['id'] ?>" class="text-purple-600 hover:text-purple-900">👁️ Voir évaluation</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>