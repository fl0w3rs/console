<?php

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();
$user->muted_buttons = !$user->muted_buttons;
R::store($user);

returnSuccess($user->muted_buttons);