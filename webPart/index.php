<?php
require 'system/main.php';

// error_reporting(E_ALL);
// ini_set('display_errors', 'On');

Route::add('/', function() {
    require DIR_PREFIX.'system/views/login.php';
}, 'get');

for($i = 0; $i < count(dept_types); $i++) {
    Route::add('/' . dept_types[$i]['link'], function() use ($i) {
        $dept_type = dept_types[$i];
        require DIR_PREFIX.'system/views/dashboard.php';
    }, 'get');
}

Route::add('/profile', function() {
    require DIR_PREFIX.'system/views/profile.php';
}, 'get');

Route::add('/get/disp_list', function() {
    require DIR_PREFIX.'system/views/get/displist.php';
}, 'get');
Route::add('/get/officer_list', function() {
    require DIR_PREFIX.'system/views/get/officer_list.php';
}, 'get');

Route::add('/get/arrest/new', function() {
    require DIR_PREFIX.'system/views/get/new_arrest.php';
}, 'get');
Route::add('/get/citation/new', function() {
    require DIR_PREFIX.'system/views/get/new_citation.php';
}, 'get');
Route::add('/get/situations', function() {
    require DIR_PREFIX.'system/views/get/situations.php';
}, 'get');
Route::add('/get/units', function() {
    require DIR_PREFIX.'system/views/get/units.php';
}, 'get');
Route::add('/get/status', function() {
    require DIR_PREFIX.'system/views/get/status.php';
}, 'get');
Route::add('/get/status_disp', function() {
    require DIR_PREFIX.'system/views/get/status_disp.php';
}, 'get');
Route::add('/get/situation/info', function() {
    require DIR_PREFIX.'system/views/get/situation_info.php';
}, 'get');
Route::add('/get/situation/attach', function() {
    require DIR_PREFIX.'system/views/get/situation_attach.php';
}, 'get');
Route::add('/get/characters', function() {
    require DIR_PREFIX.'system/views/get/characters.php';
}, 'get');
Route::add('/get/characters/edit', function() {
    require DIR_PREFIX.'system/views/get/characters_edit.php';
}, 'get');
Route::add('/get/characters/weapons', function() {
    require DIR_PREFIX.'system/views/get/characters_weapons.php';
}, 'get');
Route::add('/get/characters/weapons/edit', function() {
    require DIR_PREFIX.'system/views/get/characters_weapons_edit.php';
}, 'get');
Route::add('/get/characters/vehicles', function() {
    require DIR_PREFIX.'system/views/get/characters_vehicles.php';
}, 'get');
Route::add('/get/characters/citations', function() {
    require DIR_PREFIX.'system/views/get/characters_citations.php';
}, 'get');
Route::add('/get/orderlist', function() {
    require DIR_PREFIX.'system/views/get/order_list.php';
}, 'get');
Route::add('/get/bolo', function() {
    require DIR_PREFIX.'system/views/get/bolo.php';
}, 'get');
Route::add('/get/reportblanks', function() {
    require DIR_PREFIX.'system/views/get/report_blanks.php';
}, 'get');

Route::add('/get/casefile/list', function() {
    require DIR_PREFIX.'system/views/get/casefile_list.php';
}, 'get');
Route::add('/get/casefile/([0-9]*)', function($id) {
    require DIR_PREFIX.'system/views/get/casefile_view.php';
}, 'get');
Route::add('/get/casefile/([0-9]*)/edit', function($id) {
    require DIR_PREFIX.'system/views/get/casefile_edit.php';
}, 'get');

Route::add('/admin/dashboard', function() {
    require DIR_PREFIX.'system/views/admin/dashboard.php';
}, 'get');
Route::add('/admin/departments', function() {
    require DIR_PREFIX.'system/views/admin/departments/list.php';
}, 'get');
Route::add('/admin/departments/new', function() {
    require DIR_PREFIX.'system/views/admin/departments/new.php';
}, 'get');
Route::add('/admin/departments/([0-9]*)', function($id) {
    require DIR_PREFIX.'system/views/admin/departments/view.php';
}, 'get');

Route::add('/api/auth', function() {
    require DIR_PREFIX.'system/api/auth.php';
}, 'post');
Route::add('/api/user/departments', function() {
    require DIR_PREFIX.'system/api/user/departments.php';
}, 'get');
Route::add('/api/user/characters', function() {
    require DIR_PREFIX.'system/api/user/characters.php';
}, 'get');
Route::add('/api/user/select_dept', function() {
    require DIR_PREFIX.'system/api/user/select_dept.php';
}, 'post');
Route::add('/api/user/wallpaper', function() {
    require DIR_PREFIX.'system/api/user/wallpaper.php';
}, 'post');
Route::add('/api/user/officername', function() {
    require DIR_PREFIX.'system/api/user/officername.php';
}, 'post');
Route::add('/api/user/profile', function() {
    require DIR_PREFIX.'system/api/user/profile.php';
}, 'post');
Route::add('/api/user/mute', function() {
    require DIR_PREFIX.'system/api/user/mute.php';
}, 'post');
Route::add('/api/user/notebook', function() {
    require DIR_PREFIX.'system/api/user/notebook.php';
}, 'post');

Route::add('/api/warrants/new', function() {
    require DIR_PREFIX.'system/api/warrants/new.php';
}, 'post');
Route::add('/api/warrants/sign', function() {
    require DIR_PREFIX.'system/api/warrants/sign.php';
}, 'post');
Route::add('/api/warrants/execute', function() {
    require DIR_PREFIX.'system/api/warrants/execute.php';
}, 'post');
Route::add('/api/warrants/delete', function() {
    require DIR_PREFIX.'system/api/warrants/delete.php';
}, 'post');
Route::add('/api/warrants/cancel', function() {
    require DIR_PREFIX.'system/api/warrants/cancel.php';
}, 'post');

Route::add('/api/citations/new', function() {
    require DIR_PREFIX.'system/api/citations/new.php';
}, 'post');
Route::add('/api/arrests/new', function() {
    require DIR_PREFIX.'system/api/arrests/new.php';
}, 'post');

Route::add('/api/casefiles/new', function() {
    require DIR_PREFIX.'system/api/casefiles/new.php';
}, 'post');
Route::add('/api/casefiles/comment', function() {
    require DIR_PREFIX.'system/api/casefiles/comment.php';
}, 'post');
Route::add('/api/casefiles/edit', function() {
    require DIR_PREFIX.'system/api/casefiles/edit.php';
}, 'post');

Route::add('/api/ncic', function() {
    require DIR_PREFIX.'system/views/get/ncic.php';
}, 'post');
Route::add('/api/fld', function() {
    require DIR_PREFIX.'system/views/get/fld.php';
}, 'post');
Route::add('/api/dmv', function() {
    require DIR_PREFIX.'system/views/get/dmv.php';
}, 'post');

Route::add('/api/citations/new', function() {
    require DIR_PREFIX.'system/api/citations/new.php';
}, 'post');
Route::add('/api/citations/pay', function() {
    require DIR_PREFIX.'system/api/citations/pay.php';
}, 'post');

Route::add('/api/characters/new', function() {
    require DIR_PREFIX.'system/api/characters/new.php';
}, 'post');
Route::add('/api/characters/delete', function() {
    require DIR_PREFIX.'system/api/characters/delete.php';
}, 'post');
Route::add('/api/characters/edit', function() {
    require DIR_PREFIX.'system/api/characters/edit.php';
}, 'post');

Route::add('/api/characters/([0-9]*)/photo', function($charid) {
    require DIR_PREFIX.'system/api/characters/photo.php';
}, 'post');
Route::add('/api/characters/weapon/new', function() {
    require DIR_PREFIX.'system/api/characters/weapons/new.php';
}, 'post');
Route::add('/api/characters/weapon/delete', function() {
    require DIR_PREFIX.'system/api/characters/weapons/delete.php';
}, 'post');
Route::add('/api/characters/weapon/edit', function() {
    require DIR_PREFIX.'system/api/characters/weapons/edit.php';
}, 'post');
Route::add('/api/characters/vehicle/new', function() {
    require DIR_PREFIX.'system/api/characters/vehicles/new.php';
}, 'post');
Route::add('/api/characters/vehicle/delete', function() {
    require DIR_PREFIX.'system/api/characters/vehicles/delete.php';
}, 'post');

Route::add('/api/admin/departments/delete', function() {
    require DIR_PREFIX.'system/api/admin/departments/delete.php';
}, 'post');
Route::add('/api/admin/departments/edit', function() {
    require DIR_PREFIX.'system/api/admin/departments/edit.php';
}, 'post');
Route::add('/api/admin/departments/create', function() {
    require DIR_PREFIX.'system/api/admin/departments/create.php';
}, 'post');

Route::add('/api/reportblanks/new', function() {
    require DIR_PREFIX.'system/api/reportblanks/new.php';
}, 'post');
Route::add('/api/reportblanks/sign', function() {
    require DIR_PREFIX.'system/api/reportblanks/sign.php';
}, 'post');
Route::add('/api/reportblanks/decline', function() {
    require DIR_PREFIX.'system/api/reportblanks/decline.php';
}, 'post');
Route::add('/api/reportblanks/archive', function() {
    require DIR_PREFIX.'system/api/reportblanks/archive.php';
}, 'post');

Route::add('/api/server/aop', function() {
    require DIR_PREFIX.'system/api/server/aop.php';
}, 'get');
Route::add('/api/server/sto', function() {
    require DIR_PREFIX.'system/api/server/sto.php';
}, 'get');

Route::run(config['dir_prefix']);