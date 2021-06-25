<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['casefiles'] = R::getAssocRow('SELECT * FROM casefiles ORDER BY id DESC');

returnSuccess($twig->render('_components/casefile_list.twig', $params));
