<?php 

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

use Rakit\Validation\Validator;

$validator = new Validator;

$validation = $validator->make($_POST, [
    'ec-id' => 'required|integer',
    'ec-name' => 'required|min:3|max:32',
    'ec-birth' => 'required|date:d/m/Y',
    'ec-sex' => 'required|integer|in:0,1',
    'ec-race' => 'required|integer|in:0,1,2,3,4,5',
    'ec-work' => 'present|max:256',
    'ec-address' => 'present|max:128',
    'ec-med-diseases' => 'present|max:256',
    'ec-med-allergy' => 'present|max:256',
    'ec-med-drugs' => 'present|max:256',
    'ec-med-contact' => 'present|max:256',
    'ec-med-weight' => 'present|max:65',
    'ec-med-height' => 'present|max:65',
    'ec-driving-license' => 'required|integer|in:0,1,2,3,4',
    'ec-gun-license' => 'required|integer|in:0,1,2,3,4',
    'ec-desc' => 'required|max:8192'
]);

$validation->setAliases([
    'ec-id' => 'ID персонажа',
    'ec-name' => 'Имя',
    'ec-birth' => 'Дата рождения',
    'ec-sex' => 'Пол',
    'ec-race' => 'Раса',
    'ec-work' => 'Работа',
    'ec-address' => 'Адрес проживания',
    'ec-med-diseases' => 'Болезни',
    'ec-med-allergy' => 'Аллергии',
    'ec-med-drugs' => 'Употребляемые препараты',
    'ec-med-contact' => 'Контакт при ЧС',
    'ec-med-weight' => 'Вес',
    'ec-med-height' => 'Рост',
    'ec-driving-license' => 'Лицензия на вождение',
    'ec-gun-license' => 'Лицензия на оружие',
    'ec-desc' => 'Описание персонажа'
]);

$validation->validate();

if ($validation->fails()) {
    $errors = $validation->errors();
    
    returnError(implode('<br>', $errors->firstOfAll()));
}

$char = R::findOne('characters', 'id = ? AND owner = ?', [$_POST['ec-id'], $user['fid']]);
if($char === null) {
    returnError('Персонаж не существует или не принадлежит Вам.');
}
$char->name = htmlspecialchars($_POST['ec-name']);
$char->birth = htmlspecialchars($_POST['ec-birth']);
$char->sex = $_POST['ec-sex'];
$char->race = $_POST['ec-race'];
$char->work = htmlspecialchars($_POST['ec-work']);
$char->address = htmlspecialchars($_POST['ec-address']);
$char->med_diseases = htmlspecialchars($_POST['ec-med-diseases']);
$char->med_allergy = htmlspecialchars($_POST['ec-med-allergy']);
$char->med_drugs = htmlspecialchars($_POST['ec-med-drugs']);
$char->med_contact = htmlspecialchars($_POST['ec-med-contact']);
$char->med_weight = htmlspecialchars($_POST['ec-med-weight']);
$char->med_height = htmlspecialchars($_POST['ec-med-height']);
$char->driving_license = $_POST['ec-driving-license'];
$char->gun_license = $_POST['ec-gun-license'];
$char->description = htmlspecialchars($_POST['ec-desc']);
R::store($char);

returnSuccess('Персонаж отредактирован.');