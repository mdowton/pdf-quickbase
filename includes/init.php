<?php
   
  include_once 'config/authenticate.php';

  function __autoload($name) {

   	 	include 'classes/'.$name . '.php';
    	//throw new Exception("Unable to load $name.");
	}

	try {


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
     
     //$file = './master-enrol-form.pdf';
      
     //connect to quickbase and pull info down
     $q = new Quickbase(USRNAME, PASSWRD, true, QUICKBASEID, APPTOKEN, URLINST);
     $result = $q->get_record_info($recordID);
     //print_r($result);
  
    
    //create a file name to save pdf to First Name - Last Name 
    $fileOut = "enrol_form_";
     for($i = 0, $j = count($result); $i < $j ; $i++) {
      
      if( (string)$result->field[$i]->name == 'First Name' || (string)$result->field[$i]->name == 'Last Name'){
        $fileOut .= (string)$result->field[$i]->value;
      }
     }
    $fileOut .= '.pdf';

    //$app = simplexml_load_file($result);
    echo '<h1>Procesing Document...</h1>';
    for($i = 0, $j = count($result); $i < $j ; $i++){
         //campus rule
        if( (string)$result->field[$i]->name == 'Campus' ) {
          echo "Executing campus rule set...<br />";
          if( (string)$result->field[$i]->value == 'Byron Bay'){
            $keysArray[] = 'Byron Bay';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Brisbane'){
            $keysArray[] = 'Brisbane';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Sydney'){
            $keysArray[] = 'Sydney';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Melbourne'){
            $keysArray[] = 'Melbourne';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Adelaide'){
            $keysArray[] = 'Adelaide';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Perth'){
            $keysArray[] = 'Online';
            $valuesArray[] = 'X';  
          }
          
        } 
        //date rules
        elseif ( (string)$result->field[$i]->type == 'Date' || (string)$result->field[$i]->type == 'Date / Time'){
            echo "Executing Date rule set...<br/>";
            if ( (string)$result->field[$i]->name == 'Date of Birth'){
              $dateString = explode("-",(string)$result->field[$i]->printable);
              $keysArray[] = 'dob-day';
              $valuesArray[] = $dateString[0];
              $keysArray[] = 'dob-month';
              $valuesArray[] = $dateString[1];
              $keysArray[] = 'dob-year';
              $valuesArray[] = $dateString[2];
            }
            //$keysArray[] = (string)$result->field[$i]->name;
            //$valuesArray[] = (string)$result->field[$i]->printable;
        }

        //higest eduaction rules
        elseif ( (string)$result->field[$i]->name == 'Highest Education') {
          echo "Executing highest education rule set...</br />";
          
          if( (string)$result->field[$i]->value == 'Still completing secondary school'){
            $keysArray[] = 'Still completing secondary school';
            $valuesArray[] = 'X';  
          }          
          if( (string)$result->field[$i]->value == 'Year 12/Senior high school certificate'){
            $keysArray[] = 'Y12';
            $valuesArray[] = 'X';  
          } 
          if( (string)$result->field[$i]->value == 'Certificate I/II/III/IV'){
            $keysArray[] = 'Other';
            $valuesArray[] = 'X';  
          } 
          if( (string)$result->field[$i]->value == 'Diploma or Associate Diploma'){
            $keysArray[] = 'Other';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Advanced Diploma or Associate Degree'){
            $keysArray[] = 'Other';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Bachelor Degree'){
            $keysArray[] = 'BA';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Postgraduate qualification'){
            $keysArray[] = 'Post grad';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Did not complete senior high school certificate'){
            $keysArray[] = 'Did not complete senior high school certificate';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Other qualification or work experience'){
            $keysArray[] = 'Other qualification or work experience';
            $valuesArray[] = 'X';  
          }

        } elseif ( (string)$result->field[$i]->name == 'Gender') {
          if( (string)$result->field[$i]->value == 'Male'){
            $keysArray[] = 'Male';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field->value == 'Female'){
            $keysArray[] = 'Female';
            $valuesArray[] = 'X';  
          }


        }
 
        else {
          //default functionality
          $keysArray[] = (string)$result->field[$i]->name;
          $valuesArray[] = (string)$result->field[$i]->value;
        } 
    }
    

    

    $masterArray = array_combine($keysArray, $valuesArray);
    
    echo '<pre>';
    print_r($masterArray);
    echo '<pre>';
    
    

    $fdf = new FDF();
    $result = $fdf->createFDF(MASTERFORM,$masterArray);
   
   //write fdf data to fdf file
   $fp = fopen('file.fdf', 'w');
	 fwrite($fp, print_r($result, TRUE));
	 fclose($fp);



	 //execute pdftk server to merge data
		exec('/usr/local/bin/pdftk '.MASTERFORM.' fill_form file.fdf output '.$fileOut.' ',$output, $return);
		 if ($return != 0) echo 'error executing pdftk...shell command';
		//exit;

     //encode file and send back to server attchment
    $data = file_get_contents($fileOut);
    $data = base64_encode($data);
    
    $fileUpdate[] = array(  'value' => $data,
                            'fid' => '58',
                            'filename' => $fileOut
                          );


    $uploadResult = $q->upload_file($recordID, NULL,$fileUpdate);
    print_r($uploadResult);
		echo 'process complete';

	  } //end else
	} catch (Exception $e) {
    echo $e->getMessage(), "\n";
	}

   //include 'classes/FDF.php';
?>