<?php
require __DIR__ . '/../../_twig.php';

if(!$params['is_admin']) {
    returnError('Нету админки');
}

R::selectDatabase('forum');
$params['groups'] = R::getAssocRow("SELECT g.g_id, l.word_custom FROM core_groups g INNER JOIN core_sys_lang_words l ON l.word_key = CONCAT('core_group_', g.g_id) AND l.lang_id = ?", [config['forum_lang_id']]);
$params['fields'] = R::getAssocRow("SELECT f.pf_id, l.word_custom FROM core_pfields_data f INNER JOIN core_sys_lang_words l ON l.word_key = CONCAT('core_pfield_', f.pf_id) AND l.lang_id = ? WHERE pf_type = 'YesNo'", [config['forum_lang_id']]);

$params['types'] = dept_types;

echo $twig->render('admin/departments/new.twig', $params);