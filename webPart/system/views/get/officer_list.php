<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['officers'] = R::getAssocRow('SELECT * FROM users');

returnSuccess($twig->render('_components/officer_list.twig', $params));
