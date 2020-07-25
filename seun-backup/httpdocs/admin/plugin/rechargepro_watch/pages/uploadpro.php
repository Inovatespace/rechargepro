<?php
include "../../../engine.autoloader.php";

if(!isset($_SESSION['adminme'])){exit;};




$finalimg = $_FILES["files"]["tmp_name"][0];
$finalname = $_FILES["files"]["name"][0];
$id = $_REQUEST['id'];


$e = explode("@",$id);

$id = $e[0];
$type = $e[1];

$extention = $engine->getExtension($finalname);


$done = "Unsupported File type";

if(in_array($extention,array("pdf","jpg","jpeg","png"))){
    $filename = $type."_".$id.".".$extention;
    
    if($type == "passport"){
        
    if(in_array($extention,array("jpg","jpeg","png"))){
 if(move_uploaded_file($finalimg, "../../../../avater/$id.jpg")) {
      $done = "ok";
      }
      }
      
      
    }else{
    $tplocation = "../../../../uploade/".$filename;
 if(move_uploaded_file($finalimg, $tplocation)) {
      $done = "ok";
      }
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
    
?>

