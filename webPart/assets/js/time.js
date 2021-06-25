$(function() {
    var time_el = $('#time')
    var date_el = $('#date')
    setInterval(() => {
        let momentNow = moment().tz('Europe/Moscow');
        time_el.html(momentNow.format('H:mm'));
        date_el.html(momentNow.format('DD/MM/YYYY'));
    }, 400);
});