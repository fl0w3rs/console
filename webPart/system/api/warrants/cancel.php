<?php 

if(!isAuthorized()) returnError('Unauthorized.');
if(!isAdmin()) returnError('Не админ.');

$user = getUser();

use Rakit\Validation\Validator;

$validator = new Validator;

$validation = $validator->make($_POST, [
    'id' => 'required|integer'
]);

$validation->setAliases([
    'id' => 'ID ордера'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

$order = R::load('warrants', $_POST['id']);

if(!$order->id) {
    returnError('Ордера не существует.');
}

if($order->status != 0) {
    returnError('Статус этого ордера уже изменён.');
}

$order->status = 3;
$order->additional .= '<p class="text-danger">'. $user['name'] .' аннулировал ордер</p>';
R::store($order);

returnSuccess('Ордер аннулирован.');