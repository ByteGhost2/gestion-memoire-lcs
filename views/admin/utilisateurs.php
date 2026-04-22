<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-white" style="text-shadow: 2px 2px 4px #000;">Gestion des utilisateurs</h1>
        <a href="<?= BASE_URL ?>/admin/ajouterUtilisateur" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">+ Ajouter</a>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Utilisateur ajouté avec succès.</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Utilisateur mis à jour.</div>
    <?php endif; ?>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">Utilisateur supprimé.</div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filière</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscription</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><?= $u['id'] ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
                    <td class="px-6 py-4"><?= htmlspecialchars($u['email']) ?></td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            <?= $u['role'] == 'admin' ? 'bg-red-100 text-red-800' : ($u['role'] == 'responsable' ? 'bg-purple-100 text-purple-800' : ($u['role'] == 'encadreur' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) ?>">
                            <?= $u['role'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4"><?= $u['filiere'] ?? '-' ?></td>
                    <td class="px-6 py-4"><?= date('d/m/Y', strtotime($u['date_inscription'])) ?></td>
                    <td class="px-6 py-4 space-x-2">
                        <a href="<?= BASE_URL ?>/admin/modifierUtilisateur/<?= $u['id'] ?>" class="text-blue-600 hover:text-blue-900">Modifier</a>
                        <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                            <a href="<?= BASE_URL ?>/admin/supprimerUtilisateur/<?= $u['id'] ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>