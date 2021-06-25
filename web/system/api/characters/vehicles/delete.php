<?php 

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

$data = checkParams(['id']);

R::selectDatabase('cad');

$char = R::findOne('characters_vehicles', 'id = ? AND owner = ?', [$data['id'], $user['fid']]);
if($char === null) {
    returnError('Машина не существует или не принадлежит Вам.');
}

R::hunt('characters_vehicles', 'id = ?', [$data['id']]);

returnSuccess('Машина удалена.');