<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8" data-aos="fade-up">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-white">Calendrier des soutenances</h1>
        <a href="<?= BASE_URL ?>/enseignant/dashboard" class="mt-4 md:mt-0 glass-dark text-white px-6 py-3 rounded-full hover:bg-white/20 transition flex items-center space-x-2">
            <i class="fas fa-list"></i>
            <span>Retour à la liste</span>
        </a>
    </div>

    <div class="glass rounded-2xl p-6">
        <div id="calendar"></div>
    </div>
</div>

<!-- FullCalendar CSS et JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'fr',
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        allDaySlot: false,
        events: <?= json_encode($events) ?>,
        eventClick: function(info) {
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        eventDidMount: function(info) {
            info.el.title = info.event.title;
        }
    });
    calendar.render();
});
</script>

<?php include 'views/layouts/footer.php'; ?>