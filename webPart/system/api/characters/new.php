<?php 

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

$data = checkParams(['newchar-name', 'newchar-birth', 'newchar-sex', 'newchar-race', 'newchar-driving-license', 'newchar-gun-license', 'newchar-address',
                    'newchar-work', 'newchar-desc', 'newchar-med-diseases', 'newchar-med-allergy', 'newchar-med-drugs', 'newchar-med-contact',
                    'newchar-med-height', 'newchar-med-weight']);

if(mb_strlen($data['newchar-name'], 'UTF-8') < 3 || mb_strlen($data['newchar-name'], 'UTF-8') > 32) {
    returnError('Поле "Имя и Фамилия" должно содержать от 3 до 32 символов.');
}

$birth_match_result = preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $data['newchar-birth'], $birth_matches);
if(!$birth_match_result) {
    returnError('Введите дату рождения в виде ДД/ММ/ГГГГ');
} else {
    if((int)$birth_matches[1] < 1 || (int)$birth_matches[1] > 31) {
        returnError('День рождения должен быть от 1 до 31.');
    }
    if((int)$birth_matches[2] < 1 || (int)$birth_matches[2] > 12) {
        returnError('Месяц рождения должен быть от 1 до 12.');
    }
    if((int)$birth_matches[3] < 1900 || (int)$birth_matches[2] > 2020) {
        returnError('Год рождения должен быть от 1900 до 2020.');
    }
}

if(array_key_exists($data['newchar-sex'], sex_types) === false) {
    returnError('Укажите пол.');
}

if(array_key_exists($data['newchar-race'], races) === false) {
    returnError('Укажите расу.');
}

if(array_key_exists($data['newchar-driving-license'], driving_license_types) === false) {
    returnError('Укажите водительскую лицензию.');
}

if(array_key_exists($data['newchar-gun-license'], gun_license_types) === false) {
    returnError('Укажите лицензию на оружие.');
}

R::selectDatabase('cad');

// rams(['newchar-name', 'newchar-birth', 'newchar-sex', 'newchar-race', 'newchar-driving-license', 'newchar-gun-license', 'newchar-address',
//                     'newchar-work', 'newchar-desc', 'newchar-med-diseases', 'newchar-med-allergy', 'newchar-med-drugs', 'newchar-med-contact',
//                     'newchar-med-height', 'newchar-med-weight']);

$fngssn = new fngssn();

$char = R::dispense('characters');
$char->owner = $user['fid'];
$char->name = $data['newchar-name'];
$char->birth = $data['newchar-birth'];
$char->sex = $data['newchar-sex'];
$char->race = $data['newchar-race'];
$char->driving_license = $data['newchar-driving-license'];
$char->gun_license = $data['newchar-gun-license'];
$char->address = $data['newchar-address'];
$char->work = $data['newchar-work'];
$char->description = $data['newchar-desc'];
$char->ssn = $fngssn->generateSSN('CA');
$char->med_diseases = $data['newchar-med-diseases'];
$char->med_allergy = $data['newchar-med-allergy'];
$char->med_drugs = $data['newchar-med-drugs'];
$char->med_contact = $data['newchar-med-contact'];
$char->med_height = $data['newchar-med-height'];
$char->med_weight = $data['newchar-med-weight'];
$id = R::store($char);

returnSuccess(['charid' => $id, 'message' => 'Персонаж создан.']);