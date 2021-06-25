
$(async () => {
    $('*[data-action="create-new-report-blank"]').on('click', () => {
        $.post(SITE + 'api/reportblanks/new', customSerialize($('#newreportblank-form')), data => {
            if(data.status == 'success') {
                $('#newReportBlankModal').modal('hide');
                Notiflix.Report.Success('Успех', 'Рапорт успешно отправлен!')
            } else {
                Notiflix.Report.Failure('Ошибка', data.message)
            }
        })
    })

    $('*[data-action="create-new-case-file"]').on('click', () => {
        $.post(SITE + 'api/casefiles/new', customSerialize($('#newcasefile-form')), data => {
            if(data.status == 'success') {
                $('#newCaseFileModal').modal('hide');
                Notiflix.Report.Success('Успех', 'Кейс-файл успешно создан!')
            } else {
                Notiflix.Report.Failure('Ошибка', data.message)
            }
        })
    })

    $('#non-submit').on('click', () => {
        $.post(SITE + 'api/user/officername', customSerialize($('#non-form')), data => {
            if(data.status == 'success') {
                $('#newOfficerNameModal').modal('hide');
                $('#officer-name').html($('#non-newname').val())
                Notiflix.Report.Success('Успех', 'Новое имя успешно установлено!')
            } else {
                Notiflix.Report.Failure('Ошибка', data.message)
            }
        })
    })
})

const doEditCaseFile = () => {
    $.post(SITE + 'api/casefiles/edit', customSerialize($('#ecf-form')), data => {
        if(data.status == 'success') {
            Notiflix.Report.Success('Успех', 'Кейс-файл успешно отредактирован!')
        } else {
            Notiflix.Report.Failure('Ошибка', data.message)
        }
    })
}

function showCaseFileList() {
    $.get(SITE + 'get/casefile/list', data => {
        if(data.status == 'success') {
            $('#caseFileModal').html(data.message)
            openModal('caseFileModal')
        } else {
            Notiflix.Report.Failure('Ошибка', data.message)
        }
    })
}

function viewCaseFile(id) {
    $.get(SITE + 'get/casefile/' + id, data => {
        if(data.status == 'success') {
            $('#caseFileModal').html(data.message)
            openModal('caseFileModal')
        } else {
            Notiflix.Report.Failure('Ошибка', data.message)
        }
    })
}

function editCaseFile(id) {
    $.get(SITE + 'get/casefile/' + id + '/edit', data => {
        if(data.status == 'success') {
            $('#caseFileModal').html(data.message)
            $('#ecf-detectives, #ecf-severity, #ecf-status').materialSelect();
            openModal('caseFileModal')
        } else {
            Notiflix.Report.Failure('Ошибка', data.message)
        }
    })
}

const sendCommentToCf = (id) => {
    if($('#cf-addition').val().length < 3) {
        return
    }

    $.post(SITE + 'api/casefiles/comment', {id: id, text: $('#cf-addition').val()}, data => {
        if(data.status == 'success') {
            viewCaseFile(id)
        } else {
            Notiflix.Report.Failure('Ошибка', data.message)
        }
    })
}