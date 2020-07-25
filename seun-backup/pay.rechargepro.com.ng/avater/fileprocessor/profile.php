<?php
include "../../engine.autoloader.php";


$userid = $engine->get_session("quickpayid");
$finalimg = $_FILES["files"]["tmp_name"][0];
$finalname = $_FILES["files"]["name"][0];



if(isset($_REQUEST['id'])){
    $id = $_REQUEST['id'];
    $extention = $engine->getExtension($finalname);
    $filename = $id."_".$userid.".".$extention;
    
    
    $tplocation = "../../uploade/".$filename;
 if(move_uploaded_file($finalimg, $tplocation)) {
     
      } 
    
    
}else{

//$Logger = new Logger(array('path' => '../log/'));
//$Logger->special_log("rrr", "seun", 1);

include "../../engine/class/ImageResize.php";
    $image = new ImageResize($finalimg);
    $image->crop(250, 250)->save("../".$userid.".jpg");
    $image->crop(70, 70)->save("../small/".$userid.".jpg");
    
   } 
    
    
    
    
      $response = array(
                "files" => array(array("name"=>"ss","size"=>"40","url"=>"ddd"))
            );
        generate_response($response,true);
        
        function generate_response($content, $print_response = true) {
            $json = json_encode($content);
            echo $json;
        
        return $content;
    }
    
             
