<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

if(!isAuthorized()) returnError('Unauthorized.');

use Rakit\Validation\Validator;

$user = getUser();

$validator = new Validator;

$validation = $validator->make($_POST, [
    'id' => 'required|integer',
    'text' => 'required|max:8192'
]);

$validation->setAliases([
    'id' => 'ID кейс-файла',
    'text' => 'Текст'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

$cf = R::getRow('SELECT * FROM casefiles WHERE id = ?', [(int)$_POST['id']]);
if(!isset($cf['id'])) {
    returnError('Кейс-файл не существует.');
}

if(!haveUserAccessToCF($cf)) {
    returnError('Нету доступа к этому кейс-файлу.');
}

$new = R::xdispense('casefiles_comments');
$new->creator = $user['id'];
$new->text = htmlspecialchars($_POST['text']);
$new->cf_id = (int)$_POST['id'];
R::store($new);

// $wsc = getWSConnection();
// sendWSMessage($wsc, 'pb-new-citation', ['id' => $char['owner']]);

returnSuccess('Комментарий создан.');