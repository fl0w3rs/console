<?php
session_start();

require 'config.php';
require DIR_PREFIX.'vendor/autoload.php';

require 'database.php';

require 'libraries/Route.php';
require 'libraries/fngssn.php';
require 'libraries/websocket_client.php';

require 'functions.php';

// DEVELOPED BY FL0W3RS.DEV IN 2020 FOR SOUTHLANDPROJECT.RU