var ws_init = false
var ws_auth = false
var ws_auth_error = false
var ws_auth_error_time = 0
var ws

function connectWebsocket() {
    ws_init = false
    ws_auth = false

    ws = new WebSocket('wss://console.fl0w3rs.dev')

    Notiflix.Loading.Pulse('Connecting to WS...');

    ws.onopen = async (event) => {
        ws_auth_error_time = 0;
        ws_init = true;
        console.log('[WS] Connected.')
        sendWebsocket('authenticate', {token: getCookie('console_session')})
    }

    ws.onclose = async (event) => {
        ws_auth_error_time++;
        if(ws_auth_error_time > 3) { ws_auth_error = true; removePage('Ошибка', 'Неудачное подключение к WS.') }
        ws_init = false
        if(ws_auth_error) {
            console.log('[WS] Auth error. Disconnected.')
        } else {
            console.log(`[WS] Disconnected. Retrying in ${1 + (ws_auth_error_time * 2)} seconds.`)
            setTimeout(() => { console.log('[WS] Making attempt of connection.'); connectWebsocket(); }, 1000 * (1 + (ws_auth_error_time * 2)))
        }
    }
        

    ws.addEventListener("message", (message) => {
        var event = JSON.parse(message.data)
        if(event.type == 'auth_error') {
            ws_auth_error = true
            Notiflix.Loading.Remove();
            removePage('Ошибка', 'Неудачная авторизация в WS.')
        } else if(event.type == 'auth_success') {
            ws_auth = true
            Notiflix.Loading.Remove();
            console.log('[WS] Authorized.')
        } else if(event.type == 'unauthorized') {
            ws_auth_error = true
            removePage('Ошибка', 'Неудачная авторизация в WS.')
        } else if(event.type == 'error') {
            Notiflix.Notify.Failure(event.payload)
        } else if(event.type == 'update-situations') {
            updateSituations()
        } else if(event.type == 'update-disp-list') {
            updateDispList()
        } else if(event.type == 'update-status') {
            updateStatus()
        } else if(event.type == 'update-bolo') {
            updateBolo()
        } else if(event.type == 'notify-success') {
            Notiflix.Notify.Success(event.payload)
        } else if(event.type == 'notify-error') {
            Notiflix.Notify.Failure(event.payload)
        } else if(event.type == 'report-success') {
            Notiflix.Report.Success(event.payload.title, event.payload.message)
        } else if(event.type == 'report-error') {
            Notiflix.Report.Failure(event.payload.title, event.payload.message)
        } else if(event.type == 'reload') {
            location.reload()
        } else if(event.type == 'update-situation-info') {
            const id = event.payload.id
            const ele = $('#sitInfoModal.show')
            if(ele.length > 0 && ele.data('sit-id') == id) {
                showSituationInfo(event.payload.id)
            }
        } else if(event.type == 'new_window') {
            var curr
            switch($('#currentTemplate').val()) {
                case 'mdt': { curr = 1; break; }
                case 'cad': { curr = 2; break; }
                case 'civil': { curr = 3; break; }
                case 'fire': { curr = 4; break; }
                default: { curr = 0; }
            }
            if(curr != event.payload.type) {
                ws_auth_error = true
                ws.close()
                removePage('Ошибка', 'Вы перешли на другой департамент. Перезагрузите страницу.')
            }
        }
    })

    for(var i = 0; i < wsHooks.length; i++) {
        ws.addEventListener("message", wsHooks[i])
    }
    
}

function removePage(title, message) {
    Notiflix.Loading.Remove();
    $('#header').remove();
    $('#main-content').remove();
    $('.windows__downbar').remove();
    Notiflix.Report.Failure(title, message, 'ОК');
}

function sendWebsocket(type, payload = {}) {
    ws.send(JSON.stringify({type: type, payload: payload}))
}

function getCookie(cname) {
    var name = cname + "="
    var decodedCookie = decodeURIComponent(document.cookie)
    var ca = decodedCookie.split(';')
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i]
        while (c.charAt(0) == ' ') {
            c = c.substring(1)
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length)
        }
    }
    return ""
}

$(() => {
    connectWebsocket()
})