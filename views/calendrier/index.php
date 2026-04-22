<?php include 'views/layouts/header.php'; ?>
<div class="container mx-auto" data-aos="fade-up">
    <h1 class="text-3xl font-bold mb-6 text-white">Calendrier des soutenances</h1>
    <div class="bg-white rounded-lg shadow-xl p-4">
        <div id="calendar"></div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '<?= BASE_URL ?>/calendrier/events',
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            window.location.href = info.event.url;
        }
    });
    calendar.render();
});
</script>
<?php include 'views/layouts/footer.php'; ?>