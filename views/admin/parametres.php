<?php include 'views/layouts/header.php'; ?>
<div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8" data-aos="fade-up">
    <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Paramètres généraux</h2>
    
    <?php if (isset($success)): ?>
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4"><?= $success ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations établissement -->
            <div class="border p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Informations de l'établissement</h3>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nom de l'établissement</label>
                    <input type="text" name="nom_etablissement" value="<?= htmlspecialchars($settings['nom_etablissement'] ?? '') ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Email de contact</label>
                    <input type="email" name="email_contact" value="<?= htmlspecialchars($settings['email_contact'] ?? '') ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Téléphone</label>
                    <input type="text" name="telephone" value="<?= htmlspecialchars($settings['telephone'] ?? '') ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Adresse</label>
                    <input type="text" name="adresse" value="<?= htmlspecialchars($settings['adresse'] ?? '') ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Site web</label>
                    <input type="url" name="site_web" value="<?= htmlspecialchars($settings['site_web'] ?? '') ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                </div>
            </div>
            
            <!-- Réseaux sociaux -->
            <div class="border p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Réseaux sociaux</h3>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Facebook</label>
                    <input type="url" name="facebook" value="<?= htmlspecialchars($settings['facebook'] ?? '') ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Twitter</label>
                    <input type="url" name="twitter" value="<?= htmlspecialchars($settings['twitter'] ?? '') ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">LinkedIn</label>
                    <input type="url" name="linkedin" value="<?= htmlspecialchars($settings['linkedin'] ?? '') ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Instagram</label>
                    <input type="url" name="instagram" value="<?= htmlspecialchars($settings['instagram'] ?? '') ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                </div>
            </div>
            
            <!-- Configuration email -->
            <div class="border p-4 rounded-lg md:col-span-2">
                <h3 class="text-lg font-semibold mb-4">Configuration email (SMTP)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Serveur SMTP</label>
                        <input type="text" name="mail_host" value="<?= htmlspecialchars($settings['mail_host'] ?? MAIL_HOST) ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Port SMTP</label>
                        <input type="text" name="mail_port" value="<?= htmlspecialchars($settings['mail_port'] ?? MAIL_PORT) ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Utilisateur SMTP</label>
                        <input type="text" name="mail_user" value="<?= htmlspecialchars($settings['mail_user'] ?? MAIL_USER) ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Mot de passe SMTP</label>
                        <input type="password" name="mail_pass" value="<?= htmlspecialchars($settings['mail_pass'] ?? MAIL_PASS) ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Email d'envoi</label>
                        <input type="email" name="mail_from" value="<?= htmlspecialchars($settings['mail_from'] ?? MAIL_FROM) ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Nom d'envoi</label>
                        <input type="text" name="mail_from_name" value="<?= htmlspecialchars($settings['mail_from_name'] ?? MAIL_FROM_NAME) ?>" class="w-full px-3 py-2 border rounded dark:bg-gray-700">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">Enregistrer</button>
        </div>
    </form>
</div>
<?php include 'views/layouts/footer.php'; ?>