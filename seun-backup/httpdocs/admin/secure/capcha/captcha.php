<?php  
session_start();  
/**
 * @author seun makinde williams (seuntech)
 * @copyright 2011
 * @version 1.0
 * @tutorial for help or more script visite www.1plusalltutor.com
 * @link http://www.1plusalltutor.com
 * @license * This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * 
 */
 
#you can use an font you like but make sure it is in the same directory with your php file  
$fontsize="24";
$font = './Broken Poster.ttf';

$image_height = 30;

if (isset($_REQUEST['i'])) {
$number_code = $_REQUEST['i'];
$image_width = 120;
if ($number_code < 3) {
$number_code = 5;
$image_width = 140;
	} else {
$number_code = $_REQUEST['i'];
$image_width = 120;
}


	} else {
$number_code = 5;
$image_width = 140;
}

$random_lines = 10;#if you set to zerro it will not be generated
$random_dots = 30;#if you set to zerro it will not be generated



/* Generate random code and store it in session */
$useChars = 'AEUYBDGHJLMNPQRSTVWXZ123456789'; #allowed character
$code = $useChars{mt_rand(0,29)};
for($i=1;$i<$number_code;$i++)
{
	$code .= $useChars{mt_rand(0,29)};
}
 
$_SESSION['letters_code'] = $code; 


#image specification
$image = imagecreate($image_width, $image_height); 
#image color to change just place an color as the fist color 5 deferent colors have been provided for you.
#to ake yours use RGB color code (RED,GREEN,BLUE)
if (isset($_REQUEST['f'])) {
    
    switch ($_REQUEST['f']){ 
	case "1":$front = imagecolorallocate($image, 214,214,214); // yellow
	break;

	default : $front = imagecolorallocate($image, 214,214,214); break;
}



   }else{
$white = imagecolorallocate($image, 255, 255, 255); #white
}
//$black = imagecolorallocate($image, 0, 0, 0);#black
$red = imagecolorallocate($image, 200, 100, 90); // red  
//$green = imagecolorallocate($image, 91, 229, 22); // green
//$yellow = imagecolorallocate($image, 254, 255, 72); // yellow
//$blue = imagecolorallocate($image, 36,36, 255); // yellow




$text_color = $red;#pic any color above as text color or creat yours


#we are going to use rand() to generate ramdom intiger combine with our image height and width to match our image size. you can also use mt_rand()
/* we are going to use imagefilledellipse to generate radom  dots on the background */
for( $i=0; $i<$random_dots; $i++ ) {imagefilledellipse($image, rand(0,$image_width), rand(0,$image_height), 2, 3, $text_color);}
        
 /* we are going to draw randomly line in background of image */
for( $i=0; $i<$random_lines; $i++ ) {
imageline($image, rand(0,$image_width), rand(0,$image_height), rand(0,$image_width), rand(0,$image_height), $text_color);}



/* skip to bellow code to know why we use this */
$textbox = imagettfbbox($fontsize, 0, $font, $code); 
$x = ($image_width - $textbox[4])/2;
$y = ($image_height - $textbox[5])/2;

#$x stands for x cordinate 
#$y stand for  y cordinate 
#to save you the time of calculating the x and y cordinate, the above code does the calculation for you. 
#you can manually enter it to see it effects
#if (isset($_REQUEST['f'])) {
#imagefilledrectangle($image, 0, 0, $image_width, $image_height, $front);	
#}
 
imagettftext ($image, $fontsize, 0, $x, $y, $text_color, $font, $code);  




#we tell our browser the type of image, using header function
header('Content-Type: image/jpeg');  
imagejpeg($image,NULL,100);//showing the image and set the quality 100 is the highest
imagedestroy($image);//destroying the image instance

?>  
