<?php

if(!isAuthorized()) returnError('Unauthorized.');

returnSuccess(getUserDepartments());