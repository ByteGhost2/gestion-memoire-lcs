<?php include 'views/layouts/header.php'; ?>
<div class="max-w-4xl mx-auto" data-aos="fade-up">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-4">Gestion du jury pour la soutenance</h1>
        <p class="mb-2"><strong>Mémoire :</strong> <?= htmlspecialchars($soutenance['titre']) ?></p>
        <p class="mb-2"><strong>Étudiant :</strong> <?= htmlspecialchars($soutenance['prenom'] . ' ' . $soutenance['nom']) ?></p>
        <p class="mb-4"><strong>Date :</strong> <?= date('d/m/Y', strtotime($soutenance['date'])) ?> à <?= substr($soutenance['heure_debut'],0,5) ?></p>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= $_SESSION['flash_error'] ?>
                <?php unset($_SESSION['flash_error']); ?>
            </div>
        <?php endif; ?>

        <h2 class="text-xl font-semibold mb-3">Membres actuels du jury</h2>
        <?php if (empty($jury)): ?>
            <p class="text-gray-500 mb-4">Aucun membre affecté pour l'instant.</p>
        <?php else: ?>
            <ul class="list-disc pl-5 mb-4">
                <?php foreach ($jury as $m): ?>
                    <li class="flex justify-between items-center">
                        <span><?= $m['prenom'] . ' ' . $m['nom'] ?> (<?= $m['role'] ?>)</span>
                        <a href="<?= BASE_URL ?>/admin/retirerMembreJury/<?= $m['id'] ?>" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('Retirer ce membre ?')">Retirer</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h2 class="text-xl font-semibold mb-3">Ajouter un membre</h2>
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="id_utilisateur">Membre (encadreurs ou jurys)</label>
                <select name="id_utilisateur" id="id_utilisateur" required class="shadow border rounded w-full py-2 px-3">
                    <?php foreach ($enseignants as $ens): ?>
                        <option value="<?= $ens['id'] ?>"><?= $ens['prenom'] . ' ' . $ens['nom'] . ' (' . $ens['email'] . ')' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="role">Rôle</label>
                <select name="role" id="role" required class="shadow border rounded w-full py-2 px-3">
                    <option value="president">Président</option>
                    <option value="examinateur">Examinateur</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Ajouter</button>
        </form>

        <div class="mt-6">
            <a href="<?= BASE_URL ?>/admin/soutenances" class="text-blue-600 hover:underline">Retour à la liste</a>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>