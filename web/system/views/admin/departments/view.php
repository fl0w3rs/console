<?php
require __DIR__ . '/../../_twig.php';

if(!$params['is_admin']) {
    returnError('Нету админки');
}

$params['department'] = getDepartmentById($id);
if($params['department'] == NULL) {
    returnError('Департамент не найден.');
}

$dept_groups = explode(',', $params['department']['groups']);

R::selectDatabase('forum');
$params['groups'] = R::getAssocRow("SELECT g.g_id, l.word_custom FROM core_groups g INNER JOIN core_sys_lang_words l ON l.word_key = CONCAT('core_group_', g.g_id) AND l.lang_id = ?", [config['forum_lang_id']]);

$params['slctd_groups_cnt'] = 0;
for($i = 0; $i < count($params['groups']); $i++) {
    if(in_array($params['groups'][$i]['g_id'], $dept_groups)) {
        $params['groups'][$i]['selected'] = true;
        $params['slctd_groups_cnt']++;
    }
}

$params['fields'] = R::getAssocRow("SELECT f.pf_id, l.word_custom FROM core_pfields_data f INNER JOIN core_sys_lang_words l ON l.word_key = CONCAT('core_pfield_', f.pf_id) AND l.lang_id = ? WHERE pf_type = 'YesNo'", [config['forum_lang_id']]);
$params['training_field'] = str_replace('field_', '', $params['department']['training_field']);

$params['types'] = dept_types;

echo $twig->render('admin/departments/view.twig', $params);