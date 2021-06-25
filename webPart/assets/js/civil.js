function updateCharSelect(el, first = 'Персонаж', disabled = 'disabled') {
    var ele = $(el)
    $.get(SITE+'api/user/characters', (data) => {
        if(data.status == 'success') {
            ele.materialSelect({destroy: true})
            var chars_text = `<option value="-1" selected ${disabled}>${first}</option>`
            data.message.forEach((elem) => {
                chars_text += '<option value="'+ elem.id +'">' + elem.name + '</option>'
            })
            ele.html(chars_text)
            ele.materialSelect()
        }
    }, "json")
}

wsHooks.push((message) => {
    var event = JSON.parse(message.data)
    if(event.type == 'update-citations') {
        updateCitations()
    }
})

$(() => {
    updateCharSelect('#newcall-char', 'Очевидец', '')
    
    $('[data-target="#createWeaponModal"]').on('click', () => {
        updateCharSelect('#newweapon-char')
    })
    
    $('[data-target="#createVehModal"]').on('click', () => {
        updateCharSelect('#newveh-char')
    })

    $('[data-action="create-new-char"]').on('click', () => {
        $(this).prop('disabled', false)
        $.post( SITE + "api/characters/new", customSerialize($('#newchar-form')), ( data ) => {
            if(data.status == 'success') {
                Notiflix.Notify.Success(data.message.message)
                $('#createCharModal').modal('hide')

                var fdata = new FormData()
                fdata.append('img', $('#newchar-photo')[0].files[0])
                $.ajax( {url: SITE + `api/characters/${data.message.charid}/photo`, type: 'POST', data: fdata, success: ( data ) => {
                    if(data.status == 'success') {
                        updateCharacters()

                    } else if(data.status == 'error') {
                        Notiflix.Notify.Failure(data.message)
                    }
                }, dataType: 'json', processData:false,
                contentType: false});

                updateCharacters()

                updateCharSelect('#newcall-char', 'Очевидец', '')
            } else if(data.status == 'error') {
                Notiflix.Notify.Failure(data.message)
            }
          }, "json");
    })

    $('[data-action="create-new-weapon"]').on('click', () => {
        $.post( SITE + "api/characters/weapon/new", customSerialize($('#newweapon-form')), ( data ) => {
        if(data.status == 'success') {
            Notiflix.Notify.Success(data.message)
            $('#createWeaponModal').modal('hide')
            updateWeapons()
        } else if(data.status == 'error') {
            Notiflix.Notify.Failure(data.message)
        }
      }, "json");

    })

    $('[data-action="create-new-veh"]').on('click', () => {
        $.post( SITE + "api/characters/vehicle/new", customSerialize($('#newveh-form')), ( data ) => {
        if(data.status == 'success') {
            Notiflix.Notify.Success(data.message)
            $('#createVehModal').modal('hide')
            updateVehicles()
        } else if(data.status == 'error') {
            Notiflix.Notify.Failure(data.message)
        }
      }, "json");

    })

    $('[data-action="create-new-call"]').on('click', () => {
        sendWebsocket('create-call', customSerialize($('#newcall-form')))
        $('#newcall-form').trigger('reset')
    })

    $('#newCharPhotoFile').on("change", () => { 
        var fdata = new FormData()
        fdata.append('img', $('#newCharPhotoFile')[0].files[0])
        $.ajax( {url: SITE + `api/characters/${curr_char_photo_editing}/photo`, type: 'POST', data: fdata, success: ( data ) => {
            if(data.status == 'success') {
                updateCharacters()

            } else if(data.status == 'error') {
                Notiflix.Notify.Failure(data.message)
            }
        }, dataType: 'json', processData:false,
        contentType: false});
        $('#newCharPhotoFile').val('');
    });

})

var curr_char_photo_editing = -1;
function updateCharPhoto(id) {
    curr_char_photo_editing = id;
    $('#newCharPhotoFile').trigger('click');
}

function deleteCharacter(id) {
    
    Notiflix.Confirm.Show( 
        'Удаление',
        'Вы уверены, что хотите удалить выбранного персонажа?',
        'Да',
        'Нет',
        () => { 
            $.post( SITE + "api/characters/delete", { id: id }, ( data ) => {
                if(data.status == 'success') {
                    Notiflix.Notify.Success(data.message)

                    updateCharacters()
                    updateWeapons()
                    updateVehicles()

                    updateCharSelect('#newcall-char', 'Очевидец', '')
                } else {
                    Notiflix.Notify.Failure(data.message)
                }
              }, "json");
        })
}

function deleteWeapon(id) {
    
    Notiflix.Confirm.Show( 
        'Удаление',
        'Вы уверены, что хотите удалить выбранное оружие?',
        'Да',
        'Нет',
        () => { 
            $.post( SITE + "api/characters/weapon/delete", { id: id }, ( data ) => {
                if(data.status == 'success') {
                    Notiflix.Notify.Success(data.message)

                    updateWeapons()
                } else {
                    Notiflix.Notify.Failure(data.message)
                }
              }, "json");
        })
}

function deleteVehicle(id) {
    
    Notiflix.Confirm.Show( 
        'Удаление',
        'Вы уверены, что хотите удалить выбранную машину?',
        'Да',
        'Нет',
        () => { 
            $.post( SITE + "api/characters/vehicle/delete", { id: id }, ( data ) => {
                if(data.status == 'success') {
                    Notiflix.Notify.Success(data.message)

                    updateVehicles()
                } else {
                    Notiflix.Notify.Failure(data.message)
                }
              }, "json");
        })
}

function updateCharacters() {
    if($('#chars-table').length > 0) {
        $.get(SITE+'get/characters', function(data) {
            $('#chars-table').replaceWith(data.message)
        }, 'json')
    }
}

function updateWeapons() {
    if($('#weapons-table').length > 0) {
        $.get(SITE+'get/characters/weapons', function(data) {
            $('#weapons-table').replaceWith(data.message)
        }, 'json')
    }
}

function updateVehicles() {
    if($('#vehicles-table').length > 0) {
        $.get(SITE+'get/characters/vehicles', function(data) {
            $('#vehicles-table').replaceWith(data.message)
        }, 'json')
    }
}

function updateCitations() {
    if($('#citations-table').length > 0) {
        $.get(SITE+'get/characters/citations', function(data) {
            $('#citations-table').replaceWith(data.message)
        }, 'json')
    }
}

function payCitation(id) {
    $.post(SITE + 'api/citations/pay', {id: id}, data => {
        if(data.status == 'success') {
            updateCitations()
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function editCharacter(id) {
    $.get(SITE + 'get/characters/edit?id=' + id, data => {
        if(data.status == 'success') {

            $('#editModal').html(data.message)
            $('#ec-sex, #ec-race, #ec-driving-license, #ec-gun-license').materialSelect()
            openModal('editModal')
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

const submitCharacterEdit = () => {
    $.post(SITE + 'api/characters/edit', customSerialize($('#ec-form')), data => {
        if(data.status == 'success') {

            $('#editModal').modal('hide')
            updateCharacters()
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}

function editWeapon(id) {
    $.get(SITE + 'get/characters/weapons/edit?id=' + id, data => {
        if(data.status == 'success') {

            $('#editModal').html(data.message)
            openModal('editModal')
        } else {
            Notiflix.Notify.Failure(data.message)
        }
    })
}