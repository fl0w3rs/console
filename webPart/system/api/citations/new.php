<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

if(!isAuthorized()) returnError('Unauthorized.');

use Rakit\Validation\Validator;

$user = getUser();

$validator = new Validator;

$validation = $validator->make($_POST, [
    'charid' => 'required|integer',
    'newcitation-time' => 'required|regex:/^[0-2][0-9]:[0-6][0-9]$/',
    'newcitation-reasons' => 'required|array',
    'newcitation-details' => 'present|max:512',
    'newcitation-amount' => 'required|integer',
    'newcitation-location' => 'required|min:3|max:96'
]);

$validation->setAliases([
    'charid' => 'ID персонажа',
    'newcitation-time' => 'Время нарушения',
    'newcitation-reasons' => 'Нарушения',
    'newcitation-details' => 'Детали',
    'newcitation-amount' => 'Сумма штрафа',
    'newcitation-location' => 'Место нарушения'
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

$new = R::dispense('citations');
$new->char_id = (int)$_POST['charid'];
$new->creator = $user['id'];
$new->reasons = implode(',', $_POST['newcitation-reasons']);
$new->time = $_POST['newcitation-time'];
$new->details = htmlspecialchars($_POST['newcitation-details']);
$new->amount = (int)$_POST['newcitation-amount'];
$new->location = htmlspecialchars($_POST['newcitation-location']);
$new->created_time = time();
$new->title = '';
$id = R::store($new);

$new = R::load('citations', $id);
$new->title = $id . '-' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
R::store($new);

$wsc = getWSConnection();
sendWSMessage($wsc, 'pb-new-citation', ['id' => $char['owner']]);

returnSuccess('Штраф выписан.');