<?php

if(!isAuthorized()) returnError('Unauthorized.');

if(!isAdmin()) returnError('Вы не администратор.');

$data = checkParams(['id']);
$data['id'] = (int)$data['id'];

$dept = getDepartmentById($data['id']);
if($dept == NULL) returnError('Департамент не существует.');

R::hunt( 'departments', 'id = ?', [$data['id']] );

returnSuccess('Департамент удалён.');