<?php include 'views/layouts/header.php'; ?>
<div class="max-w-2xl mx-auto" data-aos="fade-up">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-4">Évaluation de soutenance</h1>
        <p><strong>Mémoire :</strong> <?= htmlspecialchars($soutenance['titre']) ?></p>
        <p><strong>Étudiant :</strong> <?= htmlspecialchars($soutenance['prenom'] . ' ' . $soutenance['nom']) ?></p>
        <p class="mb-4"><strong>Date :</strong> <?= date('d/m/Y', strtotime($soutenance['date'])) ?></p>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="note">Note (sur 20)</label>
                <input type="number" step="0.25" min="0" max="20" name="note" id="note" value="<?= $evaluation['note'] ?? '' ?>" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="appreciation">Appréciation</label>
                <textarea name="appreciation" id="appreciation" rows="4" class="shadow border rounded w-full py-2 px-3 text-gray-700"><?= htmlspecialchars($evaluation['appreciation'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Enregistrer l'évaluation</button>
        </form>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>