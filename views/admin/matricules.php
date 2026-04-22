<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">Gestion des matricules</h1>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="POST" class="mb-6">
            <div class="flex gap-2">
                <input type="text" name="matricule" placeholder="Nouveau matricule" required class="flex-1 px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded">Ajouter</button>
            </div>
        </form>
        <table class="min-w-full">
            <thead>
                
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Matricule</th>
                    <th class="px-4 py-2">Attribué à</th>
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">Actions</th>
                </thead>
            <tbody>
                <?php
                // Récupérer les données via PDO (séparé)
                $pdo = Db::getInstance();
                $stmt = $pdo->query("SELECT * FROM matricules ORDER BY created_at DESC");
                $matricules = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php foreach ($matricules as $m): ?>
                
                    <td class="px-4 py-2"><?= htmlspecialchars($m['id']) ?> </td>
                    <td class="px-4 py-2"><?= htmlspecialchars($m['matricule']) ?> </td>
                    <td class="px-4 py-2">
                        <?php if ($m['etudiant_id']): 
                            $stmtEt = $pdo->prepare("SELECT nom, prenom FROM utilisateurs WHERE id = ?");
                            $stmtEt->execute([$m['etudiant_id']]);
                            $et = $stmtEt->fetch(PDO::FETCH_ASSOC);
                            if ($et):
                                echo htmlspecialchars($et['prenom'] . ' ' . $et['nom']);
                            else:
                                echo '<span class="text-red-500">Inconnu</span>';
                            endif;
                        else:
                            echo '<span class="text-green-600">Libre</span>';
                        endif; ?>
                    </td>
                    <td class="px-4 py-2"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?> </td>
                    <td class="px-4 py-2">
                        <a href="<?= BASE_URL ?>/admin/supprimerMatricule/<?= $m['id'] ?>" class="text-red-600" onclick="return confirm('Supprimer ce matricule ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($matricules)): ?>
                 <tr>
                    <td colspan="5" class="px-4 py-2 text-center text-gray-500">Aucun matricule enregistré.</td>
                 </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>