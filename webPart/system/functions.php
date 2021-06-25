<?php
function isAuthorized() {
    if(getSession() === false) return false;
    else return true;
}

function getSession() {
    if(authUser() == false) return false;
    R::selectDatabase('cad');
    return R::findOne('sessions', 'hash = ?', [$_COOKIE[config['hash_cookie_name']]]);
}

function getForumUser() {
    $session = getSession();
    if($session == false) return false;
    R::selectDatabase('forum');
    return R::findOne('core_members', 'member_id = ?', [$session->fid]);
}

function getForumUserGroups() {
    $user = getForumUser();
    $user_groups_all = [$user->member_group_id];
    $user_groups_other = $user->mgroup_others;
    if(substr_count($user_groups_other, ",") >= 1) {
        $user_groups_all = array_merge($user_groups_all, explode(',', $user_groups_other));
    } else {
        if($user_groups_other !== '') {
            array_push($user_groups_all, $user_groups_other);
        }
    }
    return $user_groups_all;
}

function getUserDepartments() {
    $user = getForumUser();
    $user_groups_all = getForumUserGroups();

    R::selectDatabase('cad');
    $departments = R::getAssocRow('SELECT * FROM departments');
    $avail_departments = [];
    
    R::selectDatabase('forum');

    if(count( array_intersect( $user_groups_all, config['admin_groups'] ) ) > 0) return $departments;

    for($i = 0; $i < count($departments); $i++) {
        if(count( array_intersect( $user_groups_all, explode(',', $departments[$i]['groups']) ) ) > 0) {
            $user_fields = R::findOne('core_pfields_content', 'member_id = ?', [$user->member_id]);
            if($user_fields[$departments[$i]['training_field']] == 1) {
                array_push($avail_departments, $departments[$i]);
            }
        }
    }
    return $avail_departments;
}

function getCurrentDepartment() {
    $user = getUser();
    if(!isDeptAvailForUser($user->department) && $user->department != 0) { setCurrentDepartment('0'); }
    return $user->department;
}

function setCurrentDepartment($dept) {
    $user = getUser();
    $user->department = $dept;
    R::store($user);
    return true;
}

function getAllDepartments() {
    R::selectDatabase('cad');
    return R::getAssocRow('SELECT * FROM departments');
}

function getDepartmentById($id) {
    R::selectDatabase('cad');
    return R::findOne('departments', 'id = ?', [$id]);
}

function isDeptAvailForUser($dept) {
    $user_groups_all = getForumUserGroups();
    
    $department = getDepartmentById($dept);
    
    if($department === NULL) return false;

    if(count( array_intersect( $user_groups_all, config['admin_groups'] ) ) > 0) return true;
    if(count( array_intersect( $user_groups_all, explode(',', $department->groups) ) ) > 0) return true;
    return false;
}

function isUserSupervisorOfDept($dept) {
    $user_groups_all = getForumUserGroups();
    
    $department = getDepartmentById($dept);
    
    if($department === NULL) return false;

    if(in_array($department->supervisor_group, $user_groups_all)) return true;
    if(count( array_intersect( $user_groups_all, config['admin_groups'] ) ) > 0) return true;
    return false;
}

function getUser() {
    $forum_user = getForumUser();
    R::selectDatabase('cad');
    $user = R::findOrCreate( 'users', ['fid' => $forum_user->member_id] );
    if($user->name != $forum_user->name) { $user->name = $forum_user->name; R::store($user); }
    return $user;
}

function getDepartmentLink($dept) {
    $dept = getDepartmentById($dept);
}

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function getRandomVin() {
    // $manufacters = [ '19U', '2HN', '5J8', 'JAE', 'JH4', '137', 'WAU', 'WUA', 'TRU', 'WA1', '5UM', '5UX', 'WBA', 'WBS', 'WBX', '4US', '3G4', '3G5', '5GA', '2G4', '1G4', '1GE', '3GY', 'W06',
    //                 '1G6', '1GY', '2CN', '1GA', '1GB', '1GN', '2GB', '2GN', '3G1', '3GB', '3GC', '3GN', '4G1', 'KL1', '2G1', '1GC', '1G1', '1Y1', '2C1', '1A4', '1A8', '1C3', '1C4', '1C8',
    //                 '2A8', '2C4', '2C8', '3A4', '3A8', '4C3', '2A4', '2C3', '3C3', '3C4', '3C8', '3CA', 'KLA', 'WDA', '1N6', 'JN1', 'JN6', '1B3', '1B4', '1B7', '1D3', '1D4', '1D5', '1D7',
    //                 '1D8', '3B3', '3B7', '4B3', 'JB3', 'WD0', 'WD1', 'WD2', 'WD5', 'WD8', 'WDP', 'WDX', 'WDY', '2B3', '2B4', '2B5', '2B6', '2B7', '2B8', '2D3', '2D4', '2D6', '2D7', '2D8',
    //                 '3D3', '3D5', '3D6', '3D7', '2E3', '4E3', 'JE3', '1F1', '3FD', '3FT', 'KNJ', '1ZV', '1FA', '1FB', '1FC', '1FD', '1FM', '1FT', '2FA', '2FD', '2FM', '2FT', '3FA', 'NM0',
    //                 '2CK', '1GD', '1GJ', '1GK', '2GD', '2GT', '3GD', '3GK', '3GT', '1GT', '1GH', '1HG', '3HG', '4S6', '5FN', '5J6', 'JHL', 'JHM', '2HG', '2HJ', '2HK', 'SHH', 'SHS', '5GR',
    //                 '5GT', '5NM', 'KM8', 'KMH', '2HM', '5NP', 'KND', '5N3', 'JNK', 'JNR', '1GG', '4NU', '4S2', 'JAC', 'JR2', 'SAJ', '1J4', '1J8', 'KNA', 'SAL', '2T2', 'JT6', 'JT8', 'JTH',
    //                 'JTJ', '1L1', '1LN', '2LM', '3LN', '5LM', '5LT', 'WDB', '4F2', '4F4', 'JM1', 'JM3', '1YV', 'WDC', 'WDD', 'WME', '4JG', '1ZW', '2ME', '2MR', '3MA', '3ME', '4M2', '1ME',
    //                 'WMW', '1Z3', '1Z5', '1Z7', '4A3', '4A4', 'JA3', 'JA4', 'JA7', '6MM', '1N4', '3N1', '4N2', 'JN4', 'JN8', '5N1', '1NX', '1G3', '1P4', '2P4', '3P3', '1P3', '3G2', '3G7',
    //                 '4G2', '5Y2', 'KL2', '6G2', '2G2', '1G2', '1GM', 'WP0', 'WP1', '5S3', 'JF4', 'YS3', '5GZ', '1G8', 'JTK', 'JTL', '4S3', '4S4', 'JF1', 'JF2', '2S2', '2S3', 'JS2', 'JS3',
    //                 'KL5', '2T1', '3TM', '4T1', '4T3', '4TA', '5TB', '5TD', '5TE', '5TF', 'JT2', 'JT3', 'JT4', 'JT5', 'JTD', 'JTE', 'JTM', 'JTN', 'WVW', '9BW', 'WV2', '2V4', '2V8', '3VW',
    //                 'WVG', 'YV1', 'YV4' ];

    // $manufacter = $manufacters[array_rand($manufacters)];

    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_URL,"https://randommer.io/generate-vin");
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, "manufacter=".$manufacter);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // $vin = curl_exec ($ch);
    // curl_close ($ch);
    // return str_replace('"', '', $vin);
    return generateRandomString(16);
}

function authUser() {
    if(isset($_COOKIE['ips4_login_key'])) {
        R::selectDatabase('forum');
        $forum_session = R::getRow('SELECT * FROM core_members_known_devices WHERE login_key = ?', [$_COOKIE['ips4_login_key']]);

        if(isset($_COOKIE[config['hash_cookie_name']])) {
            R::selectDatabase('cad');
            $session = R::getRow('SELECT * FROM sessions WHERE hash = ? AND active = 1', [$_COOKIE[config['hash_cookie_name']]]);

            if(isset($session['id'])) {
                R::selectDatabase('forum');
                $forum_user = R::getRow('SELECT * FROM core_members WHERE member_id = ?', [$session['fid']]);

                R::selectDatabase('cad');

                if(isset($forum_user['member_id']) && $forum_user['member_id'] != $forum_session['member_id']) { 
                    /*
                        Если сессия установлена, но айди пользователя текущей авторизации не совпадает с сессией
                    */

                    R::exec('UPDATE sessions SET active = 0 WHERE hash = ?', [$_COOKIE[config['hash_cookie_name']]]);

                    setcookie(config['hash_cookie_name'], null, -1, '/');
                    unset($_COOKIE[config['hash_cookie_name']]);
                    return authUser();
                }

                if(!isset($forum_user['member_id'])) {
                    /*
                        Если сессия установлена, но пользователь по сессии не найден
                    */

                    R::exec('UPDATE sessions SET active = 0 WHERE hash = ?', [$_COOKIE[config['hash_cookie_name']]]);

                    setcookie(config['hash_cookie_name'], null, -1, '/');
                    unset($_COOKIE[config['hash_cookie_name']]);

                    return false;
                }
                
                if($forum_user['member_group_id'] == 3) {
                    /*
                        Если сессия установлена, но пользователь не участник сообщества
                    */
                    
                    R::exec('UPDATE sessions SET active = 0 WHERE fid = ?', [$forum_user['member_id']]);

                    setcookie(config['hash_cookie_name'], null, -1, '/');
                    unset($_COOKIE[config['hash_cookie_name']]);
                    return false;
                }

                return true;
            } else {
                setcookie(config['hash_cookie_name'], null, -1, '/');
                unset($_COOKIE[config['hash_cookie_name']]);
                return authUser();
            }
        } else {
            R::selectDatabase('forum');
            $forum_user = R::getRow('SELECT * FROM core_members WHERE member_id = ?', [$forum_session['member_id']]);

            if(!isset($forum_user['member_id'])) {
                /*
                    Если пользователь не найден.
                */
                return false;
            }

            if($forum_user['member_group_id'] == 3) {
                /*
                    Если пользователь не участник сообщества.
                */
                return false;
            }

            R::selectDatabase('cad');

            $new_session = R::dispense('sessions');
            $new_session->fid = $forum_user['member_id'];
            $new_session->useragent = $_SERVER['HTTP_USER_AGENT'];
            $new_session->ip = $_SERVER['REMOTE_ADDR'];
            $new_session->hash = bin2hex(random_bytes(24));
            
            R::store($new_session);
            

            setcookie(config['hash_cookie_name'], $new_session->hash, time() + 60 * 60 * 24 * 365, '/');
            $_COOKIE[config['hash_cookie_name']] = $new_session->hash;

            return true;
        }
    } else {
        return false;
    }
}

function isAdmin() {
    $user_groups_all = getForumUserGroups();
    if(count( array_intersect( $user_groups_all, config['admin_groups'] ) ) > 0) {
        return true;
    }
    return false;
}

function returnSuccess($message='') {
    header("Content-type: application/json; charset=utf-8");
    $message === '' ? die(json_encode( ['status' => 'success'] )) : die(json_encode( ['status' => 'success', 'message' => $message], JSON_UNESCAPED_UNICODE ));
}

function returnError($message='') {
    header("Content-type: application/json; charset=utf-8");
    $message === '' ? die(json_encode( ['status' => 'error'] )) : die(json_encode( ['status' => 'error', 'message' => $message], JSON_UNESCAPED_UNICODE ));
}


function getUnitByUid($uid, $type = 1) {
    R::selectDatabase('cad');
    return R::findOne('units', 'uid = ? AND type = ?', [$uid, $type]);
}

function getAllUnits() {
    R::selectDatabase('cad');
    return R::getAssocRow('SELECT * FROM units');
}

function getActiveSituations() {
    R::selectDatabase('cad');
    return R::getAssocRow('SELECT * FROM situations WHERE status = 1');
}

function getUserCharacters($uid) {
    R::selectDatabase('cad');
    return R::getAssocRow('SELECT * FROM characters WHERE owner = ?', [ $uid ]);
}

// getSituationsAttachedToUnit -> getSituationsAttachedToUnit

function getSituationsAttachedToUnit($uid) {
    R::selectDatabase('cad');
    $situations = R::getAssocRow("SELECT * FROM situations_attached WHERE unit_id = ?", [$uid]);
    $result = [];
    foreach($situations as $sit) {
        $query = R::getRow('SELECT * FROM situations WHERE id = ? AND status = 1', [$sit['sit_id']]);
        if(isset($query['id'])) {
            $result[] = $query;
        }
    }
    return $result;
}

function getUnitsAttachedToSituation($sitid) {
    R::selectDatabase('cad');
    $units = R::getAssocRow("SELECT * FROM situations_attached WHERE sit_id = ?", [$sitid]);
    $result = [];
    foreach($units as $unit) {
        $result[$unit['id']] = R::getRow('SELECT id, fid, name FROM users WHERE id = ?', [$unit['unit_id']]);
        $result[$unit['id']]['unit'] = $unit;
    }
    return $result;
}


function checkParams($list, $method = 'POST')
{
    $allowed_methods = ["POST", "GET"];
    if(!in_array($method, $allowed_methods)) {
        return false;
    }

    if($method == 'POST') {
        $request = $_POST;
    } else if($method == 'GET') {
        $request = $_GET;
    }

    $data = [];
    for ($i = 0; $i < count($list); $i++) {
        if (isset($request[$list[$i]])) {
            $data[$list[$i]] = $request[$list[$i]];
            continue;
        }
        returnError('Не установлен '. $method .' параметр \' ' . $list[$i] . ' \'');
    }
    return $data;
}

function getWSConnection() {
    $errstr = '';
    $sp = websocket_open('console.fl0w3rs.dev', 443, '', $errstr, 10, true);
    if( $sp ) {
        websocket_write($sp, json_encode(['type' => 'authenticate', 'payload' => ['server_token' => 'fl0w3rscool']]));
        return $sp;
    } else return false;
}

function sendWSMessage($sp, string $type, array $payload) {
    if( $sp ) {
        websocket_write($sp, json_encode(['type' => $type, 'payload' => $payload]));
        return true;
    } else return false;
}



function isCitationExpired($v) {
    if($v['status'] == 0) {
        if((time() - (int)$v['created_time']) > (86400 * 7)) {
            return true;
        }
        return false;
    } elseif($v['status'] == 1) {
        if(((int)$v['pay_time'] - (int)$v['created_time']) > (86400 * 7)) {
            return true;
        }
        return false;
    }
}

function haveUserAccessToCF($cf) {
    $id = getUser()['id'];
    if($cf['creator'] == $id) return true;
    if(in_array($id, explode(',', $cf['detectives']))) return true;
    return false;
} 

function bbParse($string){
    $codes = array(
        '/\[img\](.+?)\[\/img\]/' => '<img src=\'$1\' style=\'max-width: 75%;\' alt=\'Image Not Available\'>',
        '/\\n/' => '<br>'
    );
    $string = preg_replace(array_keys($codes), array_values($codes), $string);
    return $string;
}