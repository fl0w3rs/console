<?php 

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

use Rakit\Validation\Validator;

$validator = new Validator;


$validation = $validator->make($_POST, [
    'neworder-type' => 'required|in:1,2|integer',
    'neworder-name' => 'required|min:3|max:128',
    'neworder-desc' => 'required|min:3|max:2048',
    'neworder-by' => 'required|min:3|max:128',
    'neworder-date' => 'required|date:d/m/Y'
]);

$validation->setMessages([
	'neworder-type:in' => 'Выберите предмет ордера',
	'neworder-date:regex' => 'Введите правильную дату',
]);

$validation->setAliases([
    'neworder-type' => 'Предмет ордера',
    'neworder-name' => 'Объект ордера',
    'neworder-desc' => 'Описание ордера',
    'neworder-by' => 'Кем будет выдан',
    'neworder-date' => 'Дата выдачи'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

$new = R::dispense('warrants');
$new->type = $_POST['neworder-type'];
$new->name = htmlspecialchars($_POST['neworder-name']);
$new->description = htmlspecialchars($_POST['neworder-desc']);
$new->issued_by = htmlspecialchars($_POST['neworder-by']);
$new->date = $_POST['neworder-date'];
R::store($new);

returnSuccess('Ордер создан.');