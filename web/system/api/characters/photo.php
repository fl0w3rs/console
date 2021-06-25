<?php


if(!isAuthorized()) returnError('Unauthorized.');

use Rakit\Validation\Validator;

$user = getUser();

$validator = new Validator;

$validation = $validator->make($_FILES, [
    'img' => 'required|uploaded_file:0,1500K,png,jpeg'
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
$char = R::getRow("SELECT * FROM characters WHERE id = ? AND owner = ?", [(int)$charid, $user['fid']]);



if(!isset($char['id'])) {
    returnError('Персонаж не найден.');
}

$tmp_name = $_FILES['img']['tmp_name'];
$ext = getUploadedImageExt($tmp_name);
if(!$ext) returnError('Допустимы только PNG и JPEG.');

$new_hash = substr(md5(openssl_random_pseudo_bytes(20)),-32);

move_uploaded_file($tmp_name, __DIR__."/../../../uploads/avatars/".$new_hash.".". $ext);

R::exec("UPDATE characters SET image = CONCAT(?, '.', ?) WHERE id = ?", [$new_hash, $ext, (int)$charid]);

returnSuccess('Фото изменено.');