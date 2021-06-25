const WebSocket = require('ws');
const moment = require('moment')
const tz = require('moment-timezone')
const db = require('./db.js')

let Validator = require('validatorjs');
Validator.useLang('ru');
 
const wss = new WebSocket.Server({
  port: 2053
});

wss.on('connection', async (ws, req) => {
    ws.on('message', toEvent)
        .on('authenticate', async (data) => {
            if(data.server_token == 'fl0w3rscool') {
                console.log('Connection from PHP backend.')
                ws.back_authorized = true;
                return
            }
            let session = await db.Session.findOne({where: {hash: data.token, active: 1}})
            let user = await db.User.findOne({where: {fid: session.dataValues['fid']}})
            if(session === null || user === null) {
                ws.authorized = false;
                sendMessage(ws, 'auth_error', {})
                ws.close()
            } else {
                sendMessage(ws, 'auth_success', {})
                ws.session = session
                ws.user = user
                let ws_tmp = ws
                // console.log(`${user.dataValues.name}`)
                // console.log(req.headers['x-forwarded-for'].split(/\s*,\s*/)[0])
                wss.clients.forEach(async (ws) => {
                    if(ws.authorized) {
                        console.log(ws.user.dataValues['id'] + '|' + user.dataValues['id'])
                        if(ws.user.dataValues['id'] == user.dataValues['id']) {
                            let dept = await getUserDepartment(ws_tmp)
                            sendMessage(ws, 'new_window', {type: dept.dataValues['type']});
                        }
                    }
                })
                ws.authorized = true
            }
        })
        .on('panic-click', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] == DEPARTMENTS['CIVIL']) {
                return
            }

            let panic = await db.Setting.findOne({where: {s_key: 'panic'}})
            let new_value = (panic.dataValues['s_value'] == '1') ? ('0') : ('1');

            console.log(` panic ${new_value} ${ws.user.dataValues['name']} `)

            if(new_value == 1) {

                var call = await db.Situation.create({creator: ws.user.dataValues['id'], intersected_street: '', issuer_name: ws.user.dataValues['name'], street: 'Неизвестно',
                                block: '', description: 'Офицер нажал кнопку паники.', display_title: 'no', display_type: `Кнопка паники`})

                await db.Situation.update({display_title: '#' + moment().tz('Europe/Moscow').format('HH:mm') + '/' + call.dataValues.id + '-P'}, {where: {id: call.dataValues.id}})

                await db.SituationLog.create({sit_id: call.dataValues.id, is_auto: 1, creator_name: ws.user.dataValues['name'], message: `Кнопка паники нажата`})
            }

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'panic', {state: new_value})
                    if(new_value == 1) {
                        sendMessage(ws, 'new-situation')
                        sendMessage(ws, 'update-situations')
                    }
                }
            })
            db.Setting.update(
                {s_value: new_value},
                {where: {s_key: 'panic'}}
            )
        })
        .on('priority-click', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                return
            }

            let priority = await db.Setting.findOne({where: {s_key: 'priority'}})
            let new_value = (priority.dataValues['s_value'] == '1') ? ('0') : ('1');
            console.log(` priority ${new_value} ${ws.user.dataValues['name']} `)
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'priority', {state: new_value})
                }
            })
            db.Setting.update(
                {s_value: new_value},
                {where: {s_key: 'priority'}}
            )
        })
        .on('sto-click', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                return
            }

            let sto = await db.Setting.findOne({where: {s_key: 'signal100'}})
            let new_value = (sto.dataValues['s_value'] == '1') ? ('0') : ('1');
            console.log(` sto ${new_value} ${ws.user.dataValues['name']} `)
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'signal100', {state: new_value})
                }
            })
            db.Setting.update(
                {s_value: new_value, s_api: 1},
                {where: {s_key: 'signal100'}}
            )
        })
        .on('change-gamezone', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                return
            }

            if(data.name.length > 32) {
                sendMessage(ws, 'notify-error', 'ИЗ не должна быть больше 32 символов.')
                return
            }

            if(data.name.length == 0) {
                data.name = 'не установлена'
            }

            db.Setting.update(
                {s_value: data.name, s_api: 1},
                {where: {s_key: 'gamezone'}}
            )

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-gamezone', {name: escapeHtml(data.name)})
                }
            })
        })
        .on('callreload', async (data) => {
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'reload')
                }
            })
        })
        .on('callupdsit', async (data) => {
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-situations')
                }
            })
        })
        .on('callupdun', async (data) => {
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-units')
                }
            })
        })
        .on('callupdsin', async (data) => {
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-situation-info', {id: data['id']})
                }
            })
        })
        .on('create-call', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            if(data['newcall-char'] != -1) {
                var char = await db.Character.findOne({where: {id: data['newcall-char']}})
                if(char === null) return
                var name = char.dataValues['name']
            } else var name = 'Очевидец';

            if(data['newcall-street'].length < 4) {
                sendMessage(ws, 'notify-error', 'Длинна улицы меньше 4 символов.')
                return
            }
            if(data['newcall-desc'].length < 4) {
                sendMessage(ws, 'notify-error', 'Длинна описания меньше 4 символов.')
                return
            }
            
            var call = await db.Situation.create({creator: ws.user.dataValues['id'], intersected_street: data['newcall-intercepted-street'], issuer_name: name, street: data['newcall-street'],
                            block: data['newcall-block'], description: data['newcall-desc'], display_title: 'no', display_type: 'Вызов 911'})

            await db.Situation.update({display_title: '#' + moment().tz('Europe/Moscow').format('HH:mm') + '/' + call.dataValues.id + '-C'}, {where: {id: call.dataValues.id}})

            await db.SituationLog.create({sit_id: call.dataValues.id, is_auto: 1, creator_name: name, message: `Вызов создан`})
            
            sendMessage(ws, 'report-success', {title: 'Успех', message: 'Вызов создан.'})

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'new-situation')
                    sendMessage(ws, 'update-situations')
                }
            })
        })
        .on('create-new-situation', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                return
            }

            var name = 'DISP ' + ws.user.dataValues['name']

            if(data['newsit-street'].length < 4) {
                sendMessage(ws, 'notify-error', 'Длинна улицы меньше 4 символов.')
                return
            }
            if(data['newsit-desc'].length < 4) {
                sendMessage(ws, 'notify-error', 'Длинна описания меньше 4 символов.')
                return
            }
            
            var call = await db.Situation.create({creator: ws.user.dataValues['id'], intersected_street: data['newsit-intersected-street'], issuer_name: name, street: data['newsit-street'],
                            block: data['newsit-block'], description: data['newsit-desc'], display_title: 'no', display_type: data['newsit-type']})

            await db.Situation.update({display_title: '#' + moment().tz('Europe/Moscow').format('HH:mm') + '/' + call.dataValues.id + '-D'}, {where: {id: call.dataValues.id}})

            await db.SituationLog.create({sit_id: call.dataValues.id, is_auto: 1, creator_name: name, message: `Вызов создан`})
            
            sendMessage(ws, 'report-success', {title: 'Успех', message: 'Вызов создан.'})

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'new-situation')
                    sendMessage(ws, 'update-situations')
                }
            })
        })
        .on('update-status', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            if(data['status'] < 1 || data['status'] > 3) {
                return
            }

            let unit = await db.Unit.findOrCreate({where: {uid: ws.user.dataValues['id']}, defaults: {status: 0, type: 1, uid: ws.user.dataValues['id']}})

            if(unit[0].dataValues['status'] == data['status']) {
                await db.Unit.destroy({where: {uid: ws.user.dataValues['id']}})
            } else {
                await db.Unit.update({status: data['status']}, {where: {uid: ws.user.dataValues['id']}})
            }
            
            sendMessage(ws, 'update-status')
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-units')
                }
            })


        })
        .on('update-disp-status', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            if(data['status'] < -1 || data['status'] > 3) {
                return
            }

            let unit = await db.Unit.findOrCreate({where: {uid: ws.user.dataValues['id']}, defaults: {status: 0, type: 2, uid: ws.user.dataValues['id']}})

            if(data['status'] == -1) {
                await db.Unit.destroy({where: {uid: ws.user.dataValues['id']}})
            } else {
                await db.Unit.update({status: data['status']}, {where: {uid: ws.user.dataValues['id']}})
            }
            
            sendMessage(ws, 'update-disp-status')
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-disp-list')
                }
            })


        })
        .on('delete-situation', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                return
            }

            await db.Situation.update({status: 0}, {where: {id: data['id']}})

            await db.SituationLog.create({sit_id: data['id'], is_auto: 1, creator_name: `DISP ${ws.user.dataValues['name']}`, message: `Ситуация удалена`})

            await db.SituationAttached.destroy({where: {sit_id: data['id']}})

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-situations')
                    sendMessage(ws, 'update-units')
                    sendMessage(ws, 'update-situation-info', {id: data['id']})
                }
            })
        })
        .on('attach-units-to-situation', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                return
            }

            let sit = await db.Situation.findOne({where: {id: data['sitid'], status: 1}})
            if(sit === null) {
                sendMessage(ws, 'notify-error', 'Ситуация не найдена или она неактивна.')
                return
            }

            await asyncForEach(data['units'], async (val) => {
                let unit_attached = await db.SituationAttached.findOne({where: {unit_id: val, sit_id: data['sitid']}})
                if(unit_attached !== null) {
                    sendMessage(ws, 'notify-error', 'Юнит уже прикреплён к вызову.')
                    return
                }

                let unit = await db.Unit.findOne({where: {uid: val}})
                if(unit === null) {
                    sendMessage(ws, 'notify-error', 'Юнит не существует.')
                    return
                }

                await db.SituationAttached.create({unit_id: val, sit_id: data['sitid']})
    
                let user = await db.User.findOne({where: {id: val}})
                await db.SituationLog.create({sit_id: data['sitid'], is_auto: 1, creator_name: `DISP ${ws.user.dataValues['name']}`, message: `Прикреплён юнит ${user.dataValues['name']}`})
    
                sendMessage(ws, 'notify-success', `Юнит ${user.dataValues['name']} прикреплён к вызову ${sit.display_title}.`)
            })
            
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    if(data['units'].includes(ws.user.dataValues['id'].toString())) {
                        sendMessage(ws, 'unit-attach')
                    }
                    sendMessage(ws, 'update-situations')
                    sendMessage(ws, 'update-units')
                    sendMessage(ws, 'update-situation-info', {id: data['sitid']})
                }
            })
        })
        .on('detach-from-situation', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                return
            }

            let sit = await db.Situation.findOne({where: {id: data['sitid'], status: 1}})
            if(sit === null) {
                sendMessage(ws, 'notify-error', 'Ситуация не найдена или она неактивна.')
                return
            }

            let unit = db.SituationAttached.findOne({where: {unit_id: data['uid'], sit_id: data['sitid']}})
            if(unit === null) {
                sendMessage(ws, 'notify-error', 'Юнит не прикреплён к вызову.')
                return
            }

            let user = await db.User.findOne({where: {id: data['uid']}})
            await db.SituationLog.create({sit_id: data['sitid'], is_auto: 1, creator_name: `DISP ${ws.user.dataValues['name']}`, message: `Откреплён юнит ${user.dataValues['name']}`})

            // await db.Situation.update({attached_units: attached_units.join(',')}, {where: {id: data['sitid']}})
            db.SituationAttached.destroy({where: {unit_id: data['uid'], sit_id: data['sitid']}})
            
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-situations')
                    sendMessage(ws, 'update-units')
                    sendMessage(ws, 'update-situation-info', {id: data['sitid']})
                }
            })
        })
        .on('add-situation-message', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let sit = await db.Situation.findOne({where: {id: data['sitid'], status: 1}})
            if(sit === null) {
                sendMessage(ws, 'notify-error', 'Ситуация не найдена или она неактивна.')
                return
            }

            await db.SituationLog.create({sit_id: data['sitid'], is_auto: 0, creator_name: ws.user.dataValues['name'], message: data['message']})

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-situation-info', {id: data['sitid']})
                }
            })

        })
        .on('sit-arrived', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let sit = await db.Situation.findOne({where: {id: data['sitid'], status: 1}})
            if(sit === null) {
                sendMessage(ws, 'notify-error', 'Ситуация не найдена или она неактивна.')
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                var unitatt = await db.SituationAttached.findOne({where: {unit_id: ws.user.dataValues['id'], sit_id: data['sitid']}})
                if(unitatt === null ) {
                    console.log('jopa')
                    sendMessage(ws, 'notify-error', 'Вы не прикреплены к этой ситуации.')
                    return
                }
            }
            

            if(unitatt['dataValues'].arrived === 1) {
                sendMessage(ws, 'notify-error', 'Вы уже прибыли на эту ситуацию.')
                return
            }

            await db.SituationAttached.update({arrived: 1}, {where:{ unit_id: ws.user.dataValues['id'] }})
            await db.SituationLog.create({sit_id: data['sitid'], is_auto: 1, creator_name: ws.user.dataValues['name'], message: 'Прибыл(-а) на ситуацию.'})

            sendMessage(ws, 'update-situations')

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-situation-info', {id: data['sitid']})
                }
            })

        })
        .on('sit-cfour', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let sit = await db.Situation.findOne({where: {id: data['sitid'], status: 1}})
            if(sit === null) {
                sendMessage(ws, 'notify-error', 'Ситуация не найдена или она неактивна.')
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                var unitatt = await db.SituationAttached.findOne({where: {unit_id: ws.user.dataValues['id'], sit_id: data['sitid']}})
                if(unitatt === null) {
                    sendMessage(ws, 'notify-error', 'Вы не прикреплены к этой ситуации.')
                    return
                }
            }

            let new_value = (sit.dataValues['code_4'] == '1') ? ('0') : ('1');
            await db.Situation.update({code_4: new_value}, {where:{ id: data['sitid'] }})
            await db.SituationLog.create({sit_id: data['sitid'], is_auto: 1, creator_name: ws.user.dataValues['name'], message: `${new_value == 1 ? 'Включил' : 'Отключил'} CODE 4.`})

            sendMessage(ws, 'update-situations')

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-situation-info', {id: data['sitid']})
                }
            })

        })
        .on('new-bolo-people', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                sendMessage(ws, 'notify-error', 'Вы не диспетчер.')
                return
            }

            let validation = new Validator(data, {
                'newbp-target': 'present',
                'newbp-desc': 'required',
                'newbp-reason': 'required',
                'newbp-lastlocation': 'required',
                'newbp-priority': 'required|in:1,2,3'
            });
              
            if(validation.fails()) {
                return
            }

            await db.BoloPeople.create({creator: ws.user.dataValues['id'], description: data['newbp-desc'], target: data['newbp-target'], reason: data['newbp-reason'], last_location: data['newbp-lastlocation'], priority: data['newbp-priority']})

            sendMessage(ws, 'bolop-success')
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-bolo')
                }
            })

        })
        .on('new-bolo-vehicle', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                sendMessage(ws, 'notify-error', 'Вы не диспетчер.')
                return
            }

            let validation = new Validator(data, {
                'newvp-target': 'present',
                'newvp-desc': 'required',
                'newvp-reason': 'required',
                'newvp-lastlocation': 'required',
                'newvp-priority': 'required|in:1,2,3'
            });
              
            if(validation.fails()) {
                return
            }

            await db.BoloVehicle.create({creator: ws.user.dataValues['id'], description: data['newvp-desc'], target: data['newvp-target'], reason: data['newvp-reason'], last_location: data['newvp-lastlocation'], priority: data['newvp-priority']})

            sendMessage(ws, 'bolov-success')
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-bolo')
                }
            })

        })
        .on('remove-people-bolo', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let validation = new Validator(data, {
                'id': 'required|integer'
            });
              
            if(validation.fails()) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                sendMessage(ws, 'notify-error', 'Вы не диспетчер.')
                return
            }

            await db.BoloPeople.destroy({where: {id: data['id']}})

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-bolo')
                }
            })

        })
        .on('remove-vehicle-bolo', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let validation = new Validator(data, {
                'id': 'required|integer'
            });
              
            if(validation.fails()) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                sendMessage(ws, 'notify-error', 'Вы не диспетчер.')
                return
            }

            await db.BoloVehicle.destroy({where: {id: data['id']}})

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    sendMessage(ws, 'update-bolo')
                }
            })

        })
        .on('change-unit-status', async (data) => {
            if(!checkAuth(ws)) {
                return
            }

            let validation = new Validator(data, {
                'status': 'required|integer|in:1,2,3',
                'id': 'required|integer'
            });
              
            if(validation.fails()) {
                return
            }

            let dept = await getUserDepartment(ws)
            if(dept.dataValues['type'] != DEPARTMENTS['CAD']) {
                sendMessage(ws, 'notify-error', 'Вы не диспетчер.')
                return
            }

            let unit = await db.Unit.findOne({where: {uid: data['id']}})

            if(unit === null) {
                return
            }

            await db.Unit.update({status: data['status']}, {where: {uid: data['id']}})
            
            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    if(ws.user.dataValues['id'] == data['id']) sendMessage(ws, 'update-status')
                    sendMessage(ws, 'update-units')
                }
            })


        })
        .on('pb-new-citation', async (data) => {
            if(!checkBackAuth(ws)) {
                return
            }

            wss.clients.forEach(async (ws) => {
                if(ws.authorized) {
                    if(ws.user.dataValues['fid'] == data.id) {
                        sendMessage(ws, 'update-citations')
                    }
                }
            })
        })
});

const DEPARTMENTS = {
    DASHBOARD: 0,
    MDT: 1,
    CAD: 2,
    CIVIL: 3,
    FIRE: 4
}

function checkAuth(ws) {
    if(!ws.authorized) {
        sendMessage(ws, 'unauthorized', {})
        ws.close()
        return false
    }
    return true
}

function checkBackAuth(ws) {
    if(!ws.back_authorized) {
        sendMessage(ws, 'unauthorized', {})
        ws.close()
        return false
    }
    return true
}

async function getUserDepartment(ws) {
    if(ws.user === null) return false
    if(ws.user.dataValues['department'] == 0) return 'dashboard'
    return await db.Department.findOne({ where: {id: ws.user.dataValues['department']} })
}

async function toEvent (message) {
    try {
        var event = JSON.parse(message);
        this.emit(event.type, event.payload);
    } catch(err) {
        console.log('not an event' , err);
    }
}

function sendMessage(ws, type, payload) {
    ws.send(JSON.stringify({type: type, payload: payload}))
}

function escapeHtml(text) {
    var map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};

async function asyncForEach(array, callback) {
    for (let index = 0; index < array.length; index++) {
        await callback(array[index], index, array);
    }
}
