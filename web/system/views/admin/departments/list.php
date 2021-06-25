<?php
require __DIR__ . '/../../_twig.php';

if(!$params['is_admin']) {
    returnError('Нету админки');
}

$params['departments'] = getAllDepartments();

echo $twig->render('admin/departments/list.twig', $params);