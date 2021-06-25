<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['warrants'] = R::getAssocRow('SELECT * FROM warrants WHERE status != -1 ORDER BY id DESC');

returnSuccess($twig->render('_components/order_list.twig', $params));