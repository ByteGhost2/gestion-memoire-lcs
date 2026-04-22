<?php include 'views/layouts/header.php'; ?>

<!-- Hero Section avec photo emblématique de l'IUCS -->
<section class="relative h-screen flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0">
        <img src="<?= BASE_URL ?>/public/images/lcs-campus.jpg" alt="Campus LCS" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-900/80 to-indigo-900/80"></div>
    </div>
    <div class="relative z-10 text-center text-white px-4 max-w-5xl mx-auto" data-aos="fade-up" data-aos-duration="1500">
        <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
            <span class="block">Bienvenue sur</span>
            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-amber-400">e-Mémoire LCS</span>
        </h1>
        <p class="text-xl md:text-2xl mb-8 text-gray-200">La plateforme intelligente de gestion des mémoires de l'Institut Universitaire Les COURS SONOU. Centralisez, suivez et valorisez vos travaux académiques en toute simplicité.</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="<?= BASE_URL ?>/auth/register" class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full overflow-hidden shadow-2xl transform hover:scale-105 transition duration-300">
                <span class="relative flex items-center"><i class="fas fa-rocket mr-2 animate-bounce"></i> Commencer maintenant</span>
            </a>
            <a href="#presentation" class="group relative inline-flex items-center justify-center px-8 py-4 font-bold text-white border-2 border-white rounded-full hover:bg-white hover:text-gray-900 transition duration-300">
                <span class="relative flex items-center"><i class="fas fa-play-circle mr-2"></i> Découvrir</span>
            </a>
        </div>
    </div>
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <a href="#presentation" class="text-white text-3xl"><i class="fas fa-chevron-down"></i></a>
    </div>
</section>

<!-- Section Présentation avec arrière-plan salle de classe LCS -->
<section id="presentation" class="py-24 relative bg-cover bg-center" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-classroom.jpg');">
    <div class="absolute inset-0 bg-white/80 dark:bg-gray-900/80"></div>
    <div class="container mx-auto px-4 relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold text-center mb-16 text-gray-900 dark:text-white" data-aos="fade-up">Pourquoi e-Mémoire LCS ?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6 rounded-2xl bg-white/90 dark:bg-gray-800/90 shadow-xl hover:shadow-2xl transition" data-aos="zoom-in">
                <div class="bg-blue-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-cloud-upload-alt text-4xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2">Dépôt simplifié</h3>
                <p class="text-gray-600 dark:text-gray-300">Soumettez vos sujets et versions en ligne, sans papier. Suivez l'historique de vos fichiers.</p>
            </div>
            <div class="text-center p-6 rounded-2xl bg-white/90 dark:bg-gray-800/90 shadow-xl hover:shadow-2xl transition" data-aos="zoom-in" data-aos-delay="100">
                <div class="bg-green-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-4xl text-green-600"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2">Suivi interactif</h3>
                <p class="text-gray-600 dark:text-gray-300">Encadreurs et étudiants échangent directement sur la plateforme. Recevez des feedbacks en temps réel.</p>
            </div>
            <div class="text-center p-6 rounded-2xl bg-white/90 dark:bg-gray-800/90 shadow-xl hover:shadow-2xl transition" data-aos="zoom-in" data-aos-delay="200">
                <div class="bg-purple-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-check text-4xl text-purple-600"></i>
                </div>
                <h3 class="text-2xl font-bold mb-2">Soutenances planifiées</h3>
                <p class="text-gray-600 dark:text-gray-300">Gérez les jurys et les plannings sans conflit. Évaluez en ligne avec mentions et appréciations.</p>
            </div>
        </div>
    </div>
</section>

<!-- Section Statistiques avec image de la bibliothèque LCS -->
<section class="py-24 relative bg-cover bg-fixed" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-library.jpg');">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-900/90 to-indigo-900/90"></div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white">
            <div data-aos="zoom-in" data-aos-duration="1000">
                <div class="text-6xl font-bold mb-2 counter" data-target="<?= $stats['memoires'] ?>">0</div>
                <div class="text-xl uppercase tracking-wider">Mémoires déposés</div>
            </div>
            <div data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="100">
                <div class="text-6xl font-bold mb-2 counter" data-target="<?= $stats['etudiants'] ?>">0</div>
                <div class="text-xl uppercase tracking-wider">Étudiants</div>
            </div>
            <div data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="200">
                <div class="text-6xl font-bold mb-2 counter" data-target="<?= $stats['encadreurs'] ?>">0</div>
                <div class="text-xl uppercase tracking-wider">Encadreurs</div>
            </div>
            <div data-aos="zoom-in" data-aos-duration="1000" data-aos-delay="300">
                <div class="text-6xl font-bold mb-2 counter" data-target="<?= $stats['soutenances'] ?>">0</div>
                <div class="text-xl uppercase tracking-wider">Soutenances</div>
            </div>
        </div>
    </div>
</section>

<!-- Section Fonctionnement (timeline) avec image d’un amphithéâtre LCS -->
<section class="py-24 relative bg-cover bg-center" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-meeting.jpg');">
    <div class="absolute inset-0 bg-white/90 dark:bg-gray-900/90"></div>
    <div class="container mx-auto px-4 relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold text-center mb-16 text-gray-900 dark:text-white" data-aos="fade-up">Comment ça marche ?</h2>
        <div class="relative">
            <div class="hidden md:block absolute left-1/2 transform -translate-x-1/2 w-1 h-full bg-gradient-to-b from-blue-500 to-indigo-600"></div>
            <div class="space-y-12">
                <div class="flex flex-col md:flex-row items-center" data-aos="fade-right">
                    <div class="md:w-1/2 text-right pr-8">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl">
                            <span class="text-3xl font-bold text-blue-600">01</span>
                            <h3 class="text-2xl font-bold mt-2">Inscription</h3>
                            <p class="text-gray-600 dark:text-gray-300">Créez votre compte en tant qu'étudiant ou encadreur. Renseignez votre filière et vos informations.</p>
                        </div>
                    </div>
                    <div class="md:w-1/2 flex justify-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg z-10">1</div>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row-reverse items-center" data-aos="fade-left">
                    <div class="md:w-1/2 text-left pl-8">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl">
                            <span class="text-3xl font-bold text-green-600">02</span>
                            <h3 class="text-2xl font-bold mt-2">Dépôt et suivi</h3>
                            <p class="text-gray-600 dark:text-gray-300">Soumettez votre mémoire, recevez des feedbacks, déposez de nouvelles versions jusqu'à validation.</p>
                        </div>
                    </div>
                    <div class="md:w-1/2 flex justify-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg z-10">2</div>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row items-center" data-aos="fade-right">
                    <div class="md:w-1/2 text-right pr-8">
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-xl">
                            <span class="text-3xl font-bold text-purple-600">03</span>
                            <h3 class="text-2xl font-bold mt-2">Soutenance</h3>
                            <p class="text-gray-600 dark:text-gray-300">Planifiez votre soutenance, le jury évalue et la mention est attribuée. Accédez à la bibliothèque numérique.</p>
                        </div>
                    </div>
                    <div class="md:w-1/2 flex justify-center">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg z-10">3</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Section Appel à l'action avec image du logo LCS agrandi -->
<section class="py-24 relative bg-cover bg-fixed" style="background-image: url('<?= BASE_URL ?>/public/images/lcs-logo-bg.jpg');">
    <div class="absolute inset-0 bg-black bg-opacity-60"></div>
    <div class="container mx-auto px-4 text-center relative z-10 text-white">
        <h2 class="text-4xl md:text-5xl font-bold mb-6" data-aos="zoom-in">Prêt à rejoindre l'aventure ?</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">Inscrivez-vous dès maintenant et simplifiez votre parcours académique. Rejoignez une communauté d'apprenants et d'enseignants.</p>
        <a href="<?= BASE_URL ?>/auth/register" class="inline-block bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-600 hover:to-yellow-600 text-white font-bold py-4 px-12 rounded-full text-xl transition transform hover:scale-110 hover:shadow-2xl">
            Créer un compte
        </a>
    </div>
</section>

<script>
    // Animation des compteurs
    function animateCounters() {
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const updateCount = () => {
                const count = +counter.innerText;
                const increment = target / 50;
                if (count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 30);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        });
    }
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.disconnect();
            }
        });
    });
    observer.observe(document.querySelector('.counter').closest('section'));

    // Carrousel
    const carousel = document.getElementById('testimonialCarousel');
    const prev = document.querySelector('.carousel-prev');
    const next = document.querySelector('.carousel-next');
    if (prev && next) {
        prev.addEventListener('click', () => carousel.scrollBy({ left: -300, behavior: 'smooth' }));
        next.addEventListener('click', () => carousel.scrollBy({ left: 300, behavior: 'smooth' }));
    }
</script>

<?php include 'views/layouts/footer.php'; ?>