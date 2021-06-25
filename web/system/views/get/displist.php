<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$displist = R::getAssocRow('SELECT * FROM units WHERE type = 2');

$params['displist'] = [];

foreach($displist as $d) {
    $user = R::getRow("SELECT * FROM users WHERE id = ?", [$d['uid']]);
    if($user === null) continue;
    $params['displist'][$d['status']][] = $user['name'];
}

returnSuccess($twig->render('_components/displist.twig', $params));
