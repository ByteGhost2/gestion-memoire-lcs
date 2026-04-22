<?php include 'views/layouts/header.php'; ?>
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-cover bg-center" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-campus.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="relative z-10 max-w-md w-full space-y-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold mb-4 text-center">Authentification à deux facteurs</h2>
            <p class="mb-4 text-center">Veuillez entrer le code généré par votre application.</p>
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Code</label>
                    <input type="text" name="code" pattern="[0-9]{6}" required class="w-full px-3 py-2 border rounded-lg">
                </div>
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Vérifier</button>
            </form>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>