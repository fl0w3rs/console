<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$data = checkParams(['sitid'], 'GET');

R::selectDatabase('cad');
$find = R::findOne('situations', 'id = ? AND status = 1', [$data['sitid']]);
if($find === null) {
    returnError('Не найдено.');
}

$params['attached_units'] = getUnitsAttachedToSituation($data['sitid']);
// var_dump($params['attached_units']);

$params['logs'] = R::getAssocRow('SELECT * FROM situations_logs WHERE sit_id = ?', [$find->id]);
$params['situation'] = $find;

returnSuccess($twig->render('_components/situation_info.twig', $params));