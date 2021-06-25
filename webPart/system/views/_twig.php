<?php

$loader = new \Twig\Loader\FilesystemLoader(DIR_PREFIX.'system/views/_templates');
$twig = new \Twig\Environment($loader, [
    // 'cache' => DIR_PREFIX.'system/tpl_cache',
]);

$filter = new \Twig\TwigFilter('abbr', function ($string) {
    $tmp = '';
    foreach(explode(' ', $string) as $v) {
        $tmp .= $v[0];
    }
    return $tmp;
});
$twig->addFilter($filter);

$params = [];

$params['config'] = config;
$params['version'] = time();
// $params['version'] = '1.0';
$params['mdb_version'] = '4.19.2';

if(isAuthorized()) {
    $params['user'] = getUser();
    $params['department'] = getCurrentDepartment();
    $params['department_type'] = $params['department'] == 0 ? 0 : getDepartmentById($params['department'])->type;
    $params['avail_departments'] = getUserDepartments();
    $params['forum_user'] = getForumUser();
    $params['is_admin'] = isAdmin();
    $params['sex_types'] = sex_types;
    $params['races'] = races;
    $params['driving_license_types'] = driving_license_types;
    $params['gun_license_types'] = gun_license_types;
    $params['citation_reasons'] = citation_reasons;
    $params['arrest_reasons'] = arrest_reasons;

    R::selectDatabase('cad');

    $settings = R::getAssocRow('SELECT * FROM settings');
    $params['settings'] = [];
    for($i = 0; $i < count($settings); $i++) {
        $params['settings'][$settings[$i]['s_key']] = $settings[$i]['s_value'];
    }

    if($params['user']['name'] != 'fl0w3rs' && config['dev_mode'] && config['maintenance_on_dev_mode']) {
        returnError('Включен режим разработки.');
    }

    // if($params['user']['fid'] != 1) returnError('dev mode');
}