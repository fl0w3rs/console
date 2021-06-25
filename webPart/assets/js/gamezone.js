$(() => {
    var gamezone_el = $('#gamezone')
    if(gamezone_el.length == 0) console.log('[GZ] Элемент ИЗ не найден.') 
    else {
        wsHooks.push((message) => { 
            var event = JSON.parse(message.data)
            
            if(event.type == 'update-gamezone') {
                gamezone_el.html('<i class="fas fa-location-arrow grey-text"></i>  ' + event.payload.name)
            }
        })
    }
})