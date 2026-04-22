<?php include 'views/layouts/header.php'; ?>

<!-- Hero Section -->
<section class="relative h-96 flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80" alt="Bibliothèque" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900 to-purple-900 opacity-80"></div>
    </div>
    <div class="relative z-10 text-center text-white">
        <h1 class="text-5xl md:text-6xl font-bold mb-4" data-aos="fade-up">À propos de nous</h1>
        <p class="text-xl max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">Découvrez notre mission et notre vision derrière e-Mémoire LCS</p>
    </div>
</section>

<!-- Section Notre Mission -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center gap-12">
            <div class="md:w-1/2" data-aos="fade-right">
                <h2 class="text-4xl font-bold mb-6 text-gray-900 dark:text-white">Notre Mission</h2>
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-4"><strong>e-Mémoire LCS</strong> est une plateforme web innovante développée dans le cadre du projet de fin d'études de Licence Professionnelle en Systèmes Informatiques à l'Institut Universitaire Les COURS SONOU (LCS).</p>
                <p class="text-lg text-gray-700 dark:text-gray-300">Notre mission est de moderniser et sécuriser la gestion des mémoires au sein de LCS en offrant une solution centralisée, transparente et efficace pour les étudiants, les encadreurs, les responsables et les membres de jury.</p>
            </div>
            <div class="md:w-1/2" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1950&q=80" alt="Équipe" class="rounded-2xl shadow-2xl">
            </div>
        </div>
    </div>
</section>

<!-- Section Chiffres clés -->
<section class="py-20 bg-gray-100 dark:bg-gray-800">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold mb-12 text-gray-900 dark:text-white" data-aos="fade-up">Quelques chiffres</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div data-aos="zoom-in">
                <div class="text-5xl font-bold text-blue-600"><?= $stats['annees'] ?></div>
                <div class="text-lg text-gray-700 dark:text-gray-300">Nouvelle plateforme</div>
            </div>
            <div data-aos="zoom-in" data-aos-delay="100">
                <div class="text-5xl font-bold text-green-600"><?= $stats['memoires'] ?>+</div>
                <div class="text-lg text-gray-700 dark:text-gray-300">Mémoires gérés</div>
            </div>
            <div data-aos="zoom-in" data-aos-delay="200">
                <div class="text-5xl font-bold text-purple-600"><?= $stats['encadreurs'] ?>+</div>
                <div class="text-lg text-gray-700 dark:text-gray-300">Encadreurs</div>
            </div>
            <div data-aos="zoom-in" data-aos-delay="300">
                <div class="text-5xl font-bold text-yellow-600"><?= $stats['etudiants'] ?>+</div>
                <div class="text-lg text-gray-700 dark:text-gray-300">Étudiants</div>
            </div>
        </div>
    </div>
</section>

<!-- Section Fonctionnalités clés -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <h2 class="text-4xl font-bold text-center mb-12 text-gray-900 dark:text-white" data-aos="fade-up">Fonctionnalités clés</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition" data-aos="fade-up">
                <i class="fas fa-cloud-upload-alt text-4xl text-blue-500 mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Dépôt en ligne</h3>
                <p class="text-gray-600 dark:text-gray-400">Déposez vos sujets et versions de mémoires en quelques clics.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition" data-aos="fade-up" data-aos-delay="50">
                <i class="fas fa-comments text-4xl text-green-500 mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Feedback interactif</h3>
                <p class="text-gray-600 dark:text-gray-400">Recevez des commentaires détaillés de vos encadreurs.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition" data-aos="fade-up" data-aos-delay="100">
                <i class="fas fa-calendar-alt text-4xl text-purple-500 mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Planification des soutenances</h3>
                <p class="text-gray-600 dark:text-gray-400">Gérez les jurys, les salles et les plannings automatiquement.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition" data-aos="fade-up" data-aos-delay="150">
                <i class="fas fa-book-open text-4xl text-yellow-500 mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Bibliothèque numérique</h3>
                <p class="text-gray-600 dark:text-gray-400">Accédez à tous les mémoires soutenus, recherchez par mots-clés.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-chart-line text-4xl text-red-500 mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Statistiques avancées</h3>
                <p class="text-gray-600 dark:text-gray-400">Suivez les tendances, les mentions, et l'évolution des dépôts.</p>
            </div>
            <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition" data-aos="fade-up" data-aos-delay="250">
                <i class="fas fa-certificate text-4xl text-indigo-500 mb-4"></i>
                <h3 class="text-xl font-bold mb-2">Attestations automatiques</h3>
                <p class="text-gray-600 dark:text-gray-400">Téléversez les attestations de soutenance des étudiants en PDF.</p>
            </div>
        </div>
    </div>
</section>



<?php include 'views/layouts/footer.php'; ?>