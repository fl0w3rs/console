<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

use Rakit\Validation\Validator;

$validator = new Validator;

$validation = $validator->make($_GET, [
    'charid' => 'required|integer'
]);

$validation->setAliases([
    'charid' => 'ID персонажа'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

R::selectDatabase('cad');
$params['character'] = R::getRow("SELECT * FROM characters WHERE id = ?", [(int)$_GET['charid']]);

if(!isset($params['character']['id'])) {
    returnError('Персонаж не найден.');
}

returnSuccess($twig->render('_components/new_arrest.twig', $params));