<?php 

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

use Rakit\Validation\Validator;

$validator = new Validator;

$validation = $validator->make($_POST, [
    'id' => 'required|integer'
]);

$validation->setAliases([
    'id' => 'ID рапорта'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

$rb = R::load('report_blanks', $_POST['id']);

if(!$rb->id) {
    returnError('Рапорта не существует.');
}

if(!isUserSupervisorOfDept($rb->department)) {
    returnError('Вы не супервайзер этого департамента.');
}

if($rb->status != 1) {
    returnError('Статус этого рапорта уже изменён.');
}

$rb->status = 2;
$rb->additional .= '<p class="text-success">'. $user['name'] .' подписал рапорт</p>';
R::store($rb);

returnSuccess('Рапорт подписан.');