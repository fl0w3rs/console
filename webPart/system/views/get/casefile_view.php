<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['casefile'] = R::getRow('SELECT * FROM casefiles WHERE id = ?', [(int)$id]);

if(!isset($params['casefile']['id'])) {
    returnError('Кейс-файл не существует.');
}

$params['casefile']['description'] = bbParse($params['casefile']['description']);
$params['casefile']['evidence'] = bbParse($params['casefile']['evidence']);
$params['casefile']['witnesses'] = bbParse($params['casefile']['witnesses']);

$params['lead_detective'] = R::getRow('SELECT * FROM users WHERE id = ?', [$params['casefile']['creator']]);

$params['detectives'] = R::getAssocRow('SELECT * FROM users WHERE id IN ('. $params['casefile']['detectives'] .')');

$params['access'] = haveUserAccessToCF($params['casefile']);

$params['comments'] = R::getAssocRow('SELECT cc.*, u.name as creator_name FROM casefiles_comments cc INNER JOIN users u ON cc.creator = u.id WHERE cc.cf_id = ?', [$id]);
foreach($params['comments'] as $k => &$v) {
    $v['text'] = bbParse($v['text']);
}

returnSuccess($twig->render('_components/casefile_view.twig', $params));
