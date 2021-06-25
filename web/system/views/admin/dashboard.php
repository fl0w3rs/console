<?php
require __DIR__ . '/../_twig.php';

if(!$params['is_admin']) {
    returnError('Нету админки');
}

echo $twig->render('admin/dashboard.twig', $params);