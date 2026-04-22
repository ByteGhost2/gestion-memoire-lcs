<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Comparaison des versions <?= $v1 ?> et <?= $v2 ?></h1>
    <div class="mb-4 p-4 rounded <?= $similarity > 80 ? 'bg-red-100 border-red-500' : 'bg-green-100 border-green-500' ?> border-l-4">
        <p class="font-semibold">Taux de similarité : <strong><?= $similarity ?>%</strong></p>
        <?php if ($similarity > 80): ?>
            <p class="text-red-700">⚠️ Attention : Les deux versions sont très similaires. Vérifiez que l'étudiant n'a pas simplement réuploadé le même fichier.</p>
        <?php endif; ?>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div class="border p-4 rounded">
            <h2 class="font-bold mb-2">Version <?= $v1 ?></h2>
            <pre class="whitespace-pre-wrap text-sm"><?= htmlspecialchars($diff['old']) ?></pre>
        </div>
        <div class="border p-4 rounded">
            <h2 class="font-bold mb-2">Version <?= $v2 ?></h2>
            <pre class="whitespace-pre-wrap text-sm"><?= htmlspecialchars($diff['new']) ?></pre>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>