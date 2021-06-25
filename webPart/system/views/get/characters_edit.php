<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$user = getUser();

use Rakit\Validation\Validator;

$validator = new Validator;

$validation = $validator->make($_GET, [
    'id' => 'required|integer'
]);

$validation->setAliases([
    'id' => 'ID персонажа'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}


$params['char'] = R::getRow('SELECT * FROM characters WHERE id = ?', [(int)$_GET['id']]);

if(!isset($params['char']['id'])) {
    returnError('Персонаж не существует.');
}

if($params['char']['owner'] != $user['fid']) {
    returnError('Это не Ваш персонаж.');
}

returnSuccess($twig->render('_components/civil/character_edit.twig', $params));
