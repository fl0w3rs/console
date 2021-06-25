<?php 

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

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

if($citation->status != 0) {
    returnError('Этот штраф уже оплачен.');
}

$citation->status = 1;
$citation->pay_time = time();
R::store($citation);

returnSuccess('Штраф оплачен.');