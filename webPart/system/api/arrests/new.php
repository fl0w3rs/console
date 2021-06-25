<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

if(!isAuthorized()) returnError('Unauthorized.');

use Rakit\Validation\Validator;

$user = getUser();

$validator = new Validator;

$validation = $validator->make($_POST, [
    'charid' => 'required|integer',
    'newarrest-time' => 'required|regex:/^[0-2][0-9]:[0-6][0-9]$/',
    'newarrest-reasons' => 'required|array',
    'newarrest-details' => 'present|max:512',
    'newarrest-location' => 'required|min:3|max:96'
]);

$validation->setAliases([
    'charid' => 'ID персонажа',
    'newarrest-time' => 'Время ареста',
    'newarrest-reasons' => 'Нарушения',
    'newarrest-details' => 'Детали',
    'newarrest-location' => 'Место ареста'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

R::selectDatabase('cad');
$char = R::getRow("SELECT * FROM characters WHERE id = ?", [(int)$_POST['charid']]);

if(!isset($char['id'])) {
    returnError('Персонаж не найден.');
}

$new = R::dispense('arrests');
$new->char_id = (int)$_POST['charid'];
$new->creator = $user['id'];
$new->reasons = implode(',', $_POST['newarrest-reasons']);
$new->time = $_POST['newarrest-time'];
$new->details = htmlspecialchars($_POST['newarrest-details']);
$new->location = htmlspecialchars($_POST['newarrest-location']);
$new->title = '';
$id = R::store($new);

$new = R::load('arrests', $id);
$new->title = $id . '-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
R::store($new);

returnSuccess('Арест создан.');