<?php
	
	define("APPTOKEN", "dy2hfu5csthvumdccc2uuc5qnuja");
  define("USRNAME", "dev@incling.com.au");
  define("PASSWRD","5bztVEf4NjkLMk");
  define("QUICKBASEID","bigmp2r7u");
  define("URLINST", "sae");

  define("MASTERFORM", "master-enrol-form.pdf");


 //testing
 if( ( $_SERVER['HTTP_HOST'] == 'pdf.dev') ) { 
 		define('SS_ENVIRONMENT_TYPE', 'dev');
 }

 //live version
 if ( ( $_SERVER['HTTP_HOST'] == '8atoms.com') || $_SERVER['SERVER_ADDR'] == '103.29.84.15' ){
 		define('SS_ENVIRONMENT_TYPE', 'live');
 }
  

?>