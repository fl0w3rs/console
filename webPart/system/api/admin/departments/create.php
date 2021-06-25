<?php

if(!isAuthorized()) returnError('Unauthorized.');

if(!isAdmin()) returnError('Вы не администратор.');

$data = checkParams(['name', 'groups', 'training', 'type', 'sv_group']);
$data['type'] = (int)$data['type'];

if(mb_strlen($data['name'], 'UTF-8') < 3 || mb_strlen($data['name'], 'UTF-8') > 32) {
    returnError('Название должно содержать от 3 до 32 символов.');
}

if(count($data['groups']) < 1) {
    returnError('У департамента должна быть как минимум одна группа.');
}

if(strpos($data['training'], 'field_') === false || $data['training'] === null) {
    returnError('Выберите поле тренировки.');
}

if(!is_numeric($data['sv_group'])) {
    returnError('Выберите правильную группу супервайзера.');
}

if(!array_key_exists($data['type'], dept_types)) {
    returnError('Выберите правильный тип департамента.');
}

R::selectDatabase('cad');
$dept = R::dispense('departments');
$dept->name = $data['name'];
$dept->groups = implode(',', $data['groups']);
$dept->training_field = $data['training'];
$dept->type = $data['type'];
$dept->supervisor_group = $data['sv_group'];
R::store($dept);

returnSuccess('Департамент создан.');