<?php 

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

$data = checkParams(['id']);

$char = R::findOne('characters', 'id = ? AND owner = ?', [$data['id'], $user['fid']]);
if($char === null) {
    returnError('Персонаж не существует или не принадлежит Вам.');
}

R::hunt('characters', 'id = ?', [$data['id']]);
R::hunt('characters_weapons', 'char_id = ?', [$data['id']]);
R::hunt('characters_vehicles', 'char_id = ?', [$data['id']]);

returnSuccess('Персонаж удалён.');