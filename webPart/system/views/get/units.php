<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['units'] = R::getAssocRow('SELECT * FROM units WHERE type = 1');
for($i = 0; $i < count($params['units']); $i++) {
    $params['units_char'][$i] = R::getRow('SELECT * FROM users WHERE id = ?', [$params['units'][$i]['uid']]);
    $params['units_calls'][$i] = getSituationsAttachedToUnit($params['units'][$i]['uid']);
}

returnSuccess($twig->render('_components/units.twig', $params));