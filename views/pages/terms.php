<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="glass-card p-8" data-aos="fade-up">
            <h1 class="text-3xl md:text-4xl font-bold mb-6">Termes et conditions</h1>
            <div class="prose max-w-none text-gray-700">
                <h2 class="text-xl font-semibold mt-4">1. Acceptation</h2>
                <p>En accédant à la plateforme, vous acceptez ces conditions.</p>
                <h2 class="text-xl font-semibold mt-4">2. Utilisation</h2>
                <p>Le service est réservé à la communauté de l'IUCS.</p>
                <h2 class="text-xl font-semibold mt-4">3. Propriété intellectuelle</h2>
                <p>Les mémoires restent la propriété de leurs auteurs.</p>
                <p class="mt-6 text-sm text-gray-500">Dernière mise à jour : <?= date('d/m/Y') ?></p>
            </div>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>