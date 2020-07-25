<?php
require "../../../engine.autoloader.php";


function imagecopymerge_alpha($dst_im,$src_im,$dst_x,$dst_y,$src_x,$src_y,$pct,
    $state = 0)
{
    if ($state == 0)
    {
        define('DESIRED_IMAGE_WIDTH',130);
        define('DESIRED_IMAGE_HEIGHT',138);
        $source_path = $src_im;
        list($source_width,$source_height,$source_type) = getimagesize($source_path);
        switch ($source_type)
        {
            case IMAGETYPE_GIF:
                $source_gdim = imagecreatefromgif($source_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gdim = imagecreatefromjpeg($source_path);
                break;
            case IMAGETYPE_PNG:
                $source_gdim = imagecreatefrompng($source_path);
                break;
        }
        $source_aspect_ratio = $source_width / $source_height;
        $desired_aspect_ratio = DESIRED_IMAGE_WIDTH / DESIRED_IMAGE_HEIGHT;

        if ($source_aspect_ratio > $desired_aspect_ratio)
        {
            $temp_height = DESIRED_IMAGE_HEIGHT;
            $temp_width = (int)(DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
        } else
        {
            $temp_width = DESIRED_IMAGE_WIDTH;
            $temp_height = (int)(DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
        }

        /*
        * Resize the image into a temporary GD image
        */

        $temp_gdim = imagecreatetruecolor($temp_width,$temp_height);
        imagecopyresampled($temp_gdim,$source_gdim,0,0,0,0,$temp_width,$temp_height,$source_width,
            $source_height);

        /*
        * Copy cropped region from temporary image into the desired GD image
        */

        $x0 = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
        $y0 = 10; //($temp_height - DESIRED_IMAGE_HEIGHT) / 2;
        $desired_gdim = imagecreatetruecolor(DESIRED_IMAGE_WIDTH,DESIRED_IMAGE_HEIGHT);
        imagecopy($desired_gdim,$temp_gdim,0,0,$x0,$y0,DESIRED_IMAGE_WIDTH,
            DESIRED_IMAGE_HEIGHT);


        //header('Content-type: image/jpeg');
        imagejpeg($desired_gdim,"temp.jpg");


        $src_im = "temp.jpg";

        $dst_im = imagecreatefrompng($dst_im); //idcard
        $src_im = imagecreatefromjpeg($src_im); //passport
    } else
    {
        ///////////////////srtrtttttttttttttttttttttttttttttttttttttt
        $extension = getExtension($src_im);
        $extension = strtolower($extension);
        $src = imagecreatefrompng($src_im);

        list($width,$height) = getimagesize($src_im);

        if ($width < 160)
        {
            $newwidth = $width;
        } else
        {
            $newwidth = 160;
        }


        $newheight = 70;
        $tmp = imagecreatetruecolor($newwidth,$newheight);

        imagealphablending($tmp,false);
        imagesavealpha($tmp,true);
        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);
        $filename = "temp.jpg";

        imagepng($tmp,$filename,9);


        imagedestroy($src);
        imagedestroy($tmp);


        $src_im = "temp.jpg";

        $dst_im = imagecreatefrompng($dst_im); //idcard
        $src_im = imagecreatefrompng($src_im); //passport
        ////enddddddddddddddddddddddddddddddddddddddddddddddddd
    }


    $src_w = imagesx($src_im);
    $src_h = imagesy($src_im);
    //imagecopymerge($dest, $src, 10, 10, 0, 0, 100, 47, 75);
    // The image copy
    imagecopy($dst_im,$src_im,$dst_x,$dst_y,$src_x,$src_y,$src_w,$src_h);
    imagepng($dst_im,"1.png");

}


include ('../barcode/code128.class.php');
function barcode($staffid)
{
    $barcode = new phpCode128($staffid,120,false,18);
    $barcode->setShowText(false);
    $barcode->setPixelWidth(2);
    $barcode->setBorderWidth(0);
    $barcode->setBorderSpacing(0);
    $barcode->setEanStyle(false);
    $barcode->setTextSpacing(20);
    $barcode->setAutoAdjustFontSize(true);
    $barcode->saveBarcode("barcode.png");
    //echo "<img src='parking/$staffid.png'>";
    //echo "<div style='font-size:35px;'>".$staffid."</div>";
}


function watermark($watermark,$position)
{
    $SourceFile = "1.png";
    $font_size = 14;
    list($width,$height) = getimagesize($SourceFile);
    $image_p = imagecreatetruecolor($width,$height);
    $image = imagecreatefrompng($SourceFile);
    imagecopyresampled($image_p,$image,0,0,0,0,$width,$height,$width,$height);
    $black = imagecolorallocate($image_p,0,0,0);


    if ($position == "1")
    {
        $watermark = ucwords(strtolower($watermark));
        $font = 'BRITANIC.ttf';
        $black = imagecolorallocate($image_p,255,255,255);
        
        $arrayname = array();
        $explodename = explode(" ",$watermark);
        if(count($explodename) > 2 && strlen($watermark) > 20){
        imagettftext($image_p,$font_size,0,10,140,$black,$font,$explodename[0]." ".$explodename[1]); 
        imagettftext($image_p,$font_size,0,10,160,$black,$font,$explodename[2]);
        }elseif(count($explodename) == 2 && strlen($watermark) > 20){
        imagettftext($image_p,$font_size,0,10,140,$black,$font,$explodename[0]); 
        imagettftext($image_p,$font_size,0,10,160,$black,$font,$explodename[1]);
        }else{
            
                imagettftext($image_p,$font_size,0,10,160,$black,$font,$watermark);    
        }
        
        

    }

    if ($position == "2")
    {
        if(strlen($watermark) > 15){
        $font_size = 13;    
        }
        
        if(strlen($watermark) > 18){
        $font_size = 12;    
        }
        
        if(strlen($watermark) < 9){
        $toppos = 290;    
        }else{
        $toppos = 320;
        }
        
        $font = 'CAMBRIAI.ttf';
        imagettftext($image_p,$font_size,90,25,$toppos,$black,$font,$watermark);
    }

    if ($position == "3")
    {
        $font = 'CAMBRIAI.ttf';
        $font_size = 13;
        imagettftext($image_p,$font_size,90,195,320,$black,$font,$watermark);
    }

    imagepng($image_p,"1.png");

    imagedestroy($image);
    imagedestroy($image_p);
}


function getExtension($str)
{
    $boss = explode(".",strtolower($str));
    return end($boss);
}


$staffid = $_REQUEST['id'];
$rowcam = $engine->db_query("SELECT staff_main_id,name,staf_position FROM members WHERE staffid = ? LIMIT 1",array($staffid));
$name = $rowcam[0]['name'];
$staf_position = $rowcam[0]['staf_position'];
$staff_main_id = "#OP201504".$rowcam[0]['staff_main_id'];


if(file_exists("../../../plugin/parking_core/avater/".$staffid.".jpg")){
$location = "../../../plugin/parking_core/avater/".$staffid.".jpg";
}else{
$location = "../../../../images/default.png";
}


imagecopymerge_alpha('front.png',$location,39,188,0,0,50,0);
barcode($staffid);
imagecopymerge_alpha('1.png','barcode.png',252,180,0,0,50,1);
watermark($name,1);
watermark($staf_position,2);
watermark("#$staff_main_id",3);

$name = '1.png';
$fp = fopen($name, 'rb');

// send the right headers
header("Content-Type: image/png");
header("Content-Length: " . filesize($name));

// dump the picture and stop the script
fpassthru($fp);
?>
