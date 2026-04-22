<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-white">Journal des actions</h1>
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <table class="min-w-full">
            <thead>
                <tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Détails</th><th>IP</th></tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $l): ?>
                <tr>
                    <td><?= date('d/m/Y H:i', strtotime($l['date'])) ?></td>
                    <td><?= htmlspecialchars($l['prenom'] . ' ' . $l['nom']) ?></td>
                    <td><?= htmlspecialchars($l['action']) ?></td>
                    <td><?= htmlspecialchars($l['details']) ?></td>
                    <td><?= $l['ip'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>