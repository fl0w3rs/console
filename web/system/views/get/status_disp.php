<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['avail'] = getUnitByUid($params['user']['id'], 2);

returnSuccess($twig->render('_components/status_disp.twig', $params));