<?php include 'views/layouts/header.php'; ?>
<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h2 class="text-2xl font-bold mb-6">Upload d'attestation pour <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h2>
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php foreach ($errors as $e): ?>
                <p><?= $e ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="attestation">Fichier PDF (max 5 Mo)</label>
            <input type="file" name="attestation" id="attestation" accept=".pdf" required class="w-full">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="mention">Mention (optionnelle)</label>
            <select name="mention" id="mention" class="shadow border rounded w-full py-2 px-3">
                <option value="">-- Sélectionner --</option>
                <option value="Passable">Passable</option>
                <option value="Assez bien">Assez bien</option>
                <option value="Bien">Bien</option>
                <option value="Très bien">Très bien</option>
                <option value="Excellent">Excellent</option>
            </select>
        </div>
        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Uploader</button>
        <a href="<?= BASE_URL ?>/attestation" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>