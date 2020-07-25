<?php
header("Cache-Control: ");// leave blank to avoid IE errors
header("Pragma: ");// leave blank to avoid IE errors
header("Content-type: application/octet-stream");
header("content-disposition: attachment;filename=FILENAME.doc"); 
?>
<html xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta name="ProgId" content="Word.Document"/>
<meta name="Generator" content="Microsoft Word 9"/>
<meta name="Originator" content="Microsoft Word 9"/>
<!--[if !mso]>
<style>
v\:* {behavior:url(#default#VML);}
o\:* {behavior:url(#default#VML);}
w\:* {behavior:url(#default#VML);}
.shape {behavior:url(#default#VML);}
</style>
<![endif]-->
<title>title</title>
<!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:View>Print</w:View>
  <w:DoNotHyphenateCaps/>
  <w:PunctuationKerning/>
  <w:DrawingGridHorizontalSpacing>9.35 pt</w:DrawingGridHorizontalSpacing>
  <w:DrawingGridVerticalSpacing>9.35 pt</w:DrawingGridVerticalSpacing>
 </w:WordDocument>
</xml><![endif]-->
<style type="text/css">
.tbl{width:250px;}
.tbr{width:250px;}
</style>
</head>
<body>
<?php
require "../../engine.autoloader.php";
$username = $_REQUEST['staffid'];
$row = $engine->db_query("SELECT * FROM members WHERE staffid = ? LIMIT 1",array("$username"));

$primaryp	=  trim($row[0]['primaryp']);
$ssce	=  trim($row[0]['ssce']);
$first_degree	=  trim($row[0]['first_degree']);
$mastersp	=  trim($row[0]['mastersp']);
$phd	=  trim($row[0]['phd']);
$nd = trim($row[0]['nd']);
$hnd = trim($row[0]['hnd']);


$staff_main_id = $row[0]['staff_main_id'];	
$name = $row[0]['name'];	
$staf_address	= $row[0]['staf_address'];
$email	= $row[0]['email'];
$mobile	= $row[0]['mobile'];
$active	= $row[0]['active'];
$staf_position	= $row[0]['staf_position'];
$staf_department	= $row[0]['staf_department'];
$resumption_time	= $row[0]['resumption_time'];
$present_today	= $row[0]['present_today'];
$employment_date	= $row[0]['employment_date'];
$resignation_date	= $row[0]['resignation_date'];
$resigned_sentout	= $row[0]['resigned_sentout'];
$comment= $row[0]['comment'];
$sex	= $row[0]['sex'];
$referee1	= $row[0]['referee1'];
$referee2	= $row[0]['referee2'];
$referee3	= $row[0]['referee3'];
$picture	= $row[0]['picture'];
$pos	= $row[0]['pos'];
$lastposting	= $row[0]['lastposting'];
$entry_date	= $row[0]['entry_date'];
$today_sale= $row[0]['today_sale'];

$dob= $row[0]['dob'];
$state= $row[0]['soo'];
$lga= $row[0]['lga'];


echo '<div style="text-align:center; font-size:25px; font-style: italic; margin-bottom:30px;"><strong>CURRICULUM VITAE</strong></div>';

echo '
<table>
<tr>	<td width="350">Name:</td>	<td width="250">'.$name.'</td></tr>
<tr>	<td width="350">Date of Birth:</td>	<td width="250">'.$dob.'</td></tr>
<tr>	<td width="350">Sex:</td>	<td width="250">'.$sex.'</td></tr>
<tr>	<td width="350">State of Origin:</td>	<td width="250">'.$state.'</td></tr>
<tr>	<td width="350">L.G.A:</td>	<td width="250">'.$lga.'</td></tr>
<tr>	<td width="350">Nationality:</td>	<td width="250">Nigerian</td></tr>
<tr>	<td width="350">Address:</td>	<td width="250">'.$staf_address.'</td></tr>
<tr>	<td width="350">Phone:</td>	<td width="250">'.$mobile.'</td></tr>
<tr>	<td width="350">Email:</td>	<td width="250">'.$email.'</td></tr>
</table>
<br />




<div style="font-size:25px; font-style: italic; margin-bottom:10px;">Educational Qualification</div>';

if(!empty($primaryp) && $primaryp != "-" && $primaryp != " " && $primaryp != "@"){
    if (strpos($primaryp,'@') !== false) {$primaryp = explode("@",$primaryp);}else{ $primaryp = array($primaryp,"");}
echo '<div style="font-weight:bold;">First School Leaving Certificate</div>';
echo '<div style="font-style: italic; margin-bottom:10px;"><table><tr><td width="350">'.$primaryp[0].'</td><td width="250">';
$primaryp = explode("-",$primaryp[1]); echo $primaryp[0].'</td></tr></table></div>';}

if(!empty($ssce) && $ssce != "-" && $ssce != " " && $ssce != "@"){
    if (strpos($ssce,'@') !== false) {$ssce = explode("@",$ssce);}else{ $ssce = array($ssce,"");} 
echo '<div style="font-weight:bold;">Secondary School Certificate</div>';
echo '<div style="font-style: italic; margin-bottom:10px;"><table><tr><td width="350">'.$ssce[0].'</td><td width="250">';
$ssce = explode("-",$ssce[1]); echo $ssce[0].'</td></tr></table></div>';}

if(!empty($nd) && $nd != "-" && $nd != " " && $nd != "@"){
    if (strpos($nd,'@') !== false) {$nd = explode("@",$nd);}else{ $nd = array($nd,"");}
echo '<div style="font-weight:bold;">National Deploma</div>';
echo '<div style="font-style: italic; margin-bottom:10px;"><table><tr><td width="350">'.$nd[0].'</td><td width="250">';
$nd = explode("-",$nd[1]); echo $nd[0].'</td></tr></table></div>';}

if(!empty($hnd) && $hnd != "-" && $hnd != " " && $hnd != "@"){
    if (strpos($hnd,'@') !== false) {$hnd = explode("@",$hnd);}else{ $hnd = array($hnd,"");}
echo '<div style="font-weight:bold;">Higher National Deploma</div>';
echo '<div style="font-style: italic; margin-bottom:10px;"><table><tr><td width="350">'.$hnd[0].'</td><td width="250">';
$hnd = explode("-",$hnd[1]); echo $hnd[0].'</td></tr></table></div>';}

if(!empty($first_degree) && $first_degree != "-" && $first_degree != " " && $first_degree != "@"){
    if (strpos($first_degree,'@') !== false) {$first_degree = explode("@",$first_degree);}else{ $first_degree = array($first_degree,"");}
    echo '<div style="font-weight:bold;">University Degree</div>';
echo '<div style="font-style: italic; margin-bottom:10px;"><table><tr><td width="350">'.$first_degree[0].'</td><td width="250">';
$first_degree = explode("-",$first_degree[1]); echo $first_degree[0].'</td></tr></table></div>';}

if(!empty($mastersp) && $mastersp != "-" && $mastersp != " " && $mastersp != "@"){
    if (strpos($mastersp,'@') !== false) {$mastersp = explode("@",$mastersp);}else{ $mastersp = array($mastersp,"");}
    echo '<div style="font-weight:bold;">Post Graduate</div>';
echo '<div style="font-style: italic; margin-bottom:10px;"><table><tr><td width="350">'.$mastersp[0].'</td><td width="250">';
$mastersp = explode("-",$mastersp[1]); echo $mastersp[0].'</td></tr></table></div>';}

if(!empty($phd) && $phd != "-" && $phd != " " && $phd != "@"){
    if (strpos($phd,'@') !== false) {$phd = explode("@",$phd);}else{ $phd = array($phd,"");}
        echo '<div style="font-weight:bold;">Doctorate Degree</div>';
echo '<div style="font-style: italic; margin-bottom:10px;"><table><tr><td width="350">'.$phd[0].'</td><td width="250">';
$phd = explode("-",$phd[1]); echo $phd[0].'</td></tr></table></div>';}


echo '<br /><br /> ';

echo '<div style="font-size:25px; font-style: italic; margin-bottom:10px;">Job Referee</div>';

if(!empty($referee1)){
echo '<div style="font-style: italic; margin-bottom:10px;">'.nl2br($referee1).'</div>';}

if(!empty($referee2)){
echo '<div style="font-style: italic; margin-bottom:10px;">'.nl2br($referee2).'</div>';}

if(!empty($referee3)){
echo '<div style="font-style: italic; margin-bottom:15px;">'.nl2br($referee3).'</div>';}


echo '<br /><br />

<div style="font-size:25px; font-style: italic; margin-bottom:15px;">Office Details</div>
<table>
<tr>	<td width="350">Staff Position:</td>	<td width="250">'.$staf_position.'</td></tr>
<tr>	<td width="350">Resumption Time:</td>	<td width="250">'.$resumption_time.'</td></tr>
<tr>	<td width="350">Employment Date:</td>	<td width="250">'.$employment_date.'</td></tr>
<tr>	<td width="350">Resignature Date:</td>	<td width="250">'.$resignation_date.'</td></tr>
<tr>	<td width="350">Comment:</td>	<td width="250">'.$comment.'</td></tr>
</table>';

$tomorrow = mktime(date("h")-13, 0, 0, date("m"), date("d"), date("y"));
$date = date("Y-m-d H:i:s", $tomorrow); 
echo "</body>";
echo "</html>";
?>