<?php
$user = $_SESSION['user'] ?? null;
$role = $user ? $user['role'] : '';
?>
<aside class="w-64 bg-white dark:bg-gray-800 text-gray-800 dark:text-white min-h-screen p-4 shadow-xl transition-colors duration-300">
    <div class="mb-8 text-center">
        <?php if (!empty($user['photo'])): ?>
            <img src="<?= BASE_URL . '/' . $user['photo'] ?>" alt="Photo de profil" class="w-20 h-20 mx-auto rounded-full object-cover border-2 border-white shadow-lg">
        <?php else: ?>
            <div class="w-20 h-20 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                <?= strtoupper(substr($user['prenom'] ?? 'U', 0, 1) . substr($user['nom'] ?? '', 0, 1)) ?>
            </div>
        <?php endif; ?>
        <h2 class="text-xl font-bold mt-2"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 capitalize"><?= $role ?></p>
    </div>



    <nav>
        <ul class="space-y-2">
            <!-- ==================== SECTION RESPONSABLE ==================== -->
            <?php if ($role === 'responsable'): ?>
                <li><a href="<?= BASE_URL ?>/admin/dashboard" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-tachometer-alt mr-3 text-blue-500"></i> Dashboard
                </a></li>
                <li class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">Gestion académique</span>
                </li>
                <li><a href="<?= BASE_URL ?>/admin/memoires" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-file-alt mr-3 text-indigo-500"></i> Mémoires
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/soutenances" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-calendar-check mr-3 text-teal-500"></i> Soutenances
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/planifierSoutenance" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-plus-circle mr-3 text-green-500"></i> Planifier
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/bibliotheque" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-book mr-3 text-pink-500"></i> Bibliothèque
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/statistiques" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-chart-bar mr-3 text-orange-500"></i> Statistiques
                </a></li>
            <?php endif; ?>

            <!-- ==================== SECTION ADMIN (complet) ==================== -->
            <?php if ($role === 'admin'): ?>
                <li><a href="<?= BASE_URL ?>/admin/dashboard" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-tachometer-alt mr-3 text-blue-500"></i> Dashboard Admin
                </a></li>
                <li class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">Gestion</span>
                </li>
                <li><a href="<?= BASE_URL ?>/admin/utilisateurs" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-users mr-3 text-blue-500"></i> Utilisateurs
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/filieres" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-tags mr-3 text-green-500"></i> Filières
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/salles" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-door-open mr-3 text-yellow-500"></i> Salles
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/annees" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-calendar mr-3 text-purple-500"></i> Années univ.
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/criteres" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-star mr-3 text-orange-500"></i> Critères
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/matricules" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-id-card mr-3 text-blue-500"></i> Matricules
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/logs" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-history mr-3 text-gray-500"></i> Logs
                </a></li>
                <li><a href="<?= BASE_URL ?>/admin/parametres" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-cogs mr-3 text-gray-600"></i> Paramètres
                </a></li>
            <?php endif; ?>

            <!-- ==================== SECTION ENSEIGNANT (encadreur/jury) ==================== -->
            <?php if (in_array($role, ['encadreur', 'jury'])): ?>
                <li><a href="<?= BASE_URL ?>/enseignant/dashboard" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-chalkboard-teacher mr-3 text-green-500"></i> Dashboard enseignant
                </a></li>
                <li><a href="<?= BASE_URL ?>/enseignant/calendrier" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-calendar-alt mr-3 text-purple-500"></i> Calendrier
                </a></li>
                <li><a href="<?= BASE_URL ?>/disponibilite" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-clock mr-3 text-orange-500"></i> Mes disponibilités
                </a></li>
            <?php endif; ?>

            <!-- ==================== SECTION ÉTUDIANT ==================== -->
            <?php if ($role === 'etudiant'): ?>
                <li><a href="<?= BASE_URL ?>/dashboard" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-tachometer-alt mr-3 text-blue-500"></i> Tableau de bord
                </a></li>
                <li class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">Mémoires</span>
                </li>
                <li><a href="<?= BASE_URL ?>/memoire" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-file-alt mr-3 text-blue-500"></i> Mes mémoires
                </a></li>
                <li><a href="<?= BASE_URL ?>/memoire/soumettre" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-upload mr-3 text-green-500"></i> Soumettre un sujet
                </a></li>
                <li><a href="<?= BASE_URL ?>/attestation/mesAttestations" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                    <i class="fas fa-certificate mr-3 text-purple-500"></i> Mes attestations
                </a></li>
            <?php endif; ?>

            <!-- ==================== LIENS COMMUNS À TOUS ==================== -->
            <li class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">Outils</span>
            </li>
            <li><a href="<?= BASE_URL ?>/soutenance/planning" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                <i class="fas fa-calendar-check mr-3 text-purple-500"></i> Planning des soutenances
            </a></li>
            <li><a href="<?= BASE_URL ?>/bibliotheque" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                <i class="fas fa-book-open mr-3 text-yellow-500"></i> Bibliothèque
            </a></li>

            <!-- ==================== PROFIL ==================== -->
            <li class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">Mon compte</span>
            </li>
            <li><a href="<?= BASE_URL ?>/profil" class="block py-2 px-4 rounded-lg hover:bg-blue-50 dark:hover:bg-gray-700 transition flex items-center">
                <i class="fas fa-user-circle mr-3 text-indigo-500"></i> Mon profil
            </a></li>

            <!-- ==================== DÉCONNEXION ==================== -->
            <li><a href="<?= BASE_URL ?>/auth/logout" class="block py-2 px-4 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition flex items-center">
                <i class="fas fa-sign-out-alt mr-3"></i> Déconnexion
            </a></li>
        </ul>
    </nav>
</aside>