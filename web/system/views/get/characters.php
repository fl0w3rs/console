<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['characters'] = getUserCharacters($params['user']['fid']);

returnSuccess($twig->render('_components/civil/characters.twig', $params));