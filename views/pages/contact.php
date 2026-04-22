<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto py-12 px-4">
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden" data-aos="fade-up">
        <div class="h-48 bg-cover bg-center" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-contact.jpg');"></div>
        <div class="p-8">
            <h1 class="text-3xl font-bold mb-6 text-center text-gray-900 dark:text-white">Contactez-nous</h1>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">
                    <?php foreach ($errors as $e): ?>
                        <p><?= $e ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">
                    <?= $success ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="nom">Nom complet</label>
                    <input type="text" name="nom" id="nom" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="email">Email</label>
                    <input type="email" name="email" id="email" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="sujet">Sujet</label>
                    <input type="text" name="sujet" id="sujet" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="message">Message</label>
                    <textarea name="message" id="message" rows="6" required class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700"></textarea>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105">
                    Envoyer
                </button>
            </form>
            
            <div class="mt-8 text-center text-gray-600 dark:text-gray-400">
                <p><i class="fas fa-phone mr-2"></i> <?= htmlspecialchars($settings['telephone'] ?? '+229 01 60 20 41 41') ?></p>
                <p><i class="fas fa-envelope mr-2"></i> <?= htmlspecialchars($settings['email_contact'] ?? 'lescoursonou-universite.org') ?></p>
                <p><i class="fas fa-map-marker-alt mr-2"></i> <?= htmlspecialchars($settings['adresse'] ?? 'Cotonou, Bénin') ?></p>
            </div>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>