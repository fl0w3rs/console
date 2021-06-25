var priority_sound
var priority_button
var priority_interval

$(() => {
    priority_sound = new Audio(SITE + 'assets/sounds/priority-button.mp3')
    priority_sound.volume = 0.07;
    priority_button = $('#button-priority')

    if(priority_button.length == 0) console.log('[PRIORITY] Кнопка приоритета не найдена.') 
    else {
        wsHooks.push((message) => { 
            var event = JSON.parse(message.data)
            if(event.type == 'priority') {
                priority_button.toggleButtonActive(event.payload.state, 'btn-dark-green btn-blinking', 'btn-dark bg-cst-gray')
                if(event.payload.state == 1) {
                    priority_sound.play()
                    clearInterval(priority_interval)
                    priority_interval = setInterval(priorityCheck, 9500)
                }
            }
        })

        priority_button.on('click', () => {
            sendWebsocket('priority-click')
        })

        priority_interval = setInterval(priorityCheck, 9500)
    }
})

async function priorityCheck() {
    if(priority_button.data('active') == 1 && $('[data-action="mute-buttons"]').data('active') == 0) {
        priority_sound.play()
    }
}