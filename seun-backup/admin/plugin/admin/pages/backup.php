<?php
$engine = new engine();

?>

<div  style=" border-bottom:solid #DDDDDD 1px; padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">
<div class="admin_page_title">Backup</div>
</div>


<script type="text/javascript">
var intt;
function startzip(){
     var tmpsend = "";		   
	 var p = [];
     $(".checkbox").each(function(){
        if($(this).is(':checked')){
            p.push($(this).val());
            tmpsend = "&array="+p;
            }
        });
        

        
            $("#progressholder").html('Archiving Files, Please graba a cup of coffe, this may take a little while &nbsp; <img style="vertical-align: middle;" src="images/loading6.gif" width="105" height="16" />').show();
        $.ajax({
        type: "POST",
        url: "plugin/admin/backup/zip.php",
        data: tmpsend,
        cache: false,
        success: function(html){
     $("#progressholder").html(html);
        }
        });
    }

function removebackup(Link){
    
       $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to remove authorisation?',
    buttons: {
        confirm: function () {
            
           $.ajax({
        type: "POST",
        url: "plugin/admin/backup/delete.php",
        data: "file="+Link,
        cache: false,
        success: function(html){
     window.location.reload();
        }
        }); 
    
    },
        cancel: function () {
            
        }
    }
}); 


}
    
    function check_checknox(){
    //var checkcount = 0;
    $(".statsspecial").css("background-color","white");
   // $("#toolber_filemanager").hide();
 $(".checkbox").each(function(){ 
        if($(this).is(':checked')){
            $("#toolber_filemanager").show();
            //checkcount++;
            var checkval = $(this).val();
            $("#"+checkval).css("background-color","#FFFFD7");
            }
        });
        
        //$("#checkboxcount").html(checkcount+" Files");
};

function selectall(){
    check_checknox();
        if($('#selecctall').is(':checked')) { // check select status
                $(".checkbox").prop("checked", true);  //select all checkboxes with class "checkbox1"               
        }else{
           $(".checkbox").prop("checked", false);      
        }
        check_checknox();
}
</script>
<div style="padding: 10px;">
<div>
<div class="nInformation" style="text-align:left; ">List of Files Available for backup</div>
<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE; overflow:hidden;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;">#</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:55%;"><input id="selecctall" onclick="selectall()" type="checkbox" />Name</div>
<div style="float:right; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:35%;">Size</div>
</div>
<?php
function folderSize($dir){
$count_size = 0;
$count = 0;
$dir_array = scandir($dir);
  foreach($dir_array as $key=>$filename){
    if($filename!=".." && $filename!="."){
       if(is_dir($dir."/".$filename)){
          $new_foldersize = foldersize($dir."/".$filename);
          $count_size = $count_size+ $new_foldersize;
        }else if(is_file($dir."/".$filename)){
          $count_size = $count_size + filesize($dir."/".$filename);
          $count++;
        }
   }
 }
return $count_size;
}


$tobackuparray = $engine->backup_location(2);
$backuparray = $engine->backup_location(1);

$tofilearray = array();
foreach($tobackuparray AS $key => $value){
    $file = $value;
 if(file_exists($file)){
    $size = $size = folderSize($file);
    //$date = filemtime($file);
    if($size > 0){
    $tofilearray[$key] = array("name"=>$backuparray[$key],"size"=>$engine->byteconvert($size));
    }
 }
}

$color=1;
$id=0;
foreach($tofilearray AS $key => $value){
$backup_size = $tofilearray[$key]['size'];
$backup_type = $tofilearray[$key]['name'];
$id++;
    if($color==1){   
?>
<div class="statsspecial" id="<?php echo $key;?>" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $id;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:55%;"><input onclick="check_checknox();" class="checkbox" type="checkbox" value="<?php echo $key;?>" /><?php echo $backup_type;?></div>
<div style="float:right; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:35%;"><?php echo $backup_size;?></div>
</div>
<?php
	
    $color=2;
}else{ 
?>
<div class="statsspecial" id="<?php echo $key;?>" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $id;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:55%;"><input onclick="check_checknox();" value="<?php echo $key;?>" class="checkbox" type="checkbox" /><?php echo $backup_type;?></div>
<div style="float:right; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:35%;"><?php echo $backup_size;?></div>
</div>
<?php    
$color=1;    
}
	}  
?>
<div class="statsspecial" id="backup_database" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $id++;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:55%;"><input onclick="check_checknox();" value="backup_database" class="checkbox" type="checkbox" />Application Database</div>
<div style="float:right; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:35%;"></div>
</div>

</div>

<div style="overflow: hidden;">
<div onclick="startzip()" class="shadow greenmenu" style="float: right; padding:3px 10px; margin:3px; cursor:pointer;">Back-up Now</div>
</div>
<link href="<?php echo $engine->config('theme_folder').$engine->config('theme');?>/css/ui-progress-bar.css" rel="stylesheet" type="text/css" />


<div style="padding: 10px; margin:10px; border:solid 1px #EEEEEE; display:none; overflow:hidden;" id="progressholder">Archiving Files, Please graba a cup of coffe, this may take a little while &nbsp; <img style="vertical-align: middle;" src="images/loading6.gif" width="105" height="16" /></div>


<style type="text/css">
.stats{position: relative; overflow:hidden; background-color:#E7E7E7; border-bottom:1px solid white; padding:1px;}
.stats2{position: relative; overflow:hidden; background-color: #DDDDDD; border-bottom:1px solid white; padding:1px;}
.stats:hover {background: #F3F3F3; color:#F9C93A;}
</style>
<div class="nInformation" style="text-align:left; ">Backup files available for download</div>
<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE; overflow:hidden;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;">#</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:35%;">Name</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:25%;">Backup Date</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;">Size</div>
<div style="float:right; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:7%;"></div>
</div>

<?php
$filearray = array();
foreach($backuparray AS $key => $value){
    $file = "tmp/".$key.".zip";
 if(file_exists($file)){
    $size = filesize($file);
    $date = filemtime($file);
    $filearray[$key] = array("name"=>$value,"size"=>$size,"date"=>$date);
 }
}

$color=1;
$id = 0;
foreach($filearray AS $key => $value){    
$backup_size = $engine->byteconvert($filearray[$key]['size']);
$backup_type = $filearray[$key]['name'];
$date = $filearray[$key]['date'];
$date =  date("Y-m-d H:i:s",  strtotime("+0 day", $date));
$dateee = "-";
$id++;
    if($color==1){
           
?>
<div class="stats" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $id;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:35%;"><?php echo $backup_type;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:25%;"><?php echo $date;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;"><?php echo $backup_size;?></div>
<div style="float:right; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:7%;"><img style="cursor: pointer;" onclick="removebackup('<?php echo $key.".zip";?>')" src="images/trashcan_full-new.png" width="20" /> <a href="tmp/<?php echo $key.".zip";?>" target="_blank"><img src="images/small_icons/arrowDown.png" style="cursor: pointer;" width="14" height="14" /></a></div>
</div>
<?php
	
    $color=2;
}else{ 
?>
<div class="stats stats2" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $id;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:35%;"><?php echo $backup_type;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:25%;"><?php echo $date;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:15%;"><?php echo $backup_size;?></div>
<div style="float:right; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:7%;"><img style="cursor: pointer;" onclick="removebackup('<?php echo $key.".zip";?>')" src="images/trashcan_full-new.png" width="20" /> <a href="tmp/<?php echo $key.".zip";?>" target="_blank"><img src="images/small_icons/arrowDown.png" style="cursor: pointer;" width="14" height="14" /></a></div>
</div>
<?php    
$color=1;    
}
	}
    
    if(!isset($dateee)){echo '<div class="nWarning"> No Backup Found</div>';}
?>

</div>