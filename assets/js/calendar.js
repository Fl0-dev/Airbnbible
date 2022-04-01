document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    const idHousing = calendarEl.dataset.id
    const dates= [];

    fetch(`http://127.0.0.1:8000/getbookings/${idHousing}`)
            .then((res) => res.json())
            .then((res) => {
                console.log(res)
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    headerToolbar: {
                        left: 'prevYear,prev,next,nextYear today',
                        center: 'title',
                        right: 'dayGridMonth'
                    },
                    validRange: { start: new Date },
                    initialDate: new Date,
                    navLinks: true, // can click day/week names to navigate views
                    editable: true,
                    dayMaxEvents: true, // allow "more" link when too many events
                    events: res
                });

                calendar.render();
            })






});