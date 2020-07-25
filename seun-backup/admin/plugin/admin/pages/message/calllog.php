<div style="background-color:white; overflow: hidden; border-bottom:solid 1px #EEEEEE;  border-top:solid 1px #EEEEEE;">
<div class="profilebg" onclick="$('#loga').show(); $('#logb').hide(500);" style="float: right; padding:5px; border:solid 1px #EEEEEE; margin:5px; cursor:pointer;">Back</div>
</div>


<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE; overflow:hidden;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:5%;">#</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:11%;">IP</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;">DATE</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;">TYPE</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:28%;">MESSAGE</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:17%;">FILE LOCATION</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:5%;">LINE</div>
</div>
<?php
$file = "../../../../log/".$_REQUEST['filename'].".php";
$fh = fopen($file, 'r');
$data = fread($fh, (filesize($file)+1000));
fflush($fh);
fclose($fh);

$data = $data."|"; 
$message = explode("|",$data);
//rsort($message);

$sn = 0;//count($message);
foreach($message AS $log){

if(strlen($log) > 20){    
    
$sn++;
$ip =  explode("-",$log);
$ip = $ip[0];

$date =  explode("@",$log);
$date =  explode("-",$date[0]);
$date = $date[1];

$type =  explode("#",$log);
$type =  explode("@",$type[0]);
$type = $type[1];

$messageb =  explode("*",$log);
$messageb =  explode("#",$messageb[0]);
$messageb = $messageb[1];

$filelocation =  explode("$",$log);
$filelocation =  explode("*",$filelocation[0]);
$filelocation = $filelocation[1];
$filelocation = str_ireplace("\\"," \\",$filelocation);;


$fileline =  explode("|",$log);
$fileline =  explode("$",$fileline[0]);
$fileline = $fileline[1];
?>
<div style="padding:5px; border-bottom:solid 1px #EEEEEE; overflow:hidden;">
<div style="float:left; overflow:hidden; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:5%;"><?php echo $sn;?></div>
<div style="float:left; overflow:hidden; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:11%;"><?php echo $ip;?></div>
<div style="float:left; overflow:hidden; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;"><?php echo $date;?></div>
<div style="float:left; overflow:hidden; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;"><?php echo $type;?></div>
<div style="float:left; overflow:hidden; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:28%;"><?php echo $messageb;?></div>
<div style="float:left; overflow:hidden; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:17%;"><?php echo $filelocation;?></div>
<div style="float:left; overflow:hidden; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:5%;"><?php echo $fileline;?></div>
</div>
<?php
}

}



?>