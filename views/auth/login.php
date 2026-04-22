<?php include 'views/layouts/header.php'; ?>
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-cover bg-center" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-campus.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="relative z-10 max-w-md w-full space-y-8" data-aos="fade-up">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="h-32 bg-cover bg-center" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-classroom.jpg');"></div>
            <div class="p-8">
                <div class="text-center mb-6">
                    <img src="<?= BASE_URL ?>/public/images/logo-iucs.jpg" alt="Logo IUCS" class="h-16 mx-auto mb-3">
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Connexion</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Accédez à votre espace e-Mémoire LCS</p>
                </div>
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4"><?= $error ?></div>
                <?php endif; ?>
                <?php if (isset($_GET['registered'])): ?>
                    <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-4">Inscription réussie ! Connectez-vous.</div>
                <?php endif; ?>
                <form method="POST" action="" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="email">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" required class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="password">Mot de passe</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" required class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition transform hover:scale-105">
                        Se connecter
                    </button>
                </form>
                <p class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
                    Pas encore de compte ? <a href="<?= BASE_URL ?>/auth/register" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">Inscrivez-vous</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
</script>
<?php include 'views/layouts/footer.php'; ?>