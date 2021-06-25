<?php

if(!isAuthorized()) returnError('Unauthorized.');

require '_twig.php';

echo $twig->render('profile.twig', $params);