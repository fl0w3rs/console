<?php

if(!isAuthorized()) returnError('Unauthorized.');

require __DIR__.'/../_twig.php';

$params['characters'] = getUserCharacters($params['user']['fid']);
for($i = 0; $i < count($params['characters']); $i++) {
    $params['characters_vehicles'][$i] = R::getAssocRow('SELECT * FROM characters_vehicles WHERE char_id = ?', [ $params['characters'][$i]['id'] ]);
}

returnSuccess($twig->render('_components/civil/character_vehicles.twig', $params));