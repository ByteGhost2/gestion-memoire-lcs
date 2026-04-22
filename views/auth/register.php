<?php include 'views/layouts/header.php'; ?>
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-cover bg-center" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-hall.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="relative z-10 max-w-md w-full space-y-8" data-aos="fade-up">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="h-32 bg-cover bg-center" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-classroom.jpg');"></div>
            <div class="p-8">
                <div class="text-center mb-6">
                    <img src="<?= BASE_URL ?>/public/images/logo-iucs.jpg" alt="Logo IUCS" class="h-16 mx-auto mb-3">
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white">Inscription</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Créez votre compte étudiant</p>
                </div>
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-4">
                        <?php foreach ($errors as $e): ?>
                            <p><?= $e ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="matricule">Numéro matricule</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"><i class="fas fa-id-card"></i></span>
                            <input type="text" name="matricule" id="matricule" required class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Votre matricule vous a été fourni par le service scolarité.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="nom">Nom</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"><i class="fas fa-user"></i></span>
                            <input type="text" name="nom" id="nom" required class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="prenom">Prénom</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"><i class="fas fa-user"></i></span>
                            <input type="text" name="prenom" id="prenom" required class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="email">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" required class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="filiere">Filière</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"><i class="fas fa-graduation-cap"></i></span>
                            <input type="text" name="filiere" id="filiere" placeholder="Ex: Systèmes Informatiques" required class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="password">Mot de passe</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"><i class="fas fa-key"></i></span>
                            <input type="password" name="password" id="password" required class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div id="passwordStrengthBar" class="bg-red-500 h-1.5 rounded-full" style="width: 0%"></div>
                            </div>
                            <p id="passwordStrengthText" class="text-xs text-gray-500 mt-1">Minimum 6 caractères</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2" for="confirm_password">Confirmer le mot de passe</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500"><i class="fas fa-check-circle"></i></span>
                            <input type="password" name="confirm_password" id="confirm_password" required class="pl-10 w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 dark:bg-gray-700 dark:text-white">
                            <span id="passwordMatchIcon" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500">
                                <i class="fas fa-times-circle text-red-500"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition transform hover:scale-105">
                        S'inscrire
                    </button>
                </form>
                <p class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
                    Déjà inscrit ? <a href="<?= BASE_URL ?>/auth/login" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">Connectez-vous</a>
                </p>
            </div>
        </div>
    </div>
</div>
<script>
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('passwordStrengthBar');
    const strengthText = document.getElementById('passwordStrengthText');
    const matchIcon = document.getElementById('passwordMatchIcon');
    const togglePassword = document.getElementById('togglePassword');

    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;
        let percentage = Math.min(strength * 16, 100);
        let color, text;
        if (percentage < 20) { color = 'bg-red-500'; text = 'Très faible'; }
        else if (percentage < 40) { color = 'bg-orange-500'; text = 'Faible'; }
        else if (percentage < 60) { color = 'bg-yellow-500'; text = 'Moyen'; }
        else if (percentage < 80) { color = 'bg-blue-500'; text = 'Fort'; }
        else { color = 'bg-green-500'; text = 'Très fort'; }
        strengthBar.className = `h-1.5 rounded-full ${color}`;
        strengthBar.style.width = percentage + '%';
        strengthText.innerHTML = `Force : ${text} (${password.length} caractères)`;
    }

    function checkPasswordMatch() {
        if (confirmInput.value.length === 0) return;
        if (passwordInput.value === confirmInput.value) {
            matchIcon.innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
        } else {
            matchIcon.innerHTML = '<i class="fas fa-times-circle text-red-500"></i>';
        }
    }

    passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
    });
    confirmInput.addEventListener('input', checkPasswordMatch);
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
</script>
<?php include 'views/layouts/footer.php'; ?>