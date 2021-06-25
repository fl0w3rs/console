var sto_sound
var sto_button
var sto_interval

$(() => {
    sto_sound = new Audio(SITE + 'assets/sounds/signal-100.mp3')
    sto_sound.volume = 0.07;
    sto_button = $('#button-sto')


    if(sto_button.length == 0) console.log('[sto] Кнопка сигнала 100 не найдена.') 
    else {
        
        wsHooks.push((message) => { 
            var event = JSON.parse(message.data)
            if(event.type == 'signal100') {
                sto_button.toggleButtonActive(event.payload.state, 'btn-amber btn-blinking', 'btn-dark bg-cst-gray')
                if(event.payload.state == 1) {
                    sto_sound.play()
                    clearInterval(sto_interval)
                    sto_interval = setInterval(stoCheck, 25000)
                }
            }
        })

        sto_button.on('click', () => {
            sendWebsocket('sto-click')
        })

        sto_interval = setInterval(stoCheck, 25000)
    }
})

async function stoCheck() {
    if(sto_button.data('active') == 1 && $('[data-action="mute-buttons"]').data('active') == 0) {
        sto_sound.play()
    }
}