<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['avail'] = getUnitByUid($params['user']['id']);

returnSuccess($twig->render('_components/status.twig', $params));