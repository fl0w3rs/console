<?php
session_start();

require 'config.php';
require DIR_PREFIX.'vendor/autoload.php';

require 'database.php';

require 'libraries/Route.php';
require 'libraries/fngssn.php';
require 'libraries/websocket_client.php';

require 'functions.php';
