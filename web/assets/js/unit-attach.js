var attach_sound

$(() => {
    attach_sound = new Audio(SITE + 'assets/sounds/unit-attach.mp3')
    attach_sound.volume = 1;
        
    wsHooks.push((message) => { 
        var event = JSON.parse(message.data)
        if(event.type == 'unit-attach') {
            attach_sound.play()
        }
    })
})
