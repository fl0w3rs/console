<?php

R::selectDatabase('cad');
$row = R::getRow("SELECT * FROM settings WHERE s_key = 'gamezone'");

echo $row['s_api'].'|||'.$row['s_value'];

if((int)$row['s_api'] == 1) R::exec("UPDATE settings SET s_api = 0 WHERE s_key = 'gamezone'");