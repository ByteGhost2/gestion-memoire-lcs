<?php include 'views/layouts/header.php'; ?>
<div class="max-w-4xl mx-auto" data-aos="fade-up">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-4">Gestion du jury pour la soutenance</h1>
        <p class="mb-2"><strong>Mémoire :</strong> <?= htmlspecialchars($soutenance['titre']) ?></p>
        <p class="mb-2"><strong>Étudiant :</strong> <?= htmlspecialchars($soutenance['prenom'] . ' ' . $soutenance['nom']) ?></p>
        <p class="mb-4"><strong>Date :</strong> <?= date('d/m/Y', strtotime($soutenance['date'])) ?> à <?= substr($soutenance['heure_debut'],0,5) ?></p>

        <h2 class="text-xl font-semibold mb-3">Membres actuels du jury</h2>
        <?php if (empty($jury)): ?>
            <p class="text-gray-500 mb-4">Aucun membre affecté pour l'instant.</p>
        <?php else: ?>
            <ul class="list-disc pl-5 mb-4">
                <?php foreach ($jury as $m): ?>
                    <li><?= $m['prenom'] . ' ' . $m['nom'] ?> (<?= $m['role'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h2 class="text-xl font-semibold mb-3">Ajouter un membre</h2>
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="id_utilisateur">Enseignant</label>
                <select name="id_utilisateur" id="id_utilisateur" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    <?php foreach ($enseignants as $ens): ?>
                        <option value="<?= $ens['id'] ?>"><?= $ens['prenom'] . ' ' . $ens['nom'] . ' (' . $ens['email'] . ')' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">Rôle</label>
                <select name="role" id="role" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    <option value="president">Président</option>
                    <option value="examinateur">Examinateur</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Ajouter</button>
        </form>

        <div class="mt-6">
            <a href="<?= BASE_URL ?>/soutenance/planning" class="text-blue-600 hover:underline">Retour au planning</a>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>