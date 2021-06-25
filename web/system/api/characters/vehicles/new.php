<?php

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

$data = checkParams(['newveh-mark', 'newveh-model', 'newveh-plate', 'newveh-color', 'newveh-desc', 'newveh-char']);

$char = R::findOne('characters', 'id = ? AND owner = ?', [$data['newveh-char'], $user['fid']]);
if($char === null) {
    returnError('Персонаж не существует или не принадлежит Вам.');
}


R::exec('INSERT INTO characters_vehicles (owner, char_id, mark, model, plate, color, description, vin) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', 
        [$user->fid, $char->id, $data['newveh-mark'], $data['newveh-model'], $data['newveh-plate'],
        $data['newveh-color'], $data['newveh-desc'], getRandomVin()]);

returnSuccess('Машина создана.');