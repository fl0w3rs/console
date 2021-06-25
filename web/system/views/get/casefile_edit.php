<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['casefile'] = R::getRow('SELECT * FROM casefiles WHERE id = ?', [(int)$id]);

if(!isset($params['casefile']['id'])) {
    returnError('Кейс-файл не существует.');
}

$params['casefile']['description'] = htmlspecialchars_decode($params['casefile']['description']);
$params['casefile']['evidence'] = htmlspecialchars_decode($params['casefile']['evidence']);
$params['casefile']['witnesses'] = htmlspecialchars_decode($params['casefile']['witnesses']);

$params['access'] = haveUserAccessToCF($params['casefile']);
if(!$params['access']) {
    returnError('У Вас нету доступа к этому делу.');
}

$params['detectives'] = explode(',', $params['casefile']['detectives']);
$params['all_users'] = R::getAssocRow('SELECT id, name FROM users');

foreach($params['all_users'] as $k => &$v) {
    if(in_array(strval($v['id']), $params['detectives'])) {
        $v['selected'] = true;
    } else {
        $v['selected'] = false;
    }
}

$params['reasons'] = explode(',', $params['casefile']['reasons']);
$params['ar'] = arrest_reasons;
foreach($params['ar'] as $k => &$v) {
    $text = $v;
    $v = [];
    $v['text'] = $text;
    if(in_array(strval($k), $params['reasons'])) {
        $v['selected'] = true;
    } else {
        $v['selected'] = false;
    }
}

returnSuccess($twig->render('_components/casefile_edit.twig', $params));
