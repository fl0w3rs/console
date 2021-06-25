<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['characters'] = getUserCharacters($params['user']['fid']);
for($i = 0; $i < count($params['characters']); $i++) {
    $params['characters_citations'][$i] = R::getAssocRow('SELECT * FROM citations WHERE char_id = ? ORDER BY id DESC', [ $params['characters'][$i]['id'] ]);
    foreach($params['characters_citations'][$i] as $k => &$v) {
        $v['expired'] = isCitationExpired($v);
    }
}

returnSuccess($twig->render('_components/civil/character_citations.twig', $params));