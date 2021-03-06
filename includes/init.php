<?php
   
  include_once 'config/authenticate.php';

  function __autoload($name) {

   	 	include 'classes/'.$name . '.php';
	}

	try {


    //get url 
    $appToken = htmlspecialchars($_GET['apptoken']);
    $recordID = htmlspecialchars($_GET['rid']);
    
    //match defined token with url token
    if(APPTOKEN != $appToken || empty($recordID)){
        echo 'Error not valid application token';
        die();
    } else {
      
     
     
     //$info = array(); 
     $keysArray = array();
     $valuesArray = array();
     $streetString = "";


     //months array
     $months = array( '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
                 '05' => 'May',     '06' => 'Jun',     '07' => 'Jul',  '08' => 'Aug',
                 '09' => 'Sep', '10' => 'Oct', '11' => 'Nov',
                 '12' => 'Dec');
     
       
     //connect to quickbase and pull info down
     $q = new Quickbase(USRNAME, PASSWRD, true, QUICKBASEID, APPTOKEN, URLINST);
     $result = $q->get_record_info($recordID);
    // echo '<pre>'; 
    // print_r($result);
    // echo '</pre>';

    //create a file name to save pdf to First Name - Last Name 
    $fileOut = "Enrol_Form_";

     for($i = 0, $j = count($result); $i < $j ; $i++) {
      
      if( (string)$result->field[$i]->name == 'First Name' || (string)$result->field[$i]->name == 'Last Name'){
        $fileOut .= preg_replace('/\s+/', '',(string)$result->field[$i]->value);
      }
     }
     //append record ID as well to be safe
    $fileOut .= $recordID;
    $fileOut .= '.pdf';

    //$app = simplexml_load_file($result);
    //echo '<h1>Procesing Document...</h1>';
    for($i = 0, $j = count($result); $i < $j ; $i++){
         
         //campus rule
        if( (string)$result->field[$i]->name == 'Campus' ) {
          //echo "Executing campus rule set...<br />";
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
            //echo "Executing Date rule set...<br/>";
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
        //rule for email split
        elseif ( (string)$result->field[$i]->name == 'Email'){
            $email = (string)$result->field[$i]->value; 
            if ( strlen($email) > 19 ){
              $pos = 19;
              $str1 = substr($email, 0, $pos);
              $str2 = substr($email, $pos);
              $keysArray[] = 'Email';
              $valuesArray[] = $str1;
              $keysArray[] = 'Email-2';
              $valuesArray[] = $str2;
            }
              else {
                $keysArray[] = 'Email';
                $valuesArray[] = (string)$result->field[$i]->value;
              }
        }
        //rule for intake split
        elseif ( (string)$result->field[$i]->name == 'Intake' ){
          if ( (string)$result->field[$i]->value == '(Select one)'){
            $keysArray[] = 'Intake-year';
            $valuesArray[] = '';
            $keysArray[] = 'Intake-month';
            $valuesArray[] = '';

          }

          else {
            $intakeMonth = explode(" ", (string)$result->field[$i]->value);
            $monthNumber = array_search($intakeMonth[0], $months);
            // print_r($monthNumber);
            // echo '</br>';
            // echo $intakeMonth[1];
            $keysArray[] = 'Intake-year';
            $valuesArray[] = $intakeMonth[1];
            $keysArray[] = 'Intake-month';
            $valuesArray[] = $monthNumber;
          }
            
        }
        elseif ( (string)$result->field[$i]->name == 'Street Address 1') {
            
            //allow for case where street address 1 and 2 are filled combine into one array

            $address = (string)$result->field[$i]->value;
            
            if ( strlen($address)  > 19 ){
              
              $pos = 19;
              $str1 = substr($address, 0, $pos);
              $str2 = substr($address, $pos);
              $keysArray[] = 'Street Address 1';
              $valuesArray[] = $str1;
              $keysArray[] = 'Street Address 2';
              //write this one to a string
              //$valuesArray[] = $str2;
              $streetString .= $str2;
              
            } else {
                $keysArray[] = 'Street Address 1';
                $valuesArray[] = (string)$result->field[$i]->value;
                
              }
          }
          //rule for street address 2
          elseif ( (string)$result->field[$i]->name == 'Street Address 2') {
                    
             $address_2 = (string)$result->field[$i]->value;
           
             if( in_array('Street Address 2', $keysArray)){
               //apend value in same place
               //add a space
                $streetString .= " ";
                $streetString .= $address_2;
                $valuesArray[] = $streetString;
             }  else {
              //doesnt exists in array
              $keysArray[] = 'Street Address 2';
              $valuesArray[] = (string)$result->field[$i]->value;

             }

          }
        
        //higest eduaction rules
        elseif ( (string)$result->field[$i]->name == 'Highest Education') {
          //echo "Executing highest education rule set...</br />";
          
          if( (string)$result->field[$i]->value == 'Did not complete Year 10 schooling or equivalent'){
            $keysArray[] = 'NotY10';
            $valuesArray[] = 'X';  
          }          
          if( (string)$result->field[$i]->value == 'Completed Year 10 schooling or equivalent'){
            $keysArray[] = 'Y10';
            $valuesArray[] = 'X';  
          } 
          if( (string)$result->field[$i]->value == 'Did not complete Year 12 schooling or equivalent'){
            $keysArray[] = 'NotY12';
            $valuesArray[] = 'X';  
          } 
          if( (string)$result->field[$i]->value == 'Completed Year 12 schooling or equivalent'){
            $keysArray[] = 'Y12';
            $valuesArray[] = 'X';  
          }
          if( (string)$result->field[$i]->value == 'Other post school qualification (e.g. Certificate or Diploma)'){
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
          
          if( (string)$result->field[$i]->value == 'Don\'t know'){
            $keysArray[] = 'Know';
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
    
    //echo '<pre>';
    ///print_r($masterArray);
    //echo '<pre>';
    
    

    $fdf = new FDF();
    $result = $fdf->createFDF(MASTERFORM,$masterArray);
   
   //write fdf data to fdf file
   $fp = fopen('file.fdf', 'w');
	 fwrite($fp, print_r($result, TRUE));
	 fclose($fp);


   if ( SS_ENVIRONMENT_TYPE == 'dev' ) {
      exec('/usr/local/bin/pdftk '.MASTERFORM.' fill_form file.fdf output '.$fileOut.' flatten ',$output, $return);
   } else{
      exec('/usr/bin/pdftk '.MASTERFORM.' fill_form file.fdf output '.$fileOut.' flatten ',$output, $return);
   }
	 //execute pdftk server to merge data
		
		 if ($return != 0) {
        echo 'error executing pdftk...shell command file did not convert';
        die();
     } 

     //encode file and send back to server attchment
    $data = file_get_contents($fileOut);
    $data = base64_encode($data);
    
    $fileUpdate[] = array(  'value' => $data,
                            'fid' => '58',
                            'filename' => $fileOut
                          );


    $uploadResult = $q->upload_file($recordID, NULL,$fileUpdate);
    //print_r($uploadResult);
		echo '<h2>Process Complete</h2><p>Document attached in quickbase. Refresh page to see file.</p>';

	  } //end else
	} catch (Exception $e) {
    echo $e->getMessage(), "\n";
	}

?>