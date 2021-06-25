<?php

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

$data = checkParams(['new_dept']);

$curr_dept = (int)getCurrentDepartment();
$new_dept = (int)$data['new_dept'];

if($curr_dept != $new_dept) {
    R::selectDatabase('cad');
    R::exec('DELETE FROM units WHERE uid = ?', [$user['id']]);
    R::exec('DELETE FROM situations_attached WHERE uid = ?', [$user['id']]);
}

if($new_dept != 0 && !isDeptAvailForUser($new_dept)) {
    returnError('Этот департамент Вам недоступен.');
}

setCurrentDepartment($new_dept);

$new_dept_type = $new_dept == 0 ? '0' : getDepartmentById($new_dept)->type;
$link = dept_types[$new_dept_type]['link'];
returnSuccess(['link' => config['dir_name'] . $link]);
