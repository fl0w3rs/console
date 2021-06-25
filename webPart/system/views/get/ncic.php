<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$data = checkParams(['name']);

R::selectDatabase('cad');
$find = R::getAssocRow('SELECT * FROM characters WHERE ssn = ? OR name LIKE CONCAT(\'%\', ?, \'%\')', [$data['name'], $data['name']]);
if(count($find) === 0) {
    returnError('Не найдено.');
}

$params['ncic_chars'] = $find;
if(count($params['ncic_chars']) == 1) {
    for($i = 0; $i < count($params['ncic_chars']); $i++) {
        $params['ncic_chars_weapons'][$i] = R::getAssocRow('SELECT * FROM characters_weapons WHERE char_id = ?', [$params['ncic_chars'][$i]['id']]);
        $params['ncic_chars_vehicles'][$i] = R::getAssocRow('SELECT * FROM characters_vehicles WHERE char_id = ?', [$params['ncic_chars'][$i]['id']]);
        $params['ncic_chars_citations'][$i] = R::getAssocRow('SELECT cit.*, u.name as issuer_name FROM citations cit INNER JOIN users u ON u.id = cit.creator WHERE cit.char_id = ? ORDER BY cit.id DESC', [$params['ncic_chars'][$i]['id']]);
        foreach($params['ncic_chars_citations'][$i] as $k => &$v) {
            $v['expired'] = isCitationExpired($v);
        }
        // var_dump($params['ncic_chars_citations'][$i]);
        $params['ncic_chars_arrests'][$i] = R::getAssocRow('SELECT arr.*, u.name as issuer_name FROM arrests arr INNER JOIN users u ON u.id = arr.creator WHERE arr.char_id = ?', [$params['ncic_chars'][$i]['id']]);
    }
}

returnSuccess($twig->render('_components/ncic.twig', $params));