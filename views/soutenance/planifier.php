<?php include 'views/layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h2 class="text-2xl font-bold mb-6">Planifier une soutenance</h2>
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="id_memoire">Mémoire validé</label>
            <select name="id_memoire" id="id_memoire" required class="shadow border rounded w-full py-2 px-3">
                <option value="">Choisir...</option>
                <?php foreach ($memoires as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['titre']) ?> (<?= $m['prenom'] ?> <?= $m['nom'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="date">Date</label>
            <input type="date" name="date" id="date" required class="shadow border rounded w-full py-2 px-3">
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="heure_debut">Heure début</label>
                <input type="time" name="heure_debut" id="heure_debut" required class="shadow border rounded w-full py-2 px-3">
            </div>
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="heure_fin">Heure fin</label>
                <input type="time" name="heure_fin" id="heure_fin" required class="shadow border rounded w-full py-2 px-3">
            </div>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="salle">Salle</label>
            <input type="text" name="salle" id="salle" required class="shadow border rounded w-full py-2 px-3">
        </div>
        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Planifier</button>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>