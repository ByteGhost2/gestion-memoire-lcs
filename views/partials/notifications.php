<?php
// Récupérer les notifications non lues
$pdo = Db::getInstance();
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE id_utilisateur = ? AND lu = FALSE ORDER BY date_creation DESC LIMIT 5");
$stmt->execute([$_SESSION['user']['id']]);
$notifs = $stmt->fetchAll();
?>
<div class="relative">
    <button id="notifButton" class="relative p-2 text-white hover:bg-gray-700 rounded">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <?php if (count($notifs) > 0): ?>
            <span class="absolute top-0 right-0 inline-block w-3 h-3 bg-red-600 rounded-full"></span>
        <?php endif; ?>
    </button>
    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl z-10">
        <div class="p-4 border-b">
            <h3 class="font-semibold">Notifications</h3>
        </div>
        <div class="max-h-64 overflow-y-auto">
            <?php if (empty($notifs)): ?>
                <p class="p-4 text-gray-500">Aucune nouvelle notification</p>
            <?php else: ?>
                <?php foreach ($notifs as $n): ?>
                    <a href="<?= BASE_URL ?>/<?= $n['lien'] ?>" class="block p-4 border-b hover:bg-gray-50">
                        <p class="text-sm"><?= htmlspecialchars($n['message']) ?></p>
                        <span class="text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($n['date_creation'])) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="p-2 border-t text-center">
            <a href="<?= BASE_URL ?>/notifications" class="text-sm text-blue-600 hover:underline">Voir tout</a>
        </div>
    </div>
</div>
<script>
    document.getElementById('notifButton').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('notifDropdown').classList.toggle('hidden');
    });
    document.addEventListener('click', function() {
        document.getElementById('notifDropdown').classList.add('hidden');
    });
</script>