<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

if(!isAuthorized()) returnError('Unauthorized.');

use Rakit\Validation\Validator;

$user = getUser();

$validator = new Validator;

$validation = $validator->make($_POST, [
    'newcasefile-severity' => 'required|integer|in:1,2,3',
    'newcasefile-title' => 'required|max:32',
    'newcasefile-subject' => 'required|max:128',
    'newcasefile-description' => 'required|max:24576',
    'newcasefile-evidence' => 'required|max:24576',
    'newcasefile-witnesses' => 'required|max:24576',
    'newcasefile-detectives' => 'present|array|max:10',
    'newcasefile-reasons' => 'required|array'
]);

$validation->setAliases([
    'newcasefile-severity' => 'Тяжесть',
    'newcasefile-title' => 'Название',
    'newcasefile-subject' => 'Субъект',
    'newcasefile-description' => 'Описание',
    'newcasefile-evidence' => 'Улики',
    'newcasefile-witnesses' => 'Свидетели',
    'newcasefile-detectives' => 'Участвующие детективы',
    'newcasefile-reasons' => 'Преступления'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

$new = R::dispense('casefiles');
$new->creator = $user['id'];
$new->title = htmlspecialchars($_POST['newcasefile-title']);
$new->severity = (int)$_POST['newcasefile-severity'];
$new->subject = htmlspecialchars($_POST['newcasefile-subject']);
$new->description = htmlspecialchars($_POST['newcasefile-description']);
$new->evidence = htmlspecialchars($_POST['newcasefile-evidence']);
$new->witnesses = htmlspecialchars($_POST['newcasefile-witnesses']);
$new->detectives = implode(',', $_POST['newcasefile-detectives']);
$new->reasons = implode(',', $_POST['newcasefile-reasons']);
$id = R::store($new);

// $wsc = getWSConnection();
// sendWSMessage($wsc, 'pb-new-citation', ['id' => $char['owner']]);

returnSuccess('Кейс-файл создан.');