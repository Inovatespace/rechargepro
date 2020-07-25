<?php
include "../../../../engine.autoloader.php";


$userid = $engine->get_session("adminid");
$finalimg = $_FILES["files"]["tmp_name"][0];
$finalname = $_FILES["files"]["name"][0];

//$Logger = new Logger(array('path' => '../log/'));
//$Logger->special_log("rrr", "seun", 1);
    $image = new ImageResize($finalimg);
   
    $image->crop(300, 300)->save("../../../../tmp/".$finalname);
    
      $response = array(
                "files" => array(array("name"=>$finalname,"size"=>"40","url"=>"ddd"))
            );
        generate_response($response,true);
        
        function generate_response($content, $print_response = true) {
            $json = json_encode($content);
            echo $json;
        
        return $content;
    }
    
             
