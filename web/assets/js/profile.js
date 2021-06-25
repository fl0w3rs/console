$( document ).ready(function() {
    $('[data-action="edit"]').on('click', function() {
        var steamid = $('#profile-steamid');

        $(this).prop('disabled', true);

        $.post( SITE + "api/user/profile", { steamid: steamid.val() }, ( data ) => {
            if(data.status == 'success') {
                Notiflix.Notify.Success(data.message)
                setTimeout(() => { location.reload() }, 1500)
            } else if(data.status == 'error') {
                Notiflix.Notify.Failure(data.message)
                $(this).prop('disabled', false)
            }
          }, "json");

    })
})