<?php
// Inclusion du fichier de thème pour définir $theme (nécessaire pour le lien de mode)
require_once __DIR__ . '/../../config/theme.php';
?>
        </div> <!-- fin contenu principal -->
    </main>
</div>

<footer class="relative bg-cover bg-center py-12 mt-auto" style="background-image: url('<?= BASE_URL ?>/public/images/footer-bg.jpg'); overflow: visible;">
    <div class="absolute inset-0 bg-gradient-to-r from-gray-900/95 to-gray-800/95 dark:from-gray-950 dark:to-gray-900"></div>
    <div class="container mx-auto px-4 relative z-10 text-white">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Colonne 1 : Logo et présentation -->
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <img src="<?= BASE_URL ?>/public/images/logo-iucs.jpg" alt="Logo IUCS" class="h-10 w-auto rounded-full border border-white">
                    <h3 class="text-xl font-bold border-b-2 border-blue-400 pb-2">e-Mémoire LCS</h3>
                </div>
                <p class="text-gray-300">Plateforme de gestion des mémoires de l'Institut Universitaire Les COURS SONOU.</p>
                <img src="<?= BASE_URL ?>/public/images/lcs-hall.jpg" alt="Institut Universitaire Les COURS SONOU" class="mt-4 rounded-lg shadow-md w-full">
            </div>

            <!-- Colonne 2 : Liens utiles -->
            <div>
                <h3 class="text-xl font-bold mb-4 border-b-2 border-blue-400 pb-2 inline-block">Liens utiles</h3>
                <ul class="space-y-2 mt-2">
                    <li><a href="<?= BASE_URL ?>/" class="text-gray-300 hover:text-white transition">Accueil</a></li>
                    <li><a href="<?= BASE_URL ?>/page/apropos" class="text-gray-300 hover:text-white transition">À propos</a></li>
                    <li><a href="<?= BASE_URL ?>/page/contact" class="text-gray-300 hover:text-white transition">Contact</a></li>
                    <li><a href="<?= BASE_URL ?>/bibliotheque" class="text-gray-300 hover:text-white transition">Bibliothèque</a></li>
                </ul>
            </div>

            <!-- Colonne 3 : Espace utilisateur -->
            <div>
                <h3 class="text-xl font-bold mb-4 border-b-2 border-blue-400 pb-2 inline-block">Espace</h3>
                <ul class="space-y-2 mt-2">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li><a href="<?= BASE_URL ?>/profil" class="text-gray-300 hover:text-white transition">Mon profil</a></li>
                        <li><a href="<?= BASE_URL ?>/auth/logout" class="text-gray-300 hover:text-white transition">Déconnexion</a></li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>/auth/login" class="text-gray-300 hover:text-white transition">Connexion</a></li>
                        <li><a href="<?= BASE_URL ?>/auth/register" class="text-gray-300 hover:text-white transition">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Colonne 4 : Contact et adresse -->
            <div>
                <h3 class="text-xl font-bold mb-4 border-b-2 border-blue-400 pb-2 inline-block">Contact</h3>
                <address class="not-italic text-gray-300">
                    <p><i class="fas fa-envelope mr-2"></i> lescoursonou-universite.org</p>
                    <p><i class="fas fa-phone mr-2"></i> +229 01 60 20 41 41</p>
                    <p><i class="fas fa-map-marker-alt mr-2"></i> Cotonou, Bénin</p>
                </address>
                <img src="<?= BASE_URL ?>/public/images/campus-mini.jpg" alt="Campus" class="mt-4 rounded-lg shadow-md w-full">
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-4 text-center text-gray-400">
            &copy; <?= date('Y') ?> e-Mémoire LCS - Institut Universitaire Les COURS SONOU - Tous droits réservés. 
            <a href="?toggle_theme=1" class="ml-4 text-blue-400 hover:underline">
                <?= (isset($theme) && $theme === 'dark') ? 'Mode clair' : 'Mode sombre' ?>
            </a>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="<?= BASE_URL ?>/public/js/app.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true,
        offset: 100
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const notifButton = document.getElementById('notifButton');
    const notifDropdown = document.getElementById('notifDropdown');
    if (notifButton && notifDropdown) {
        notifButton.addEventListener('click', function(e) {
            e.stopPropagation();
            notifDropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', function(e) {
            if (!notifDropdown.contains(e.target) && e.target !== notifButton) {
                notifDropdown.classList.add('hidden');
            }
        });
        notifDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function(e) {
                const id = this.dataset.id;
                if (id) {
                    fetch('<?= BASE_URL ?>/notification/markAsRead', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    }).catch(err => console.error('Erreur lors du marquage', err));
                }
            });
        });
    }
});
</script>
</body>
</html>