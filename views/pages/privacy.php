<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="glass-card p-8" data-aos="fade-up">
            <h1 class="text-3xl md:text-4xl font-bold mb-6">Politique de confidentialité</h1>
            <div class="prose max-w-none text-gray-700">
                <h2 class="text-xl font-semibold mt-4">Collecte des données</h2>
                <p>Nous collectons les informations que vous fournissez (nom, email, etc.) pour la gestion de votre compte.</p>
                <h2 class="text-xl font-semibold mt-4">Sécurité</h2>
                <p>Nous mettons en œuvre des mesures de sécurité pour protéger vos données.</p>
                <h2 class="text-xl font-semibold mt-4">Cookies</h2>
                <p>La plateforme utilise des cookies de session.</p>
                <p class="mt-6 text-sm text-gray-500">Dernière mise à jour : <?= date('d/m/Y') ?></p>
            </div>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>