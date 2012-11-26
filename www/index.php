<?php

/**
*** Logiciel E-Pick ***
*** Read license joint in text file for more information ***
*** Copyright © E-Pick ***
***/

if(file_exists('../application/config/bootstrap.php'))
	require_once '../application/config/bootstrap.php';
else
	die('The bootstrap is missing, please reinstall.');

 
//run the application
Router::run();
?>