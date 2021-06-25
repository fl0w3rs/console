<?php
require 'libraries/rb-mysql.php';

R::addDatabase( 'forum', 'mysql:host='. config["forum_db_host"] .';dbname='. config["forum_db_name"], config["forum_db_user"], config["forum_db_password"] );
R::selectDatabase('forum');
if(!R::testConnection()) {
    die("Не удалось подключиться к базе данных форума.");
}

R::addDatabase( 'cad', 'mysql:host='. config["cad_db_host"] .';dbname='. config["cad_db_name"], config["cad_db_user"], config["cad_db_password"] );
R::selectDatabase('cad');
if(!R::testConnection()) {
    die("Не удалось подключиться к базе данных CAD.");
}

R::ext('xdispense', function( $type ){ 
    return R::getRedBean()->dispense( $type ); 
});