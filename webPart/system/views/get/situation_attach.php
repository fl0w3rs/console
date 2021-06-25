<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$data = checkParams(['sitid'], 'GET');

R::selectDatabase('cad');
$find = R::findOne('situations', 'id = ? AND status = 1', [$data['sitid']]);
if($find === null) {
    returnError('Не найдено.');
}

$units = getUnitsAttachedToSituation($data['sitid']);
$mapped_units = [];
// var_dump($units);
foreach($units as $unit) {
    array_push($mapped_units, (int)$unit['id']);
}

if(count($mapped_units) > 0 ) {
    $params['units'] = R::getAssocRow('SELECT * FROM units WHERE type = 1 AND uid NOT IN ('. R::genSlots( $mapped_units ) .')', $mapped_units);
} else {
    $params['units'] = R::getAssocRow('SELECT * FROM units WHERE type = 1');
}

$params['units_char'] = [];
for($i = 0; $i < count($params['units']); $i++) {
    $params['units_char'][$i] = R::getRow('SELECT * FROM users WHERE id = ?', [$params['units'][$i]['uid']]);
}
$params['situation'] = $find;

returnSuccess($twig->render('_components/situation_attach.twig', $params));