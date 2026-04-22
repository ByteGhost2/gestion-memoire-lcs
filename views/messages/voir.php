<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto max-w-4xl" data-aos="fade-up">
    <div class="bg-white rounded-lg shadow-xl overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
            <h2 class="text-xl font-semibold"><?= htmlspecialchars($conv['titre']) ?></h2>
        </div>
        <div class="p-4 h-96 overflow-y-auto" id="message-list">
            <?php foreach ($messages as $msg): ?>
                <div class="mb-4 <?= $msg['expediteur_id'] == $_SESSION['user']['id'] ? 'text-right' : 'text-left' ?>">
                    <div class="inline-block max-w-xs p-3 rounded-lg <?= $msg['expediteur_id'] == $_SESSION['user']['id'] ? 'bg-blue-500 text-white' : 'bg-gray-200' ?>">
                        <p class="text-sm"><?= htmlspecialchars($msg['message']) ?></p>
                        <p class="text-xs mt-1 opacity-75"><?= $msg['prenom'] ?> <?= $msg['nom'] ?> - <?= date('H:i', strtotime($msg['created_at'])) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="p-4 border-t">
            <form method="POST" action="<?= BASE_URL ?>/messages/envoyer">
                <input type="hidden" name="conversation_id" value="<?= $conv['id'] ?>">
                <div class="flex space-x-2">
                    <input type="text" name="message" placeholder="Écrivez votre message..." required class="flex-1 border rounded p-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Scroll en bas au chargement
    var list = document.getElementById('message-list');
    list.scrollTop = list.scrollHeight;
</script>
<?php include 'views/layouts/footer.php'; ?>