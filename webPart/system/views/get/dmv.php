<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$data = checkParams(['plate']);

R::selectDatabase('cad');

$params['dmv_vehicle'] = R::getRow('SELECT * FROM characters_vehicles WHERE vin = ? OR plate = ?', [$data['plate'], $data['plate']]);
if(!isset($params['dmv_vehicle']['id'])) {
    returnError('Не найдено.');
}

$params['owner'] = R::getRow('SELECT * FROM characters WHERE id = ?', [$params['dmv_vehicle']['char_id']]);

returnSuccess($twig->render('_components/dmv.twig', $params));