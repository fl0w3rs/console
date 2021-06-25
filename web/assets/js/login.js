$( document ).ready(function() {
    var authStatusElem = $('#auth-status')
    authStatusElem.html('<h5>Делаем запрос к API...</h5>')
    $.ajax({
        method: 'POST',
        url: SITE + 'api/auth'
    }).done(function( resp ) {
        if(resp.status == 'success') {
            authStatusElem.html('<h5 class="text-success">Успешная авторизация</h5>')
            setTimeout(() => { window.location = SITE+'dashboard' }, 2000)
        } else {
            authStatusElem.html('<h5 class="text-danger">У вас нету доступа к базе данных</h5>')
            authStatusElem.removeClass('blink-animation')
        }
      });
    
});