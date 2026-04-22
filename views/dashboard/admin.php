<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-white" style="text-shadow: 2px 2px 4px #000;">Administration</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 text-center" data-aos="zoom-in">
            <p class="text-3xl font-bold text-blue-600"><?= count($users) ?></p>
            <p class="text-gray-600">Utilisateurs</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center" data-aos="zoom-in" data-aos-delay="50">
            <?php
            $pdo = Db::getInstance();
            $stmt = $pdo->query("SELECT COUNT(*) as c FROM memoires");
            $totalMemoires = $stmt->fetch()['c'];
            ?>
            <p class="text-3xl font-bold text-green-600"><?= $totalMemoires ?></p>
            <p class="text-gray-600">Mémoires</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center" data-aos="zoom-in" data-aos-delay="100">
            <?php
            $stmt = $pdo->query("SELECT COUNT(*) as c FROM soutenances WHERE date >= CURDATE()");
            $prochaines = $stmt->fetch()['c'];
            ?>
            <p class="text-3xl font-bold text-purple-600"><?= $prochaines ?></p>
            <p class="text-gray-600">Soutenances à venir</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Derniers utilisateurs inscrits -->
        <div class="bg-white rounded-lg shadow-lg p-6" data-aos="fade-right">
            <h2 class="text-xl font-semibold mb-4">Derniers inscrits</h2>
            <ul class="divide-y">
                <?php foreach ($users as $u): ?>
                <li class="py-2 flex justify-between">
                    <span><?= $u['prenom'] ?> <?= $u['nom'] ?> (<?= $u['role'] ?>)</span>
                    <span class="text-sm text-gray-500"><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Actions d'administration -->
        <div class="bg-white rounded-lg shadow-lg p-6" data-aos="fade-left">
            <h2 class="text-xl font-semibold mb-4">Administration</h2>
            <div class="space-y-2">
                <a href="<?= BASE_URL ?>/admin/utilisateurs" class="block bg-indigo-500 hover:bg-indigo-700 text-white text-center py-2 rounded">Gérer les utilisateurs</a>
                <a href="<?= BASE_URL ?>/admin/parametres" class="block bg-gray-500 hover:bg-gray-700 text-white text-center py-2 rounded">Paramètres</a>
                <a href="<?= BASE_URL ?>/soutenance/planifier" class="block bg-green-500 hover:bg-green-700 text-white text-center py-2 rounded">Planifier une soutenance</a>
            </div>
        </div>
    </div>

    <!-- Sauvegarde -->
    <div class="mt-8 bg-white rounded-lg shadow-lg p-6" data-aos="fade-up">
        <h2 class="text-xl font-semibold mb-4">Sauvegarde de la base</h2>
        <p class="mb-4">Télécharger une sauvegarde manuelle de la base de données.</p>
        <a href="<?= BASE_URL ?>/admin/backup" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Sauvegarder maintenant</a>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>