<?php include 'views/layouts/header.php'; ?>
<div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Modifier l'utilisateur</h2>
    <?php if (isset($error) && !empty($error)): ?>
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user['nom']) ?>" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="role">Rôle</label>
            <select name="role" id="role" required class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
                <option value="etudiant" <?= $user['role'] == 'etudiant' ? 'selected' : '' ?>>Étudiant</option>
                <option value="encadreur" <?= $user['role'] == 'encadreur' ? 'selected' : '' ?>>Encadreur</option>
                <option value="responsable" <?= $user['role'] == 'responsable' ? 'selected' : '' ?>>Responsable</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="jury" <?= $user['role'] == 'jury' ? 'selected' : '' ?>>Membre de jury</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="filiere">Filière</label>
            <input type="text" name="filiere" id="filiere" value="<?= htmlspecialchars($user['filiere'] ?? '') ?>" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="new_password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
            <input type="password" name="new_password" id="new_password" class="shadow border rounded w-full py-2 px-3 dark:bg-gray-700 dark:text-white">
            <p class="text-xs text-gray-500 mt-1">Minimum 6 caractères</p>
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Mettre à jour</button>
            <a href="<?= BASE_URL ?>/admin/utilisateurs" class="text-gray-600 dark:text-gray-400 hover:underline">Annuler</a>
        </div>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>