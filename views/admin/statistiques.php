<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-white">Statistiques avancées</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-xl p-6">
            <h2 class="text-xl font-bold mb-4">Répartition des mentions</h2>
            <canvas id="chartMentions"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow-xl p-6">
            <h2 class="text-xl font-bold mb-4">Mémoires par statut</h2>
            <canvas id="chartStatuts"></canvas>
        </div>
    
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxMentions = document.getElementById('chartMentions').getContext('2d');
    new Chart(ctxMentions, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($mentions, 'mention')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($mentions, 'total')) ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        }
    });
    const ctxStatuts = document.getElementById('chartStatuts').getContext('2d');
    new Chart(ctxStatuts, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($statuts, 'statut')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($statuts, 'total')) ?>,
                backgroundColor: '#36A2EB'
            }]
        }
    });
    const ctxMois = document.getElementById('chartMois').getContext('2d');
    new Chart(ctxMois, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($mois, 'mois')) ?>,
            datasets: [{
                label: 'Dépôts',
                data: <?= json_encode(array_column($mois, 'total')) ?>,
                borderColor: '#FF6384'
            }]
        }
    });
</script>
<?php include 'views/layouts/footer.php'; ?>