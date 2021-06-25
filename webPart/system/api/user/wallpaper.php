<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

if(!isAuthorized()) returnError('Unauthorized.');

use Rakit\Validation\Validator;

$user = getUser();

$validator = new Validator;

$validation = $validator->make($_FILES, [
    'img' => 'required|uploaded_file:0,5000K,png,jpeg'
]);

$validation->setAliases([
    'img' => 'Изображение'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

R::selectDatabase('cad');

$name = $_FILES["img"]["name"];
$ext = end((explode(".", $name)));

$tmp_name = $_FILES['img']['tmp_name'];

$new_hash = substr(md5(openssl_random_pseudo_bytes(20)),-32);

move_uploaded_file($tmp_name, __DIR__."/../../../uploads/wallpapers/".$new_hash.".". $ext);

R::exec("UPDATE users SET wallpaper = CONCAT(?, '.', ?) WHERE id = ?", [$new_hash, $ext, $user['id']]);

returnSuccess('Фото изменено.');