<?php

$result = authUser();
if($result == true) {
    setCurrentDepartment(0);
    returnSuccess();
} else {
    returnError();
}