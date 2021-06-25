<?php

if(!isAuthorized()) returnError('Unauthorized.');

$data = checkParams(['text']);
$user = getUser();

if(strcmp($data['text'], $user->notebook) !== 0) {
    $user->notebook = $data['text'];
    R::store($user);
}

returnSuccess();