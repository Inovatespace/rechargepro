<div id="loga" style="padding: 10px;">

<style type="text/css">
.stats{position: relative; overflow:hidden; background-color:#F7F7F7; border-bottom:1px solid #CCCCCC; padding:1px;}
.stats2{position: relative; overflow:hidden; background-color: #EEEEEE; border-bottom:1px solid #CCCCCC; padding:1px;}
.stats:hover {background: #F3F3F3; color:#F9C93A;}
</style>
<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE; overflow:hidden;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;">#</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:7%;">Type</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:30%;">Log Date</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:30%;">Last Modified</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;">Size</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:6%;"><img src="images/small_icons/minus-circle.png" width="16" height="16" /></div>
</div>

<?php
$color=1;
$i = 0;
$dir = "log/";
$a = scandir($dir);

foreach($a AS $filename){
if(!in_array($filename,array(".",".."))){
$i++;   

$lastmodified = date("F d Y H:i:s.",filemtime($dir.$filename));
$filesize = $engine->byteconvert(filesize($dir.$filename));

$filename = explode(".",$filename);
$filename = $filename[0];
if($color==1){
?>
<div class="stats" style="overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $i;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:7%;">Type</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:30%; cursor:pointer;" onclick="calllog('<?php echo $filename;?>')"><?php echo $filename;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:30%;"><?php echo $lastmodified;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;"><?php echo $filesize;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:6%;"><img onclick="remove_log('<?php echo $filename;?>')" src="images/small_icons/minus-circle.png" width="16" height="16" style=" cursor:pointer;" /></div>
</div>
<?php
    $color=2;
}else{ 
?>
<div class="stats stats2" style="overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $i;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:7%;">Type</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:30%; cursor:pointer;" onclick="calllog('<?php echo $filename;?>')"><?php echo $filename;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:30%;"><?php echo $lastmodified;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;"><?php echo $filesize;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:6%;"><img onclick="remove_log('<?php echo $filename;?>')" src="images/small_icons/minus-circle.png" width="16" height="16" style=" cursor:pointer;" /></div>
</div>
<?php    
$color=1;    
}
}
	}
?>

</div>

<div id="logb" style=""></div>

<script type="text/javascript">
function remove_log(id){
    
    
    
        $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to delete log file?',
    buttons: {
        confirm: function () {
            
			
                       var dataString = "filename="+id;                            
$("#loga").append('<img id="loading" src="images/loading.gif"  />');
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/message/dellog.php",
                        data: dataString,// + "&id="+ Id,
                        cache: false,
                        success: function(){
location.reload();
}
                        });
     
				        },
        cancel: function () {
            
        }
    }
});   
    
}

function calllog(id){
                       var dataString = "filename="+id;                            
$("#loga").append('<img id="loading" src="images/loading.gif"  />');
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/message/calllog.php",
                        data: dataString,// + "&id="+ Id,
                        cache: false,
                        success: function(html){
                            $("#loading").remove();
$("#loga").hide(500);
$("#logb").show();
$("#logb").html(html);
}
                        });
     
}
</script>