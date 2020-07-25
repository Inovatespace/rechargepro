<?php
 header("Generator: RECHARGEPRO API");
 header('HTTP/1.0 200" " ok');
 header("Content-Type: application/xml; charset=UTF-8");
 if($first_parameter[2] == "myapp"){$first_parameter[2] = "Param";}
$root = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><'.$first_parameter[2].'/>');
//$root->addAttribute("client", $_SERVER['REMOTE_ADDR']);
//$root->addAttribute("time", time());

encode(0, $encode, $root);
 

 
$input = $root->asXML();



print_r($input);



 function encode($key, $value, $root)
 {

    // Get variable type
    $type = gettype($value);

    if (is_numeric($key))
    {
       $key = $root->getName() . "\n";
    }

    switch ($type)
    {
       case "boolean";
          $value = $value ? "true":
          "false";
          $root->addChild($key, $value);
          break;
       case "integer":
          $root->addChild($key, $value);
          break;
       case "double":
          $root->addChild($key, $value);
          break;
       case "string":
          $root->addChild($key, htmlspecialchars($value));
          break;
       case "array":
          if(count($value) == 0) return;
          #array_unique($value);
          
            array_to_xml($value,$root);
     
          //$child = $root->addChild($key, "");
          //foreach ($value as $arr_key => $arr_val)
          //{
           
            //encode($arr_key, $arr_val, $child);
            
          //}
          break;
       case "object":
          $properties = get_object_vars($value);
          $child = $root->addChild($key, "");
          foreach ($properties as $arr_key => $arr_val)
          {
             encode($arr_key, $arr_val, $child);
          }
          break;
       default:

          break;
    }
    
    return $value;


 }
 

  

 function array_to_xml($array, &$xml_user_info) {
    foreach($array as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml_user_info->addChild("$key");
                array_to_xml($value, $subnode);
            }else{
                $subnode = $xml_user_info->addChild("Param");
                array_to_xml($value, $subnode);
            }
        }else {
            $xml_user_info->addChild("$key",htmlspecialchars("$value"));
        }
    }
}


 function CleanString($string)
 {
    //This function removes all characters other than numbers
    $string = preg_replace("/[^a-zA-Z]/", "", $string);
    return $string;
 }
