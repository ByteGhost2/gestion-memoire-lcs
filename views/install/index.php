<?php include 'views/layouts/header.php'; ?>
<div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h1 class="text-2xl font-bold mb-6 text-center">Bienvenue sur e-Mémoire LCS</h1>
    <p class="mb-4 text-gray-600 text-center">Aucun utilisateur n'existe. Veuillez créer le compte administrateur.</p>
    
    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php foreach ($errors as $e): ?>
                <p><?= $e ?></p>
            <?php endforeach; ?>
        </div>
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
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required class="shadow border rounded w-full py-2 px-3">
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" name="confirm_password" id="confirm_password" required class="shadow border rounded w-full py-2 px-3">
        </div>
        <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">Créer le compte administrateur</button>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>