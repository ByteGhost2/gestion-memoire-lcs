<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-8 text-gray-900 dark:text-white" data-aos="fade-up">
        <i class="fas fa-book-open mr-3 text-yellow-500"></i>Bibliothèque numérique
    </h1>

    <!-- Filtres avancés -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-8" data-aos="fade-down">
        <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recherche</label>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Titre, auteur, mots-clés..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filière</label>
                <select name="filiere" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Toutes</option>
                    <?php foreach ($filieres as $f): ?>
                        <option value="<?= $f ?>" <?= ($_GET['filiere'] ?? '') == $f ? 'selected' : '' ?>><?= $f ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Année de soutenance</label>
                <select name="annee" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Toutes</option>
                    <?php foreach ($annees as $a): ?>
                        <option value="<?= $a ?>" <?= ($_GET['annee'] ?? '') == $a ? 'selected' : '' ?>><?= $a ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Encadreur</label>
                <select name="encadreur" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Tous</option>
                    <?php foreach ($encadreurs as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= ($_GET['encadreur'] ?? '') == $e['id'] ? 'selected' : '' ?>><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mention</label>
                <select name="mention" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Toutes</option>
                    <?php foreach ($mentions as $m): ?>
                        <option value="<?= $m ?>" <?= ($_GET['mention'] ?? '') == $m ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Année universitaire</label>
                <select name="annee_univ" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Toutes</option>
                    <?php foreach ($anneesUniv as $au): ?>
                        <option value="<?= $au['id'] ?>" <?= ($_GET['annee_univ'] ?? '') == $au['id'] ? 'selected' : '' ?>><?= $au['libelle'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold py-2 px-4 rounded-lg transition transform hover:scale-105">Filtrer</button>
            </div>
        </form>
    </div>

    <!-- Résultats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($memoires as $index => $m): ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden transform hover:scale-105 transition duration-300" data-aos="fade-up" data-aos-delay="<?= $index * 50 ?>">
            <?php if (!empty($m['couverture'])): ?>
                <img src="<?= BASE_URL ?>/<?= $m['couverture'] ?>" alt="Couverture" class="w-full h-48 object-cover">
            <?php else: ?>
                <div class="h-48 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80');"></div>
            <?php endif; ?>
            <div class="p-6">
                <h2 class="text-xl font-bold mb-2 text-gray-900 dark:text-white"><?= htmlspecialchars($m['titre']) ?></h2>
                <p class="text-gray-600 dark:text-gray-400 mb-2">par <?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></p>
                <div class="space-y-1 text-sm text-gray-500 dark:text-gray-500 mb-4">
                    <p><i class="fas fa-tag mr-1"></i> Filière: <?= $m['filiere'] ?></p>
                    <p><i class="fas fa-calendar mr-1"></i> Année: <?= date('Y', strtotime($m['date_soumission'])) ?></p>
                    <p><i class="fas fa-star mr-1"></i> Mots-clés: <?= htmlspecialchars($m['mots_cles'] ?? '-') ?></p>
                    <p><i class="fas fa-chart-line mr-1"></i> Téléchargements: <?= $m['nb_telechargements'] ?? 0 ?> | Vues: <?= $m['nb_vues'] ?? 0 ?></p>
                    <?php if ($m['nb_notes'] > 0): ?>
                        <p><i class="fas fa-graduation-cap mr-1"></i> Évaluations: <?= $m['nb_notes'] ?></p>
                    <?php endif; ?>
                </div>
                <p class="text-gray-700 dark:text-gray-300 mb-4 line-clamp-3"><?= htmlspecialchars(substr($m['resume'], 0, 150)) ?>...</p>
                <?php if ($m['fichier']): ?>
                    <div class="flex space-x-2">
                        <a href="<?= BASE_URL ?>/bibliotheque/vue/<?= $m['id'] ?>" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white rounded-lg transition">
                            <i class="fas fa-eye mr-2"></i>Consulter
                        </a>
                        <a href="<?= BASE_URL ?>/bibliotheque/telecharger/<?= $m['id'] ?>" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded-lg transition">
                            <i class="fas fa-download mr-2"></i>Télécharger
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>