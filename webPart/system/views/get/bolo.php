<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

R::selectDatabase('cad');

$params['bolo_p'] = R::getAssocRow('SELECT * FROM bolo_peoples');
$params['bolo_v'] = R::getAssocRow('SELECT * FROM bolo_vehicle');

returnSuccess($twig->render('_components/bolo.twig', $params));