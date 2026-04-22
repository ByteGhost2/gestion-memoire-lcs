<?php include 'views/layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h1 class="text-2xl font-bold mb-4">Évaluation de la soutenance</h1>
    <p class="mb-2"><strong>Mémoire :</strong> <?= htmlspecialchars($soutenance['titre']) ?></p>
    <p class="mb-2"><strong>Étudiant :</strong> <?= htmlspecialchars($soutenance['prenom'] . ' ' . $soutenance['nom']) ?></p>
    <p class="mb-4"><strong>Date :</strong> <?= date('d/m/Y', strtotime($soutenance['date'])) ?> à <?= substr($soutenance['heure_debut'],0,5) ?></p>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="note">Note (sur 20)</label>
            <input type="number" step="0.25" min="0" max="20" name="note" id="note" value="<?= $evaluation['note'] ?? '' ?>" required class="shadow border rounded w-full py-2 px-3">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="appreciation">Appréciation</label>
            <textarea name="appreciation" id="appreciation" rows="4" class="shadow border rounded w-full py-2 px-3"><?= htmlspecialchars($evaluation['appreciation'] ?? '') ?></textarea>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Enregistrer</button>
            <a href="<?= BASE_URL ?>/jury/dashboard" class="text-blue-600 hover:underline">Retour</a>
        </div>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>