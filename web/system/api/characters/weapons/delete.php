<?php 

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

$data = checkParams(['id']);

R::selectDatabase('cad');

$char = R::findOne('characters_weapons', 'id = ? AND owner = ?', [$data['id'], $user['fid']]);
if($char === null) {
    returnError('Оружие не существует или не принадлежит Вам.');
}

R::hunt('characters_weapons', 'id = ?', [$data['id']]);

returnSuccess('Оружие удалено.');