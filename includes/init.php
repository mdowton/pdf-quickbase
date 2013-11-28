<?php
   
  function __autoload($name) {

   	 	include 'classes/'.$name . '.php';
    	//throw new Exception("Unable to load $name.");
	}

	try {
    
    $info = array(
   	'Title' 				=> 'Mr',
   	'First name' 		=> 'Mark',
   	'Surname'				=> 'Dowton',
   	'Given name'		=> 'John',
   	'Previous name' => 'Clint Eastwood'

   	);
   $file = './enrol-form.php';

   //connect to quickbase and pull info down
   //$quick  = new Quickbase();
   $quickBaseID = 'bigmp2r7e?a=td';
   $q = new Quickbase('dev@incling.com.au', '5bztVEf4NjkLMk', true, $quickBaseID, 'dy2hfu5csthvumdccc2uuc5qnuja', 'sae');
   print_r($q);
   $fdf = new FDF();
   $result = $fdf->createFDF($file,$info);
   
   //write fdf data to fdf file
   $fp = fopen('file.fdf', 'w');
	 fwrite($fp, print_r($result, TRUE));
	 fclose($fp);

	 //execute pdftk server to merge data
		exec('/usr/local/bin/pdftk enrol-form.pdf fill_form file.fdf output form_with_data_test.pdf ',$output, $return);
		 if ($return != 0) echo 'error executing pdftk...shell command';
		//exit;
		echo 'process complete';

	
	} catch (Exception $e) {
    echo $e->getMessage(), "\n";
	}

   //include 'classes/FDF.php';
?>