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
    $ids = array_column(getUserDepartments(), 'id');
    R::selectDatabase('cad');
    return in_array($dept, $ids);
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

function isLawEnforcement() {
    $allowed = isAdmin();
    if(!$allowed) {
        $dept = getDepartmentById(getCurrentDepartment());
        $allowed = $dept !== NULL && in_array((int)$dept->type, [1, 2]);
    }
    R::selectDatabase('cad');
    return $allowed;
}

function getUploadedImageExt($tmp_name) {
    $types = [IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg'];
    $info = @getimagesize($tmp_name);
    return $info ? ($types[$info[2]] ?? false) : false;
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
    $sp = websocket_open(config['ws_host'], config['ws_port'], '', $errstr, 10, config['ws_ssl']);
    if( $sp ) {
        websocket_write($sp, json_encode(['type' => 'authenticate', 'payload' => ['server_token' => config['ws_server_token']]]));
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
        '/\[img\](https?:\/\/[^\s\'"<>]+?)\[\/img\]/' => '<img src=\'$1\' style=\'max-width: 75%;\' alt=\'Image Not Available\'>',
        '/\\n/' => '<br>'
    );
    $string = preg_replace(array_keys($codes), array_values($codes), $string);
    return $string;
}
