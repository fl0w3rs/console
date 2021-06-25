<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

if(!isAuthorized()) returnError('Unauthorized.');

use Rakit\Validation\Validator;

$user = getUser();

$validator = new Validator;

$validation = $validator->make($_POST, [
    'ecf-severity' => 'required|integer|in:1,2,3',
    'ecf-title' => 'required|max:32',
    'ecf-subject' => 'required|max:128',
    'ecf-description' => 'required|max:24576',
    'ecf-evidence' => 'required|max:24576',
    'ecf-witnesses' => 'required|max:24576',
    'ecf-detectives' => 'present|array|max:10',
    'ecf-reasons' => 'required|array',
    'ecf-id' => 'required|integer',
    'ecf-status' => 'required|integer|in:0,1,2'
]);

$validation->setAliases([
    'ecf-severity' => 'Тяжесть',
    'ecf-title' => 'Название',
    'ecf-subject' => 'Субъект',
    'ecf-description' => 'Описание',
    'ecf-evidence' => 'Улики',
    'ecf-witnesses' => 'Свидетели',
    'ecf-detectives' => 'Участвующие детективы',
    'ecf-reasons' => 'Преступления',
    'ecf-id' => 'ID кейс-файла',
    'ecf-status' => 'Статус'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

$tmp = R::getRow('SELECT * FROM casefiles WHERE id = ?', [$_POST['ecf-id']]);
if(!haveUserAccessToCF($tmp)) {
    returnError('Нету доступа к этому кейс-файлу.');
}

$edit = R::load('casefiles', $_POST['ecf-id']);
$edit->status = (int)$_POST['ecf-status'];
$edit->title = htmlspecialchars($_POST['ecf-title']);
$edit->severity = (int)$_POST['ecf-severity'];
$edit->subject = htmlspecialchars($_POST['ecf-subject']);
$edit->description = htmlspecialchars($_POST['ecf-description']);
$edit->evidence = htmlspecialchars($_POST['ecf-evidence']);
$edit->witnesses = htmlspecialchars($_POST['ecf-witnesses']);
$edit->detectives = implode(',', $_POST['ecf-detectives']);
$edit->reasons = implode(',', $_POST['ecf-reasons']);
$id = R::store($edit);

// $wsc = getWSConnection();
// sendWSMessage($wsc, 'pb-new-citation', ['id' => $char['owner']]);

returnSuccess('Кейс-файл отредактирован.');