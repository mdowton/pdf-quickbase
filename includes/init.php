<?php
   
  function __autoload($name) {

   	 	include 'classes/'.$name . '.php';
    	//throw new Exception("Unable to load $name.");
	}

	try {

    define("APPTOKEN", "dy2hfu5csthvumdccc2uuc5qnuja");

    //get url 
    $appToken = htmlspecialchars($_GET['apptoken']);
    $recordID = htmlspecialchars($_GET['rid']);
    //match defined token with url token
    if(APPTOKEN != $appToken){
        echo 'Error not valid application token';
        die();
    } else {
      
      $info = array(
     	'Title' 				=> 'Mr',
     	'First Name' 		=> 'Mark',
     	'Last Name'				=> 'Dowton',
     	'Email'		=> 'mark@11atoms.com',
     	'Previous Name' => 'Clint Eastwood',
      'Male'          => 'Yes',
      'Date of Birth' => '20-09-76'
     	);

     //$info = array(); 
     $keysArray = array();
     $valuesArray = array();
     
     $file = './enrol-form-4.php';
      
     //connect to quickbase and pull info down
     //$quick  = new Quickbase();
     $quickBaseID = 'bigmp2r7u';
     $q = new Quickbase('dev@incling.com.au', '5bztVEf4NjkLMk', true, $quickBaseID, 'dy2hfu5csthvumdccc2uuc5qnuja', 'sae');
     $result = $q->get_record_info($recordID);
     //print_r($result);
  
    
    //$app = simplexml_load_file($result);
    echo '<h1>Results Array</h1>';
    for($i = 0, $j = count($result); $i < $j ; $i++){
        $keysArray[] = (string)$result->field[$i]->name;
        if ( (string)$result->field[$i]->type == 'Date' || (string)$result->field[$i]->type == 'Date / Time'){
          //TODO sample for date of birth here and split the string
          $valuesArray[] = (string)$result->field[$i]->printable;
        } else {
          $valuesArray[] = (string)$result->field[$i]->value;
        } 
    }
    

    $masterArray = array_combine($keysArray, $valuesArray);
    
    echo '<pre>';
    print_r($masterArray);
    echo '<pre>';
    
    

    $fdf = new FDF();
    $result = $fdf->createFDF($file,$masterArray);
   
   //write fdf data to fdf file
   $fp = fopen('file.fdf', 'w');
	 fwrite($fp, print_r($result, TRUE));
	 fclose($fp);

	 //execute pdftk server to merge data
		exec('/usr/local/bin/pdftk enrol-form-4.pdf fill_form file.fdf output form_with_data_test.pdf ',$output, $return);
		 if ($return != 0) echo 'error executing pdftk...shell command';
		//exit;
		echo 'process complete';

	  } //end else
	} catch (Exception $e) {
    echo $e->getMessage(), "\n";
	}

   //include 'classes/FDF.php';
?>