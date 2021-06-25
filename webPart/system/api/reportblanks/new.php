<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

if(!isAuthorized()) returnError('Unauthorized.');

use Rakit\Validation\Validator;

$user = getUser();

$validator = new Validator;

$validation = $validator->make($_POST, [
    'newreportblank-from' => 'required|max:32',
    'newreportblank-to' => 'required|max:32',
    'newreportblank-rank' => 'required|max:32',
    'newreportblank-time' => 'required|regex:/^[0-2][0-9]:[0-6][0-9]$/',
    'newreportblank-description' => 'required|max:4096'
]);

$validation->setAliases([
    'newreportblank-from' => 'От кого',
    'newreportblank-to' => 'Кому адресовано',
    'newreportblank-rank' => 'Звание',
    'newreportblank-time' => 'Время',
    'newreportblank-description' => 'Описание'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

$new = R::xdispense('report_blanks');
$new -> creator = $user['id'];
$new -> rb_from = htmlspecialchars($_POST['newreportblank-from']);
$new -> rb_to = htmlspecialchars($_POST['newreportblank-to']);
$new -> rank = htmlspecialchars($_POST['newreportblank-rank']);
$new -> time = $_POST['newreportblank-time'];
$new -> description = htmlspecialchars($_POST['newreportblank-description']);
$new -> department = $user->department;
$new -> additional = '';
R::store($new);

returnSuccess('Рапорт создан.');