<?php

	Class FDF {
	/*
	 * @the vars array
	 * @access private
	*/
	
	private $vars = array();

	public function __construct() {
       print "In FDF constructor\n";
     }

     public function xml2array ( $xmlObject, $out = array () )
      {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

        return $out;
      }

    public function object2array($object)
    {
        return json_decode(json_encode((array) $object), TRUE); 
    }

	public function createFDF($file,$info){

    $data="%FDF-1.2\n%????\n1 0 obj\n<< \n/FDF << /Fields [ ";

    foreach($info as $field => $val){

        if(is_array($val)){
        	echo 'true';

            $data.='<</T('.$field.')/V[';

            foreach($val as $opt)

                $data.='('.trim($opt).')';

            $data.=']>>';

        }else{

            $data.='<< /T('.$field.') /V('.trim($val).')>>';

        }

    }

    $data.="] \n/F (".$file.") /ID [ <".md5(time()).">\n] >>".

        " \n>> \nendobj\ntrailer\n".

        "<<\n/Root 1 0 R \n\n>>\n%%EOF\n";

    return $data;

	}



	}
?>