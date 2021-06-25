<?php

if(!isAuthorized()) returnError('Unauthorized.');

$user = getUser();

R::selectDatabase('cad');
$chars = R::getAssocRow('SELECT * FROM characters WHERE owner = ?', [$user['fid']]);

returnSuccess($chars);