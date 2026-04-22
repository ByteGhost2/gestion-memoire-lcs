<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold mb-8 text-gray-900 dark:text-white" data-aos="fade-up">
        <i class="fas fa-user-graduate mr-3 text-blue-500"></i>Tableau de bord étudiant
    </h1>

    <!-- Cartes statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300" data-aos="zoom-in">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg opacity-90">Total mémoires</p>
                    <p class="text-4xl font-bold"><?= $totalMemos ?></p>
                </div>
                <i class="fas fa-file-alt text-5xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300" data-aos="zoom-in" data-aos-delay="100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg opacity-90">Soutenus</p>
                    <p class="text-4xl font-bold"><?= $termines ?></p>
                </div>
                <i class="fas fa-graduation-cap text-5xl opacity-50"></i>
            </div>
        </div>
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition duration-300" data-aos="zoom-in" data-aos-delay="200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-lg opacity-90">En cours</p>
                    <p class="text-4xl font-bold"><?= $enCours ?></p>
                </div>
                <i class="fas fa-spinner text-5xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Liste des mémoires -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6" data-aos="fade-up">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Mes mémoires</h2>
        <?php if (empty($memoires)): ?>
            <div class="text-center py-12">
                <i class="fas fa-folder-open text-6xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-4">Aucun mémoire soumis pour l'instant.</p>
                <a href="<?= BASE_URL ?>/memoire/soumettre" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600">
                    <i class="fas fa-upload mr-2"></i>Soumettre un sujet
                </a>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($memoires as $index => $m): ?>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-xl transition bg-gray-50 dark:bg-gray-700/50" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white"><?= htmlspecialchars($m['titre']) ?></h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Déposé le <?= date('d/m/Y', strtotime($m['created_at'])) ?></p>
                            </div>
                            <div class="mt-2 md:mt-0 flex items-center space-x-2">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    <?= $m['statut'] == 'soutenu' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                        ($m['statut'] == 'valide' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                                        ($m['statut'] == 'en_cours' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                        'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300')) ?>">
                                    <?= $m['statut'] ?>
                                </span>
                                <a href="<?= BASE_URL ?>/memoire/voir/<?= $m['id'] ?>" class="text-blue-600 dark:text-blue-400 hover:underline"><i class="fas fa-eye"></i></a>
                            </div>
                        </div>

                        <!-- Barre de progression -->
                        <?php
                        $progression = 0;
                        if ($m['statut'] == 'soumis') $progression = 20;
                        elseif ($m['statut'] == 'en_cours') $progression = 50;
                        elseif ($m['statut'] == 'valide') $progression = 80;
                        elseif ($m['statut'] == 'soutenu') $progression = 100;
                        ?>
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5 mb-4">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2.5 rounded-full transition-all duration-500" style="width: <?= $progression ?>%"></div>
                        </div>

                        <!-- Notes obtenues -->
                        <?php if ($m['statut'] == 'soutenu' && isset($notes[$m['id']])): ?>
                            <div class="mt-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="font-semibold mb-3 text-gray-900 dark:text-white">📊 Notes obtenues :</p>
                                <div class="space-y-2">
                                    <?php foreach ($notes[$m['id']] as $eval): ?>
                                        <div class="flex justify-between items-center py-1 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-gray-700 dark:text-gray-300"><?= htmlspecialchars($eval['prenom'] . ' ' . $eval['nom']) ?> (<?= $eval['role'] ?>)</span>
                                            <span class="font-bold <?= $eval['note'] >= 10 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' ?>"><?= $eval['note'] ?>/20</span>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php
                                    $moyenne = array_sum(array_column($notes[$m['id']], 'note')) / count($notes[$m['id']]);
                                    ?>
                                    <div class="flex justify-between items-center pt-2 font-bold text-lg">
                                        <span>Moyenne</span>
                                        <span class="<?= $moyenne >= 10 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' ?>"><?= number_format($moyenne, 2) ?>/20</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Lien attestation si disponible pour cet étudiant -->
                        <?php if (isset($attestations[$m['id']])): ?>
                            <div class="mt-4">
                                <a href="<?= BASE_URL ?>/attestation/telecharger/<?= $m['id'] ?>?etudiant=<?= $_SESSION['user']['id'] ?>" class="inline-flex items-center px-4 py-2 bg-purple-500 hover:bg-purple-700 text-white rounded-lg transition">
                                    <i class="fas fa-download mr-2"></i>Télécharger attestation
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>