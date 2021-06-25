$( document ).ready(function() {
    $('[data-action="save"]').on('click', function() {
        const id = $(this).data('dept-id')

        var name = $('#dept-name');
        if(name.val().length < 3 || name.val().length > 32) {
            return Notiflix.Notify.Failure('Название должно содержать от 3 до 32 символов.')
        }

        var groups = $('#dept-groups')
        if(groups.val().length < 1) {
            return Notiflix.Notify.Failure('У департамента должна быть как минимум одна группа.')
        }

        var training = $('#dept-training')
        if(training.val() === null || !(training.val().includes('field_'))) {
            return Notiflix.Notify.Failure('Выберите поле тренировки.')
        }

        var supervisor_group = $('#dept-supervisor-group')
        if(supervisor_group.val() === null) {
            return Notiflix.Notify.Failure('Выберите группу супервайзера.')
        }

        var type = $('#dept-type')
        if(type.val() === null) {
            return Notiflix.Notify.Failure('Выберите тип департамента.')
        }

        $(this).prop('disabled', true);

        $.post( SITE + "api/admin/departments/edit", { id: id, name: name.val(), groups: groups.val(), sv_group: supervisor_group.val(), training: training.val(), type: type.val() }, ( data ) => {
            if(data.status == 'success') {
                Notiflix.Notify.Success(data.message)
                setTimeout(() => { location.reload() }, 1500)
            } else {
                Notiflix.Notify.Failure(data.message)
                $(this).prop('disabled', false);
            }
          }, "json");

    })
})