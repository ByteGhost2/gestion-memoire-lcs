<?php include 'views/layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-8" data-aos="fade-up">
    <h1 class="text-2xl font-bold mb-4">Évaluation de la soutenance</h1>
    <p class="mb-2"><strong>Mémoire :</strong> <?= htmlspecialchars($soutenance['titre']) ?></p>
    <p class="mb-2"><strong>Étudiant :</strong> <?= htmlspecialchars($soutenance['prenom'] . ' ' . $soutenance['nom']) ?></p>
    <p class="mb-4"><strong>Date :</strong> <?= date('d/m/Y', strtotime($soutenance['date'])) ?> à <?= substr($soutenance['heure_debut'],0,5) ?></p>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <?php if (!empty($criteres)): ?>
            <h3 class="text-lg font-semibold mb-2">Évaluation par critères</h3>
            <table class="min-w-full mb-4">
                <thead>
                    <tr>
                        <th class="text-left">Critère</th>
                        <th class="text-left">Poids</th>
                        <th class="text-left">Note /20</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($criteres as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['nom']) ?></td>
                        <td><?= $c['poids'] ?></td>
                        <td><input type="number" name="note_critere[<?= $c['id'] ?>]" min="0" max="20" step="0.5" class="w-20 border rounded p-1 dark:bg-gray-700"></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Note (sur 20)</label>
                <input type="number" step="0.25" min="0" max="20" name="note" id="note" value="<?= $evaluation['note'] ?? '' ?>" required class="shadow border rounded w-full py-2 px-3">
            </div>
        <?php endif; ?>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mention</label>
            <select name="mention" id="mention" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700">
                <option value="">-- Calcul automatique --</option>
                <option value="Passable">Passable</option>
                <option value="Assez bien">Assez bien</option>
                <option value="Bien">Bien</option>
                <option value="Très bien">Très bien</option>
                <option value="Excellent">Excellent</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Appréciation</label>
            <textarea name="appreciation" id="appreciation" rows="4" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700"><?= htmlspecialchars($evaluation['appreciation'] ?? '') ?></textarea>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Enregistrer</button>
            <a href="<?= BASE_URL ?>/enseignant/dashboard" class="text-blue-600 hover:underline">Retour</a>
        </div>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>