<?php include 'views/layouts/header.php'; ?>
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Planifier une soutenance</h2>
    <?php if (isset($error)): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="" id="planificationForm">
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="id_memoire">Mémoire validé</label>
            <select name="id_memoire" id="id_memoire" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
                <option value="">Choisir...</option>
                <?php foreach ($memoires as $m): ?>
                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['titre']) ?> (<?= $m['prenom'] ?> <?= $m['nom'] ?>)</option>
                <?php endforeach; ?>
            </select>
            <p class="text-sm text-gray-500 mt-1">Seuls les mémoires ayant le statut "validé" apparaissent.</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="date">Date</label>
            <input type="date" name="date" id="date" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="heure_debut">Heure début</label>
                <input type="time" name="heure_debut" id="heure_debut" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="heure_fin">Heure fin</label>
                <input type="time" name="heure_fin" id="heure_fin" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
            </div>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="salle">Salle</label>
            <select name="salle" id="salle" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
                <option value="">Choisir une salle</option>
                <?php foreach ($salles as $salle): ?>
                    <option value="<?= $salle ?>"><?= $salle ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex justify-between items-center">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Planifier</button>
            <button type="button" id="proposerBtn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Proposer des créneaux</button>
            <a href="<?= BASE_URL ?>/admin/soutenances" class="text-gray-600 dark:text-gray-400 hover:underline">Annuler</a>
        </div>
    </form>

    <div id="propositions" class="mt-6 hidden">
        <h3 class="text-lg font-semibold mb-2">Créneaux proposés</h3>
        <div id="propositionsList" class="space-y-2"></div>
    </div>
</div>

<script>
document.getElementById('proposerBtn').addEventListener('click', function() {
    const id_memoire = document.getElementById('id_memoire').value;
    if (!id_memoire) {
        alert('Veuillez d\'abord sélectionner un mémoire.');
        return;
    }
    fetch('<?= BASE_URL ?>/admin/proposerCreneaux/' + id_memoire)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('propositions');
            const list = document.getElementById('propositionsList');
            list.innerHTML = '';
            if (data.error) {
                list.innerHTML = '<p class="text-red-600">' + data.error + '</p>';
            } else if (data.length === 0) {
                list.innerHTML = '<p class="text-gray-600">Aucun créneau disponible dans les 7 prochains jours.</p>';
            } else {
                data.forEach(creneau => {
                    const div = document.createElement('div');
                    div.className = 'p-2 border rounded cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700';
                    div.innerHTML = `${creneau.date} de ${creneau.heure_debut} à ${creneau.heure_fin} - Salle ${creneau.salle}`;
                    div.addEventListener('click', function() {
                        document.getElementById('date').value = creneau.date;
                        document.getElementById('heure_debut').value = creneau.heure_debut;
                        document.getElementById('heure_fin').value = creneau.heure_fin;
                        document.getElementById('salle').value = creneau.salle;
                    });
                    list.appendChild(div);
                });
            }
            container.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la récupération des créneaux. Vérifiez la console.');
        });
});
</script>
<?php include 'views/layouts/footer.php'; ?>