$(function() {
    const gz_btn = $('[data-action="gz-save"]');
    gz_btn.on('click', () => {
        sendWebsocket('change-gamezone', {name: $('#disp-gamezone').val()})
    })

    wsHooks.push((message) => {
        var event = JSON.parse(message.data)
        if(event.type == 'update-units') {
            updateUnits()
        }
    })

    $('[data-action="create-new-situation"]').on('click', () => {
        sendWebsocket('create-new-situation', customSerialize($('#newsit-disp-form')))
        $('#newsit-disp-form').trigger('reset')
    })

    $('[data-action="create-new-order"]').on('click', () => {
        $.post(SITE + 'api/warrants/new', customSerialize($('#neworder-form')), data => {
            if(data.status == 'success') {
                Notiflix.Report.Success("Успех", "Ордер успешно создан!")
                $('#warrantNewModal').modal('hide')
            } else {
                Notiflix.Report.Failure("Ошибка", data.message)
            }
        })

    })

    $('[data-action="create-new-bp"]').on('click', () => {
        sendWebsocket('new-bolo-people', customSerialize($('#newbp-form')));

    })

    $('[data-action="create-new-vp"]').on('click', () => {
        sendWebsocket('new-bolo-vehicle', customSerialize($('#newvp-form')));

    })

    wsHooks.push((message) => {
        var event = JSON.parse(message.data)
        if(event.type == 'bolop-success') {
            Notiflix.Report.Success("Успех", "BOLO успешно создан!")
            $('#boloPersonNewModal').modal('hide')
        } else if(event.type == 'bolov-success') {
            Notiflix.Report.Success("Успех", "BOLO успешно создан!")
            $('#boloVehicleNewModal').modal('hide')
        } else if(event.type == 'update-disp-status') {
            updateDispStatus()
        }
    })
})

function updateDispStatus() {
    $.get(SITE+'get/status_disp', function(data) {
        $('#disp-status-buttons').replaceWith(data.message);
    }, 'json')
}

function deleteSituation(id) {

    Notiflix.Confirm.Show( 
        'Удаление',
        'Вы уверены, что хотите завершить выбранный вызов?',
        'Да',
        'Нет',
        () => { 
            sendWebsocket('delete-situation', {id: id})
        })
}

function updateUnits() {
    if($('#units-table').length > 0) {
        $.get(SITE+`get/units`, function(data) {
            $('#units-table').replaceWith(data.message)
        }, 'json')
    }
}

function showSituationAttach(id) {

    $.get(`${SITE}get/situation/attach?sitid=${id}`, function(data) {
        if(data.status == 'success') {
            $('#sitAttachModal').html(data.message)
            $('#sitAttachModal').data('sit-id', id)
            $('#sitAttachModal').modal('show')

            $('#unitSelectEl').materialSelect();
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    }, 'json')
}