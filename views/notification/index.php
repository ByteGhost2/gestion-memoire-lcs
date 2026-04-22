<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mes notifications</h1>
        <?php if (!empty($notifications)): ?>
            <a href="<?= BASE_URL ?>/notification/markAllRead" class="text-sm bg-gray-200 dark:bg-gray-700 px-3 py-1 rounded hover:bg-gray-300 transition">Tout marquer comme lu</a>
        <?php endif; ?>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <?php if (empty($notifications)): ?>
            <p class="p-6 text-center text-gray-500 dark:text-gray-400">Aucune notification.</p>
        <?php else: ?>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                <?php foreach ($notifications as $notif): ?>
                <li class="p-4 <?= $notif['lu'] ? 'bg-white dark:bg-gray-800' : 'bg-blue-50 dark:bg-blue-900/30' ?>">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <p class="text-gray-800 dark:text-gray-200"><?= htmlspecialchars($notif['message']) ?></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1"><?= date('d/m/Y H:i', strtotime($notif['date_creation'])) ?></p>
                        </div>
                        <?php if (!$notif['lu']): ?>
                            <a href="<?= BASE_URL ?>/notification/markAsReadGet/<?= $notif['id'] ?>" class="text-blue-600 dark:text-blue-400 text-sm hover:underline">Marquer comme lue</a>
                        <?php endif; ?>
                    </div>
                    <?php if ($notif['lien']): ?>
                        <div class="mt-2">
                            <a href="<?= BASE_URL . '/' . ltrim($notif['lien'], '/') ?>" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">Voir le détail</a>
                        </div>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>