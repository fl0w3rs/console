<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

if(!isAuthorized()) returnError('Unauthorized.');

use Rakit\Validation\Validator;

$user = getUser();

$validator = new Validator;

$validation = $validator->make($_POST, [
    'non-newname' => 'required|min:3|max:48'
]);

$validation->setAliases([
    'non-newname' => 'Новое имя'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

R::exec("UPDATE users SET officer_name = ? WHERE id = ?", [htmlspecialchars($_POST['non-newname']), $user['id']]);

returnSuccess('Новое имя установлено.');