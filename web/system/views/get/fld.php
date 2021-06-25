<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$data = checkParams(['serial']);

R::selectDatabase('cad');

$params['fld_weapon'] = R::getRow('SELECT * FROM characters_weapons WHERE serial = ?', [$data['serial']]);
if(!isset($params['fld_weapon']['id'])) {
    returnError('Не найдено.');
}

$params['owner'] = R::getRow('SELECT * FROM characters WHERE id = ?', [$params['fld_weapon']['char_id']]);

returnSuccess($twig->render('_components/fld.twig', $params));