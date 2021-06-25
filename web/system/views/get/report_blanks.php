<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

R::selectDatabase('cad');
$params['reportblanks'] = R::getAssocRow("SELECT rb.*, d.name as dept_name FROM report_blanks rb LEFT JOIN departments d ON rb.department = d.id WHERE status <> -1 ORDER BY id DESC");
foreach($params['reportblanks'] as $k => &$v) {
    $v['is_sv'] = isUserSupervisorOfDept($v['department']);
}

returnSuccess($twig->render('_components/report_blanks.twig', $params));