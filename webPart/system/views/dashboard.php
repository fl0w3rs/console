<?php
require '_twig.php';

if($dept_type['link'] != dept_types[$params['department_type']]['link']) {
    header('Location: '. config['dir_prefix'] . dept_types[$params['department_type']]['link']);
}

switch($dept_type['link']) {
    case 'dashboard': { echo $twig->render('dashboard.twig', $params); break; }
    case 'mdt': {
        R::selectDatabase('cad');
        $params['bolo_p'] = R::getAssocRow('SELECT * FROM bolo_peoples');
        $params['bolo_v'] = R::getAssocRow('SELECT * FROM bolo_vehicle');

        $params['avail'] = getUnitByUid($params['user']['id']);
        $params['situations'] = getActiveSituations();

        $params['situations_attached'] = [];
        for($i = 0; $i < count($params['situations']); $i++) {
            $params['situations_attached'][$i] = getUnitsAttachedToSituation($params['situations'][$i]['id']);
            $params['situations_attached_info'][$i] = R::getRow("SELECT * FROM situations_attached WHERE unit_id = ? AND sit_id = ?", [$params['user']['id'], $params['situations'][$i]['id']]);
            $params['situation_is_attached'][$i] = isset($params['situations_attached_info'][$i]['id']);
        }

        $params['all_users'] = R::getAssocRow('SELECT id, name FROM users');

        echo $twig->render('mdt.twig', $params); break; }
    case 'cad': {
        R::selectDatabase('cad');
        $params['bolo_p'] = R::getAssocRow('SELECT * FROM bolo_peoples');
        $params['bolo_v'] = R::getAssocRow('SELECT * FROM bolo_vehicle');

        $params['avail'] = getUnitByUid($params['user']['id'], 2);
        $params['situations'] = getActiveSituations();
        $params['units'] = getAllUnits();
        for($i = 0; $i < count($params['units']); $i++) {
            $params['units_char'][$i] = R::getRow('SELECT * FROM users WHERE id = ?', [$params['units'][$i]['uid']]);
            $params['units_calls'][$i] = getSituationsAttachedToUnit($params['units'][$i]['uid']);
        }
        echo $twig->render('cad.twig', $params); break; }
    case 'fire': {
        $params['avail'] = getUnitByUid($params['user']['id']);
        $params['situations'] = getActiveSituations();
        echo $twig->render('fire.twig', $params); break; }
    case 'civil': {
        $params['characters'] = getUserCharacters($params['user']['fid']);
        for($i = 0; $i < count($params['characters']); $i++) {
            $params['characters_vehicles'][$i] = R::getAssocRow('SELECT * FROM characters_vehicles WHERE char_id = ?', [ $params['characters'][$i]['id'] ]);
            $params['characters_weapons'][$i] = R::getAssocRow('SELECT * FROM characters_weapons WHERE char_id = ?', [ $params['characters'][$i]['id'] ]);
            $params['characters_citations'][$i] = R::getAssocRow('SELECT * FROM citations WHERE char_id = ? ORDER BY id DESC', [ $params['characters'][$i]['id'] ]);
            foreach($params['characters_citations'][$i] as $k => &$v) {
                $v['expired'] = isCitationExpired($v);
            }
        }

        echo $twig->render('civil.twig', $params);
        break;
    }
}