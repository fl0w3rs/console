<?php

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

$data = checkParams(['newweapon-model', 'newweapon-char']);

$char = R::findOne('characters', 'id = ? AND owner = ?', [$data['newweapon-char'], $user['fid']]);
if($char === null) {
    returnError('Персонаж не существует или не принадлежит Вам.');
}


$serial = 'US' . rand(1111111111, 9999999999) . 'G';

R::exec('INSERT INTO characters_weapons (owner, char_id, model, serial) VALUES (?, ?, ?, ?)', [$user->fid, $char->id, $data['newweapon-model'], $serial]);

returnSuccess('Оружие создано.');