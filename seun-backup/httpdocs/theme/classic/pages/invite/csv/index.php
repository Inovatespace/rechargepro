<?php
include "../../../../../engine.autoloader.php";



if(!$engine->get_session("rechargeproid")){
   $response = array(
                "files" => array(array("name"=>"ss","size"=>"40","url"=>"ddd","done"=>"Please Login First"))
            );
        generate_response($response,true);
        exit;
        }
        
$finalimg = $_FILES["files"]["tmp_name"][0];
$finalname = $_FILES["files"]["name"][0];
//$group = $_REQUEST['group'];
$extention = $engine->getExtension($finalname);



$done = "Unsupported File type";
//"pdf","doc","docx","xls","xlsx",
if(in_array($extention,array("csv","txt"))){
    
    $tplocation = "../../../../../admin/tmp/".$finalname;
 if(move_uploaded_file($finalimg, $tplocation)) {
      $done = "ok";
      
switch ($extention){
//	case "pdf":
//	break;
    
//	case "xls":
   // case "xlsx":
//	break;
    
	case "csv":
    $csv = readCSV($tplocation);
    insert_db($csv,$engine);
	break;
    
	case "txt":
    $txt = readTXT($tplocation,$engine);
    insert_db($txt,$engine);
   // $done = implode("@",$txt[0]);
	break;
    
	//case "doc":
    //case "docx":
   // case "rtf":
	//break;
    }


    }else{
        $done = "An error occured verifying the file type";
    }
    }
   
          $response = array(
                "files" => array(array("name"=>"ss","size"=>"40","url"=>"ddd","done"=>$done))
            );
        generate_response($response,true);
        
    function generate_response($content, $print_response = true) {
            $json = json_encode($content);
            echo $json;
        
        return $content;
    }
    
    
    
    
    
    
    
      
function insert_db($csv,$engine){
  
   for($i = 0; $i < count($csv); $i++){
    
   $name = $csv[$i][0];
   $email = $csv[$i][0];

   
   if(isset($csv[$i][1])){$name = $csv[$i][1];}
 if(empty($name)){$name = $email;}
           
if (filter_var($email, FILTER_VALIDATE_EMAIL)){
           $row = $engine->db_query("SELECT toemail FROM myinvite WHERE toemail = ?  LIMIT 1", array($email));
           if(empty($row[0]['toemail'])){
            $engine->db_query("INSERT INTO myinvite (invite_sourse,sentemail,sentname,toemail,toname) VALUES (?,?,?,?,?)", array("csv",$engine->get_session("rechargeproemail"),$engine->get_session("name"),$email,$name));
           }
           
           }
   
   
   
   
   
   }

} 
    
    
    
   function multiexplode($data)
    {
        $delimiters = array(
            '&',
            ',',
            '|',
            '@',
            ';',
            '"',
            '\'',
            '-',
            '_',
            ':',
            ' ',
            '\n',
            PHP_EOL);
        $MakeReady = str_replace($delimiters, $delimiters[0], $data);
        $Return = explode($delimiters[0], $MakeReady);
        return $Return;
    }
    
    
    function validate_phone($mobile)
    {
        //https://en.wikipedia.org/wiki/Telephone_numbers_in_Nigeria
        $cool = true;

        if (strlen($mobile) > 11) {

            if (substr($mobile, 0, 4) == "+234") {
                $mobile = "0" . substr($mobile, 4);
            }

            if (substr($mobile, 0, 3) == "234") {
                $mobile = "0" . substr($mobile, 3);
            }

        }
        if (strlen($mobile) < 11 || strlen($mobile) > 11) {
            $cool = false;
        }
        
        

        return $cool;
    }


    function fix_phone($mobile)
    {

        if (strlen($mobile) > 11) {

            if (substr($mobile, 0, 4) == "+234") {
                $mobile = "0" . substr($mobile, 4);
            }

            if (substr($mobile, 0, 3) == "234") {
                $mobile = "0" . substr($mobile, 3);
            }

        }
        
        if (strlen($mobile) == 11) {
            $mobile = "0" . substr($mobile, 1);
            }
            
            
        return $mobile;
    }
    
    
function readCSV($csvFile){
    $file_handle = fopen($csvFile, 'r');
    while (!feof($file_handle) ) {
        $line_of_text[] = fgetcsv($file_handle, 1024);
    }
    fclose($file_handle);
    return $line_of_text;
}
 
 
function readTXT($txtFile,$engine){
  
    $fp = fopen($txtFile, "r");
    $content = fread($fp, filesize($txtFile));
    fclose($fp);
  
   
    $explode = multiexplode($content);

    $array = array();
    for($i = 0; $i < count($explode); $i++){
    $array[$i][0] = $explode[$i];
    }  
    
    return $array;
}

    
?>
      