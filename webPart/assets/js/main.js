(function ( $ ) {
    $.fn.toggleButtonActive = function (state, active_class = 'btn-unique', unactive_class = 'btn-elegant') {
        this.attr('data-active', state);
        this.data('active', state);
        this.addClass(state == 1 ? active_class : unactive_class)
        this.removeClass(state == 1 ? unactive_class : active_class)

        return this
    };
}( jQuery ));

const SITE = 'https://southlandproject.ru/console/'
var wsHooks = []
var notebook_last_content;

// $(window).on('load', function() {
//     $('#mdb-preloader').delay(3000).fadeOut(900);
// });

$(function() {
    $('.mdb-select').materialSelect();

    Notiflix.Notify.Init({
        plainText: false,
    })
    Notiflix.Report.Init({
        plainText: false,
        width: '400px',
        backgroundColor: '#121212',
        success: {
            titleColor: '#eeeeee',
            messageColor: '#eeeeee',
        },
    
        failure: {
            titleColor: '#eeeeee',
            messageColor: '#eeeeee',
        },
    
        warning: {
            titleColor: '#eeeeee',
            messageColor: '#eeeeee',
        },
    
        info: {
            titleColor: '#eeeeee',
            messageColor: '#eeeeee',
        },
    })
    Notiflix.Confirm.Init({
        backgroundColor: '#121212',
        titleColor: '#eeeeee',
        messageColor: '#eeeeee'
    })

    $('form').on('submit', (e) => {
        e.preventDefault()
    })
    
    // $('body').on('show.bs.modal', '.modal', function () {
    //     hideAllModals()
    // });

    $('[data-action="mute-buttons"]').on('click', () => {
        $.post(SITE + 'api/user/mute', {}, (data) => {
            if(data.status == 'success') {
                $('[data-action="mute-buttons"]').toggleButtonActive(data.message, 'text-danger', 'text-light')
            }
        })
    })

    $('#ncic-btn').on('click', () => {
        ncicSearch($('#ncic-people').val())
    })

    $('#dmv-btn').on('click', () => {
        dmvSearch($('#dmv-vehicle').val())
    })

    $('#fld-btn').on('click', () => {
        fldSearch($('#fld-serial').val())
    })

    $('#dbsearch__button').on('click', () => {
        let type = $('#dbsearch__type').val()
        console.log(type)
        if(type == 'NCIC') {
            ncicSearch($('#dbsearch__query').val())
        } else if(type == 'DMV') {
            dmvSearch($('#dbsearch__query').val())
        } else if(type == 'FLD') {
            fldSearch($('#dbsearch__query').val())
        }
    })

    updateDispList()

    notebook_last_content = $('#notebook__text').val()
    setInterval(() => {
        var noteBook = $('#notebook__text')
        if(notebook_last_content != noteBook.val()) {
            $.post(SITE + 'api/user/notebook', {text: noteBook.val()})
        }
        notebook_last_content = noteBook.val()
    }, 3000)
});

function showReportBlankList() {
    if($('#reportBlankListModal').length) {
        $.get(SITE + 'get/reportblanks', data => {
            if(data.status == 'success') {
                $('#reportBlankListModal').html(data.message)
                openModal('reportBlankListModal')
            } else {
                Notiflix.Notify.Failure(data.message)
            }
        })
    }
}

function updateDashboardWP() {
    var fdata = new FormData()
    fdata.append('img', $('#dbs-wp')[0].files[0])
    $.ajax( {url: SITE + `api/user/wallpaper`, type: 'POST', data: fdata, success: ( data ) => {
        if(data.status == 'success') {
            location.reload()

        } else if(data.status == 'error') {
            Notiflix.Notify.Failure(data.message)
        }
    }, dataType: 'json', processData:false,
    contentType: false});
    $('#dbs-wp').val('');
}

const gotoForum = () => {
    Notiflix.Confirm.Show( 
        'Переход на форум',
        'Вы уверены, что хотите перейти на форум?',
        'Да',
        'Нет',
        () => { 
            location.href = 'https://southlandproject.ru'
        })
}

function signRB(id) {
    $.post(SITE + 'api/reportblanks/sign', {id: id}, data => {
        if(data.status == 'success') {
            showReportBlankList()
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function declineRB(id) {
    $.post(SITE + 'api/reportblanks/decline', {id: id}, data => {
        if(data.status == 'success') {
            showReportBlankList()
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function archiveRB(id) {
    $.post(SITE + 'api/reportblanks/archive', {id: id}, data => {
        if(data.status == 'success') {
            showReportBlankList()
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function ncicSearch(name) {
    $.post(SITE + 'api/ncic', {name: name}, data => {
        if(data.status == 'success') {
            // hideAllModals()

            $('#ncicModal').html(data.message)
            // $('#ncicModal').modal('show')
            openModal('ncicModal')
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function showAllOfficersModal() {
    $.get(SITE + 'get/officer_list', data => {
        if(data.status == 'success') {
            $('#allOfficersModal').html(data.message)
            openModal('allOfficersModal')
        } else {
            Notiflix.Report.Failure('Ошибка', data.message)
        }
    })
}

function fldSearch(serial) {
    $.post(SITE + 'api/fld', {serial: serial}, data => {
        if(data.status == 'success') {
            // hideAllModals()

            $('#fldModal').html(data.message)
            // $('#fldModal').modal('show')
            openModal('fldModal')
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function dmvSearch(plate) {
    $.post(SITE + 'api/dmv', {plate: plate}, data => {
        if(data.status == 'success') {
            // hideAllModals()

            $('#dmvModal').html(data.message)
            // $('#dmvModal').modal('show')
            openModal('dmvModal')
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function hideAllModals() {
    // $('.modal-backdrop').remove()
    // $('.modal.show').removeAttr("style")
    $('.modal.show').modal('hide')
    // $('.modal.show').removeClass('show') 
}

function customSerialize(el) {
    var arr = {};
    el.find('input, select, textarea').each( (i, e) => {
        let ele = $(e);
        let ele_id = ele.attr('id');
        if(ele_id !== undefined) {
            if(ele.attr('type') != 'file') {
                arr[ele_id] = ele.val()
            }
        }
    });
    return arr
}

function updateSituations() {
    if($('#situations-table').length > 0) {
        $.get(SITE + `get/situations`, function(data) {
            $('#situations-table').replaceWith(data.message)
        }, 'json')
    }
}

function updateDispList() {
    if($('#displist').length) {
        $.get(SITE+'get/disp_list', function(data) {
            $('#displist').replaceWith(data.message);
        }, 'json')

    }
}

function updateStatus() {
    $.get(SITE+'get/status', function(data) {
        $('#status-buttons').replaceWith(data.message);
    }, 'json')
}

function updateBolo() {
    $.get(SITE+'get/bolo', function(data) {
        $('#bolo-container').replaceWith(data.message);
    }, 'json')
}

function sendSituationMessage(id, ele_sel) {
    var ele = $(ele_sel)
    if(ele.length > 0 && ele.val().length > 0) {
        sendWebsocket('add-situation-message', {sitid: id, message: ele.val()})
        ele.val('')
    }
}


function showNewArrest(id) {

    $.get(`${SITE}get/arrest/new?charid=${id}`, function(data) {
        if(data.status == 'success') {
            $('#newArrestModal').html(data.message)
            openModal('newArrestModal')
            $('#newarrest-reasons').materialSelect();
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    }, 'json')
}
function showNewCitation(id) {

    $.get(`${SITE}get/citation/new?charid=${id}`, function(data) {
        if(data.status == 'success') {
            $('#newCitationModal').html(data.message)
            openModal('newCitationModal')
            $('#newcitation-reasons').materialSelect();
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    }, 'json')
}

function showSituationInfo(id) {

    $.get(`${SITE}get/situation/info?sitid=${id}`, function(data) {
        if(data.status == 'success') {
            $('#sitInfoModal').html(data.message)
            $('#sitInfoModal').data('sit-id', id)
            $('#sitInfoModal').modal('show')
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    }, 'json')
}

function showWarrantList() {
    $.get(SITE + 'get/orderlist', data => {
        if(data.status == 'success') {
            $('#orderListModal').html(data.message)
            openModal('orderListModal')
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function openModal(modalid) {
    if($('#' + modalid).hasClass('show'))
        return

    if ($('.modal.fade.show').length > 0) { 
        var currentOpenModalId = $('.modal.fade.show').attr('id');
        $('#' + currentOpenModalId).on('hidden.bs.modal', function () {
            $('#' + modalid).modal('show');
            $('#' + currentOpenModalId).off('hidden.bs.modal');
        });
        $('#' + currentOpenModalId).modal('hide');
    } else {
        $('#' + modalid).modal('show');
    }
}

function makeCitation(ssn) {
    $.post(SITE + 'api/citations/new', customSerialize($('#newcitation-form')), data => {
        if(data.status == 'success') {
            ncicSearch(ssn)
        } else {
            Notiflix.Report.Failure('Ошибка', data.message)
        }
    })
}

function makeArrest(ssn) {
    $.post(SITE + 'api/arrests/new', customSerialize($('#newarrest-form')), data => {
        if(data.status == 'success') {
            ncicSearch(ssn)
        } else {
            Notiflix.Report.Failure('Ошибка', data.message)
        }
    })
}


function signWarrant(id) {
    $.post(SITE + 'api/warrants/sign', {id: id}, data => {
        if(data.status == 'success') {
            showWarrantList()
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function executeWarrant(id) {
    $.post(SITE + 'api/warrants/execute', {id: id}, data => {
        if(data.status == 'success') {
            showWarrantList()
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function cancelWarrant(id) {
    $.post(SITE + 'api/warrants/cancel', {id: id}, data => {
        if(data.status == 'success') {
            showWarrantList()
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function deleteWarrant(id) {
    $.post(SITE + 'api/warrants/delete', {id: id}, data => {
        if(data.status == 'success') {
            showWarrantList()
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

var click_sound = new Audio(SITE + 'assets/sounds/click.mp3')
const playClick = async () => {
    click_sound.play()
}