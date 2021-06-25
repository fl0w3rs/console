var panic_sound
var panic_button
var panic_interval

$(() => {
    panic_sound = new Audio(SITE + 'assets/sounds/panic-button.mp3')
    panic_sound.volume = 0.07;
    panic_button = $('#button-panic')

    if(panic_button.length == 0) console.log('[panic] Кнопка паники не найдена.') 
    else {
        wsHooks.push((message) => { 
            var event = JSON.parse(message.data)
            if(event.type == 'panic') {
                panic_button.toggleButtonActive(event.payload.state, 'btn-danger btn-blinking', 'btn-dark bg-cst-gray')
                if(event.payload.state == 1) {
                    panic_sound.play()
                    clearInterval(panic_interval)
                    panic_interval = setInterval(panicCheck, 10000)
                }
            }
        })

        panic_button.on('click', () => {
            sendWebsocket('panic-click')
        })

        panic_interval = setInterval(panicCheck, 10000)
    }
})

async function panicCheck() {
    if(panic_button.data('active') == 1 && $('[data-action="mute-buttons"]').data('active') == 0) {
        panic_sound.play()
    }
}