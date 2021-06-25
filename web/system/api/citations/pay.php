<?php 


if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

use Rakit\Validation\Validator;

$validator = new Validator;

$validation = $validator->make($_POST, [
    'id' => 'required|integer'
]);

$validation->setAliases([
    'id' => 'ID штрафа'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

$citation = R::load('citations', $_POST['id']);

if(!$citation->id) {
    returnError('Штрафа не существует.');
}

$char = R::getRow('SELECT owner FROM characters WHERE id = ?', [$citation->char_id]);
if(!isset($char['owner']) || $char['owner'] != $user['fid']) {
    returnError('Это не ваш штраф.');
}

if($citation->status != 0) {
    returnError('Этот штраф уже оплачен.');
}

$citation->status = 1;
$citation->pay_time = time();
R::store($citation);

returnSuccess('Штраф оплачен.');