<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['situations'] = getActiveSituations();

$params['situations_attached'] = [];
for($i = 0; $i < count($params['situations']); $i++) {
    $params['situations_attached'][$i] = getUnitsAttachedToSituation($params['situations'][$i]['id']);
    $params['situations_attached_info'][$i] = R::getRow("SELECT * FROM situations_attached WHERE unit_id = ? AND sit_id = ?", [$params['user']['id'], $params['situations'][$i]['id']]);
    $params['situation_is_attached'][$i] = isset($params['situations_attached_info'][$i]['id']);
}

returnSuccess($twig->render('_components/situations.twig', $params));