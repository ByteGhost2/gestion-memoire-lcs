<?php include 'views/layouts/header.php'; ?>
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden" data-aos="fade-up">
        <div class="h-32 bg-cover bg-center" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-classroom.jpg');"></div>
        <div class="p-8">
            <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Mon profil</h1>

            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['flash_success'] ?>
                    <?php unset($_SESSION['flash_success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['flash_error'])): ?>
                <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['flash_error'] ?>
                    <?php unset($_SESSION['flash_error']); ?>
                </div>
            <?php endif; ?>

            <!-- Photo de profil -->
            <div class="flex flex-col items-center mb-8">
                <?php if (!empty($user['photo'])): ?>
                    <img src="<?= BASE_URL ?>/<?= $user['photo'] ?>" alt="Photo" class="w-32 h-32 rounded-full object-cover border-4 border-blue-500 shadow-lg">
                <?php else: ?>
                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                        <?= strtoupper(substr($user['prenom'] ?? 'U', 0, 1) . substr($user['nom'] ?? '', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="<?= BASE_URL ?>/profil/updatePhoto" enctype="multipart/form-data" class="mt-4">
                    <input type="file" name="photo" accept="image/*" required class="mb-2 text-sm text-gray-600 dark:text-gray-400">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">Uploader</button>
                </form>
            </div>

            <!-- Formulaire infos -->
            <form method="POST" action="<?= BASE_URL ?>/profil/update" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nom</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Prénom</label>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Filière</label>
                    <input type="text" name="filiere" value="<?= htmlspecialchars($user['filiere'] ?? '') ?>" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105">Mettre à jour</button>
            </form>

            <hr class="my-8 border-gray-200 dark:border-gray-700">

            <!-- Changer mot de passe -->
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Changer de mot de passe</h2>
            <form method="POST" action="<?= BASE_URL ?>/profil/changerMotDePasse" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Ancien mot de passe</label>
                    <input type="password" name="ancien" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Nouveau mot de passe</label>
                    <input type="password" name="nouveau" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Confirmer</label>
                    <input type="password" name="confirme" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105">Changer</button>
            </form>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>