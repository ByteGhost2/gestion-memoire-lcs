<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
        <!-- En-tête -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($memoire['titre']) ?></h1>
            <span class="mt-2 md:mt-0 px-3 py-1 rounded-full text-sm font-semibold 
                <?= $memoire['statut'] == 'valide' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                    ($memoire['statut'] == 'en_cours' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                    ($memoire['statut'] == 'soumis' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 
                    ($memoire['statut'] == 'rejete' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'))) ?>">
                <?= $memoire['statut'] ?>
            </span>
        </div>

        <div class="flex items-center text-gray-600 dark:text-gray-400 mb-4">
            <i class="fas fa-user-graduate mr-2"></i>
            <span>par <strong><?= htmlspecialchars($memoire['prenom'] . ' ' . $memoire['nom']) ?></strong> (<?= htmlspecialchars($memoire['email']) ?>)</span>
        </div>

        <?php if (!empty($verrou) && !$is_admin_or_responsable): ?>
            <div class="mb-4 p-3 bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 rounded-lg flex items-center">
                <i class="fas fa-lock mr-2"></i>Ce mémoire est verrouillé par <?= htmlspecialchars($verrou['prenom'] . ' ' . $verrou['nom']) ?>.
            </div>
        <?php endif; ?>

        <!-- Description du thème - mise en avant pour l'encadreur -->
<?php if (!empty($memoire['theme_description'])): ?>
    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-r-lg">
        <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">📝 Description du thème</h2>
        <p class="text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($memoire['theme_description'])) ?></p>
    </div>
<?php else: ?>
    <?php if ($_SESSION['user']['role'] == 'encadreur' && $_SESSION['user']['id'] == $memoire['id_encadreur'] && $memoire['statut'] == 'soumis'): ?>
        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded-r-lg">
            <p class="text-yellow-700 dark:text-yellow-300">⚠️ Aucune description du thème n'a été fournie par l'étudiant.</p>
        </div>
    <?php endif; ?>
<?php endif; ?>

        <!-- Résumé du mémoire (si déjà complété) -->
        <?php if (!empty($memoire['resume'])): ?>
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Résumé du mémoire</h2>
                <p class="text-gray-700 dark:text-gray-300"><?= nl2br(htmlspecialchars($memoire['resume'])) ?></p>
            </div>
        <?php endif; ?>

        <!-- Mots-clés (si déjà complété) -->
        <?php if (!empty($memoire['mots_cles'])): ?>
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Mots-clés</h2>
                <div class="flex flex-wrap gap-2">
                    <?php foreach (explode(',', $memoire['mots_cles']) as $mot): ?>
                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm"><?= trim(htmlspecialchars($mot)) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Fichier actuel (si complété) -->
        <?php if (!empty($memoire['fichier'])): ?>
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Document</h2>
                <a href="<?= BASE_URL ?>/<?= $memoire['fichier'] ?>" target="_blank" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-lg transition transform hover:scale-105">
                    <i class="fas fa-file-pdf mr-2"></i> Télécharger la dernière version (v<?= $memoire['version_actuelle'] ?>)
                </a>
            </div>
        <?php endif; ?>

        <!-- Historique des versions (uniquement pour encadreur et étudiant) -->
        <?php if (!$is_admin_or_responsable && !empty($versions)): ?>
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">Historique des versions</h2>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left">Version</th>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </thead>
                    <tbody>
                        <?php foreach ($versions as $v): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">v<?= $v['numero'] ?> </td>
                                <td class="px-4 py-2"><?= date('d/m/Y H:i', strtotime($v['date_upload'])) ?> </td>
                                <td class="px-4 py-2">
                                    <a href="<?= BASE_URL ?>/<?= $v['fichier'] ?>" target="_blank" class="text-blue-600">Télécharger</a>
                                    <?php if ($_SESSION['user']['role'] != 'etudiant' && count($versions) > 1 && $v['numero'] != $versions[0]['numero']): ?>
                                        <a href="<?= BASE_URL ?>/memoire/comparerVersions/<?= $memoire['id'] ?>/<?= $versions[0]['numero'] ?>/<?= $v['numero'] ?>" class="text-green-600 ml-2">Comparer avec la dernière version</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Dépôt de la première version (étudiant, après validation du thème, aucune version encore) -->
        <?php
        $hasVersion = !empty($versions);
        if ($_SESSION['user']['role'] == 'etudiant' && $memoire['statut'] == 'en_cours' && !$hasVersion && !$is_admin_or_responsable):
        ?>
            <div class="mt-6">
                <a href="<?= BASE_URL ?>/memoire/completer/<?= $memoire['id'] ?>" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-file-upload mr-2"></i>Soumettre le mémoire complet
                </a>
                <p class="text-sm text-gray-500 mt-2">Déposez la version finale avec résumé, mots-clés et fichier PDF.</p>
            </div>
        <?php endif; ?>

        <!-- Dépôt de versions ultérieures (étudiant, si une version existe déjà) -->
        <?php if ($_SESSION['user']['role'] == 'etudiant' && $memoire['statut'] == 'en_cours' && $hasVersion && !$is_admin_or_responsable): ?>
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h2 class="text-xl font-semibold mb-2">Déposer une nouvelle version du mémoire</h2>
                <?php if (!empty($verrou) && $verrou['id'] != $_SESSION['user']['id']): ?>
                    <p class="text-red-600">Le mémoire est verrouillé par un autre utilisateur.</p>
                <?php else: ?>
                    <form method="POST" action="<?= BASE_URL ?>/memoire/ajouterVersion/<?= $memoire['id'] ?>" enctype="multipart/form-data">
                        <input type="file" name="fichier" accept=".pdf" required class="mb-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Uploader le mémoire</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Gestion du groupe (pour le chef de groupe, uniquement étudiant) -->
        <?php if ($_SESSION['user']['role'] == 'etudiant' && $memoire['id_etudiant'] == $_SESSION['user']['id'] && !$is_admin_or_responsable): ?>
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h2 class="text-xl font-semibold mb-2">Gestion du groupe</h2>
                <form method="POST" action="<?= BASE_URL ?>/memoire/ajouterEtudiant/<?= $memoire['id'] ?>" class="flex flex-col sm:flex-row gap-2 mb-4">
                    <select name="id_etudiant" class="border rounded p-2">
                        <option value="">Ajouter un étudiant</option>
                        <?php foreach ($etudiantsDisponibles as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['prenom'] . ' ' . $e['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="role" class="border rounded p-2">
                        <option value="membre">Membre</option>
                        <option value="co-auteur">Co-auteur</option>
                        <option value="binome">Binôme</option>
                    </select>
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white py-2 px-4 rounded">Ajouter</button>
                </form>
                <ul>
                    <?php foreach ($groupe as $m): ?>
                        <li class="flex justify-between items-center border-b py-2">
                            <span><?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?> (<?= $m['role'] ?>)</span>
                            <?php if ($m['id'] != $_SESSION['user']['id']): ?>
                                <a href="<?= BASE_URL ?>/memoire/retirerEtudiant/<?= $memoire['id'] ?>/<?= $m['id'] ?>" class="text-red-600" onclick="return confirm('Retirer cet étudiant ?')">Retirer</a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Plagiat (pour encadreur seulement) -->
        <?php if ($_SESSION['user']['role'] == 'encadreur' && $_SESSION['user']['id'] == $memoire['id_encadreur'] && !$is_admin_or_responsable): ?>
            <div class="mb-6">
                <a href="<?= BASE_URL ?>/memoire/checkPlagiat/<?= $memoire['id'] ?>" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">🔍 Vérifier plagiat</a>
            </div>
        <?php endif; ?>
        <?php if (!empty($plagiat) && $_SESSION['user']['role'] == 'encadreur' && $_SESSION['user']['id'] == $memoire['id_encadreur'] && !$is_admin_or_responsable): ?>
            <div class="mb-4 p-3 <?= $plagiat['score'] > 30 ? 'bg-red-100' : 'bg-green-100' ?> rounded">
                <p><strong>Dernière vérification :</strong> <?= date('d/m/Y H:i', strtotime($plagiat['date_check'])) ?></p>
                <p>Score : <?= $plagiat['score'] ?>% (<?= $plagiat['score'] > 30 ? '⚠️ Attention' : '✅ Acceptable' ?>)</p>
            </div>
        <?php endif; ?>

        <!-- Section Commentaires (style chat) - pour encadreur et étudiant -->
        <?php if (($_SESSION['user']['role'] == 'encadreur' && $_SESSION['user']['id'] == $memoire['id_encadreur']) || 
                  ($_SESSION['user']['role'] == 'etudiant' && (in_array($_SESSION['user']['id'], array_column($groupe, 'id')) || $_SESSION['user']['id'] == $memoire['id_etudiant']))): ?>
            <div class="mt-8">
                <h2 class="text-2xl font-bold mb-4">Discussion</h2>
                <div class="space-y-4 max-h-96 overflow-y-auto p-2" id="chatContainer">
                    <?php if (empty($feedbacks)): ?>
                        <p class="text-gray-500 text-center">Aucun message pour l'instant. Soyez le premier à commenter !</p>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $f): 
                            $isEncadreur = ($f['role'] == 'encadreur');
                            $isEtudiant = ($f['role'] == 'etudiant');
                            $bubbleClass = $isEncadreur ? 'bg-blue-100 dark:bg-blue-800 text-gray-800 dark:text-gray-100' : 'bg-green-100 dark:bg-green-800 text-gray-800 dark:text-gray-100';
                            $alignClass = $isEncadreur ? 'justify-start' : 'justify-end';
                        ?>
                            <div class="flex <?= $alignClass ?>">
                                <div class="max-w-[70%] <?= $bubbleClass ?> rounded-2xl p-3 shadow-sm">
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="font-semibold text-sm"><?= htmlspecialchars($f['prenom'] . ' ' . $f['nom']) ?> (<?= $f['role'] ?>)</span>
                                        <span class="text-xs text-gray-500 ml-2"><?= date('d/m/Y H:i', strtotime($f['date'])) ?></span>
                                    </div>
                                    <p class="whitespace-pre-wrap"><?= nl2br(htmlspecialchars($f['message'])) ?></p>
                                    <?php if ($f['piece_jointe']): ?>
                                        <div class="mt-2">
                                            <a href="<?= BASE_URL ?>/<?= $f['piece_jointe'] ?>" target="_blank" class="text-blue-600 hover:underline text-sm">
                                                <i class="fas fa-paperclip mr-1"></i>Pièce jointe
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Formulaire d'ajout de commentaire (simplifié) -->
                <?php 
                $peutCommenter = false;
                if ($_SESSION['user']['role'] == 'encadreur' && $_SESSION['user']['id'] == $memoire['id_encadreur']) $peutCommenter = true;
                elseif ($_SESSION['user']['role'] == 'etudiant') {
                    foreach ($groupe as $m) if ($m['id'] == $_SESSION['user']['id']) $peutCommenter = true;
                }
                if ($peutCommenter && !empty($versions)): ?>
                    <div class="mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <form method="POST" action="<?= BASE_URL ?>/memoire/commenter/<?= $versions[0]['id'] ?>" enctype="multipart/form-data" class="space-y-3">
                            <textarea name="message" rows="3" placeholder="Écrivez votre message..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-600 dark:text-white" required></textarea>
                            <div class="flex justify-between items-center">
                                <label class="cursor-pointer bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg transition">
                                    <i class="fas fa-paperclip mr-1"></i> Joindre un fichier
                                    <input type="file" name="piece_jointe" accept=".pdf,.doc,.docx,.jpg,.png" class="hidden">
                                </label>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition transform hover:scale-105">
                                    <i class="fas fa-paper-plane mr-2"></i>Envoyer
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Validation du sujet par l'encadreur (si statut 'soumis') -->
        <?php if ($memoire['statut'] == 'soumis' && $_SESSION['user']['role'] == 'encadreur' && $_SESSION['user']['id'] == $memoire['id_encadreur'] && !$is_admin_or_responsable): ?>
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <h3 class="text-lg font-semibold mb-2">Valider ou rejeter le sujet</h3>
                <div class="flex flex-col md:flex-row gap-4">
                    <a href="<?= BASE_URL ?>/memoire/validerSujet/<?= $memoire['id'] ?>" class="bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded text-center" onclick="return confirm('Valider ce sujet ?')">
                        <i class="fas fa-check-circle mr-2"></i>Valider le sujet
                    </a>
                    <form method="POST" action="<?= BASE_URL ?>/memoire/rejeterSujet/<?= $memoire['id'] ?>" class="flex-1">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <textarea name="feedback" rows="2" class="border rounded p-2 flex-1" placeholder="Motif du rejet (obligatoire)"></textarea>
                            <button type="submit" class="bg-red-600 hover:bg-red-800 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Rejeter ce sujet ?')">
                                <i class="fas fa-times-circle mr-2"></i>Rejeter avec commentaire
                            </button>
                        </div>
                    </form>
                </div>
                <p class="text-sm text-gray-500 mt-2">Après validation, l'étudiant pourra déposer les versions du mémoire.</p>
            </div>
        <?php endif; ?>

        <!-- Affichage du commentaire de rejet -->
        <?php if ($memoire['statut'] == 'rejete' && !empty($memoire['theme_feedback']) && !$is_admin_or_responsable): ?>
            <div class="mt-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-300 rounded-lg">
                <strong>Motif du rejet :</strong><br>
                <?= nl2br(htmlspecialchars($memoire['theme_feedback'])) ?>
                <?php if ($_SESSION['user']['role'] == 'etudiant'): ?>
                    <p class="mt-2 text-sm">Vous pouvez modifier votre thème et le soumettre à nouveau.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Bouton pour modifier le thème (étudiant, si rejeté) -->
        <?php if ($memoire['statut'] == 'rejete' && $_SESSION['user']['role'] == 'etudiant' && !$is_admin_or_responsable): ?>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/memoire/modifierTheme/<?= $memoire['id'] ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Modifier le thème
                </a>
            </div>
        <?php endif; ?>

        <!-- Validation finale du mémoire (encadreur) -->
        <?php if ($_SESSION['user']['role'] == 'encadreur' && $_SESSION['user']['id'] == $memoire['id_encadreur'] && $memoire['statut'] == 'en_cours' && !$is_admin_or_responsable): ?>
            <div class="mt-6">
                <a href="<?= BASE_URL ?>/memoire/valider/<?= $memoire['id'] ?>" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Valider ce mémoire pour soutenance ?')">
                    <i class="fas fa-check-double mr-2"></i>Valider le mémoire
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>