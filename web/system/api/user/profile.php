<?php

if(!isAuthorized()) returnError('Unauthorized.');

$data = checkParams(['steamid']);

$error = [];
$success = [];

$user = getUser();

if($user->steam_id != $data['steamid'] && $user->steam_id != 'steam:' . dechex($data['steamid'])) {
    if(strlen($data['steamid']) != 0) {
        try {
            $steam = new SteamID($data['steamid']);
            if($steam->isValid()) {
                $user->steam_id = 'steam:'.dechex($steam->ConvertToUInt64());
                $success[] = 'steamid (изменён)';
            } else $error[] = 'steamid';
        } catch ( InvalidArgumentException $e) { $error[] = str_replace('.', '', $e->getMessage()); }
    } else {
        $user->steam_id = '';
        $success[] = 'steamid (удалён)';
    }
}

$success_count = count($success);
$error_count = count($error);

if($success_count > 0) R::store($user);

if($success_count > 0 && $error_count == 0) returnSuccess('Успешно изменено: '. implode(',', $success) .'.');
else if($success_count == 0 && $error_count > 0) returnError('Ничего не изменено. Ошибки: ' . implode(',', $error) . '.');
else if($success_count > 0 && $error_count > 0) returnError('Успешно изменено: ' . implode(',', $success) . '. Ошибки: ' . implode(',', $error) . '.');
else if($success_count == 0 && $error_count == 0) returnError('Ничего не изменено.');