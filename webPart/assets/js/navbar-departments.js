$(function() {
    $('#dept-select-navbar').on("change", function(event) {
        updateDept(event.target.value)
    });
});

const updateDept = (id) => {
    $.post( SITE + "api/user/select_dept", { new_dept: id }, function( data ) {
        if(data.status == 'success') {
            window.location = SITE + data.message.link;
        } else if(data.status == 'error') {
            Notiflix.Notify.Failure(data.message);
        }
    }, "json" );
}