<?php 

if(!isAuthorized()) returnError('Unauthorized.');

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

if($order->status != 1) {
    returnError('Этот ордер не подписан или уже исполнен/аннулирован.');
}

$order->status = 2;
$order->additional .= '<p class="text-success">'. $user['name'] .' исполнил ордер</p>';
R::store($order);

returnSuccess('Ордер исполнен.');