<?php include 'views/layouts/header.php'; ?>
<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h2 class="text-2xl font-bold mb-6">Ajouter un utilisateur</h2>
    
    <?php if (isset($error) && !empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="nom">Nom</label>
            <input type="text" name="nom" id="nom" required class="shadow border rounded w-full py-2 px-3">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" required class="shadow border rounded w-full py-2 px-3">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
            <input type="email" name="email" id="email" required class="shadow border rounded w-full py-2 px-3">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="role">Rôle</label>
            <select name="role" id="role" required class="shadow border rounded w-full py-2 px-3">
                <option value="etudiant">Étudiant</option>
                <option value="encadreur">Encadreur</option>
                <option value="responsable">Responsable</option>
                <option value="admin">Admin</option>
                <option value="jury">Membre de jury</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="filiere">Filière</label>
            <input type="text" name="filiere" id="filiere" class="shadow border rounded w-full py-2 px-3">
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required class="shadow border rounded w-full py-2 px-3">
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Créer</button>
        <a href="<?= BASE_URL ?>/admin/utilisateurs" class="ml-2 text-gray-600 hover:underline">Annuler</a>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>