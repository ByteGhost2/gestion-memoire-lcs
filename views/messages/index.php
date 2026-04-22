<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-white">Messagerie interne</h1>

    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <ul class="divide-y divide-gray-200">
            <?php foreach ($conversations as $c): ?>
                <li class="hover:bg-gray-50 transition">
                    <a href="<?= BASE_URL ?>/messages/voir/<?= $c['id'] ?>" class="block p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold"><?= htmlspecialchars($c['titre']) ?></h3>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($c['dernier_message'] ?? 'Aucun message') ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <?php if ($c['non_lus'] > 0): ?>
                                    <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs"><?= $c['non_lus'] ?></span>
                                <?php endif; ?>
                                <span class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($c['derniere_activite'])) ?></span>
                            </div>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
            <?php if (empty($conversations)): ?>
                <li class="p-4 text-center text-gray-500">Aucune conversation.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>