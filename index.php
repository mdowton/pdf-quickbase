<?php 
 /*** error reporting on ***/
 if ( SS_ENVIRONMENT_TYPE == 'dev' ) {
 		error_reporting(E_ALL);
 	} else {
		error_reporting(0);
	}
//error_reporting(E_ALL);
 /*** define the site path constant ***/
 $site_path = realpath(dirname(__FILE__));
 define ('__SITE_PATH', $site_path);

 /*** include the init.php file ***/
 include 'includes/init.php';

?>