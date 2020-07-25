<?php
$engine = new engine();

?>

<script type="text/javascript">
function move_down(pos,id){
             $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to change widget position?',
    buttons: {
        confirm: function () {
            
                       var dataString = "type=down&id="+id+"&pos="+pos;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/moveposition.php",
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

function move_up(pos,id){
                 $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to change widget position?',
    buttons: {
        confirm: function () {
            
                       var dataString = "type=up&id="+id+"&pos="+pos;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/moveposition.php",
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

function set_position(Id){
     $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to change widget position?',
    buttons: {
        confirm: function () {
            
                       var dataString = "id="+Id+"&val="+$('input:radio[name='+Id+']:checked').val();                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/updateposition.php",
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

function remove_widget(Id){
         $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to remove this widget?',
    buttons: {
        confirm: function () {
            
                       var dataString = "id="+ Id;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/removewidget.php",
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


function enable_widget(Id){
             $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to enable this widget?',
    buttons: {
        confirm: function () {
                       var dataString = "id="+ Id;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/enablewidget.php",
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

function disable_widget(Id){
        $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to Disable this widget?',
    buttons: {
        confirm: function () {
                       var dataString = "id="+ Id;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/disablewidget.php",
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
</script>

<script type="text/javascript">
$(document).ready(function() { 

	var progressbox     = $('#progressbox');
	var progressbar     = $('#progressbar');
	var statustxt       = $('#statustxt');
	var completed       = '0%';
	
	var options = { 
			target:   '#output',   // target element(s) to be updated with server response 
			beforeSubmit:  beforeSubmit,  // pre-submit callback 
			uploadProgress: OnProgress,
			success:       afterSuccess,  // post-submit callback 
			resetForm: true        // reset the form after successful submit 
		}; 
		
	 $('#MyUploadForm').submit(function() { 
			$(this).ajaxSubmit(options);  			
			// return false to prevent standard browser submit and page navigation 
			return false; 
		});
	
//when upload progresses	
function OnProgress(event, position, total, percentComplete)
{
	//Progress bar
	progressbar.width(percentComplete + '%') //update progressbar percent complete
	statustxt.html(percentComplete + '%'); //update status text
	if(percentComplete>50)
		{
			statustxt.css('color','#fff'); //change status text to white after 50%
		}
}

//after succesful upload
function afterSuccess()
{
	$('#submit-btn').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button
    location.reload();

}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
	{

		if( !$('#imageInput').val()) //check empty input filed
		{
			$("#output").html("Are you kidding me?");
			return false
		}
		
		var fsize = $('#imageInput')[0].files[0].size; //get file size
		var ftype = $('#imageInput')[0].files[0].type; // get file type
		
		
		//Allowed file size is less than 1 MB (1048576)
		if(fsize>1048576) 
		{
			$("#output").html("<b>"+bytesToSize(fsize) +"</b> Too big Image file! <br />Please reduce the size of your photo using an image editor.");
			return false
		}
		
		//Progress bar
		progressbox.show(); //show progressbar
		progressbar.width(completed); //initial value 0% of progressbar
		statustxt.html(completed); //set status text
		statustxt.css('color','#000'); //initial color of status text

				
		$('#submit-btn').hide(); //hide submit button
		$('#loading-img').show(); //hide submit button
		$("#output").html("");  
	}
	else
	{
		//Output error to older unsupported browsers that doesn't support HTML5 File API
		$("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
		return false;
	}
}

//function to format bites bit.ly/19yoIPO
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

}); 

</script>
<div  style="padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">
<div class="admin_page_title">Manage Widget</div>
</div>

<?php include "notification.php";?>

<div style="padding: 10px;">
<div>Upload Widget</div>
<div id="output"></div>
<div id="progressbox" style="display:none;"><div id="progressbar"></div ><div id="statustxt">0%</div></div>
<div style="overflow: hidden; margin-bottom:20px;">
<form action="plugin/admin/pages/pro/uploadwidget.php" onsubmit="return false" method="post" enctype="multipart/form-data" id="MyUploadForm">
<div style="width: 90%; float: left;"><input type="file" class="input"  name="ImageFile" id="imageInput"  style="margin:0px; padding:0px; width: 99%;" /></div>
<div style="width: 10%; float:left;">
<input class="greenmenu shadow" id="submit-btn" style="border: none; width: 99%;" type="submit" value="Upload" />
<img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/></div>
</form>
</div>


<style type="text/css">
.stats{position: relative; overflow:hidden; background-color:#E7E7E7; border-bottom:1px solid white; padding:1px;}
.stats2{position: relative; overflow:hidden; background-color: #DDDDDD; border-bottom:1px solid white; padding:1px;}
.stats:hover {background: #F3F3F3; color:#F9C93A;}
</style>
<div class="adminheader" style="padding:5px; border-bottom:solid 1px #EEEEEE; overflow:hidden;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:2%;">#</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:25%;">Name</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;">Version</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:8%;">Update</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;">Installed Date</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:13%;">Position</div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:7%;">Disable</div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:5%;">Uninstall</div>
</div>

<?php
$arraywidgetdb = array();
$row = $engine->db_query("SELECT name, widgetid,widgetkey,website,version,about,position,widgetstatus,date,widgetorder  FROM widget  ORDER BY widgetorder ASC",array()); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    $arraywidgetdb[] = $row[$dbc];
    }
    
    
    
$color=1;
$sn = 0;
$arraycount = count($arraywidgetdb);
foreach($arraywidgetdb AS $rowcam){
$sn++;  
$name = $rowcam['name'];
$widgetid = $rowcam['widgetid'];
$widgetkey = $rowcam['widgetkey'];
$website = $rowcam['website'];
$version = $rowcam['version'];
$about = $rowcam['about'];
$position = $rowcam['position'];
$widgetstatus = $rowcam['widgetstatus'];
$date = $rowcam['date'];
$widgetorder = $rowcam['widgetorder'];

$left = '';
$center = '';
$right = '';
if($position == 1){$left = 'checked="checked"';}
if($position == 0){$center = 'checked="checked"';}
if($position == 2){$right = 'checked="checked"';}


$status = '<img onclick="enable_widget(\''.$widgetid.'\')" src="plugin/admin/images/cross.png" width="16" />';
if($widgetstatus == 1){$status = '<img  onclick="disable_widget(\''.$widgetid.'\')" src="plugin/admin/images/tick.png" width="16" />';}

$igbo = '<input onchange="set_position(\''.$widgetkey.'\')" '.$left.' type="radio" name="'.$widgetkey.'" value="1" class="input" style="float: left; margin-right:2%;" /> 
<input onchange="set_position(\''.$widgetkey.'\')" '.$center.' type="radio" name="'.$widgetkey.'" value="0" class="input"  style="float: left; margin-right:2%;"/> 
<input onchange="set_position(\''.$widgetkey.'\')" '.$right.' type="radio" name="'.$widgetkey.'" value="2" class="input"  style="float: left; margin-right:2%;"/>';




$both = '<div style="overflow:hidden; float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3.5%; padding-top:4px;">
<img onclick="move_up(\''.$widgetorder.'\',\''.$widgetid.'\')" style="float: right; cursor:pointer;" src="plugin/admin/images/sort_asc.png"/> 
<img onclick="move_down(\''.$widgetorder.'\',\''.$widgetid.'\')" style="float: right; cursor:pointer;" src="plugin/admin/images/sort_desc.png"/></div>'; 
$down = '<div style="overflow:hidden; float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3.5%; padding-top:4px;">
<img onclick="move_down(\''.$widgetorder.'\',\''.$widgetid.'\')" style="float: right; cursor:pointer;" src="plugin/admin/images/sort_desc.png"/></div>'; 
$up = '<div style="overflow:hidden; float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3.5%; padding-top:4px;">
<img src="plugin/admin/images/sort_asc.png"  onclick="move_up(\''.$widgetorder.'\',\''.$widgetid.'\')" style="float: right; cursor:pointer;"/></div>';
   
$arrow = $both;
if($sn == 1){$arrow = $down;}  
if($sn == $arraycount){$arrow = $up;}   
   
   
if($color==1){
?>
<div class="stats" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:2%;"><?php echo $sn;?>&nbsp;</div>
<?php echo $arrow;?>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:22%;"><?php echo $name;?>&nbsp;</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;"><?php echo $version;?>&nbsp;</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:8%;"><img src="plugin/admin/images/lifebuoy.png" width="16" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><?php echo $date;?>&nbsp;</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:13%; overflow:hidden;"><?php echo $igbo;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:7%;"><?php echo $status;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:5%;"><img onclick="remove_widget('<?php echo $widgetid;?>')" src="plugin/admin/images/bin.png" width="16" /></div>
</div>
<?php
	
    $color=2;
}else{ 
?>
<div class="stats stats2" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:2%;"><?php echo $sn;?>&nbsp;</div>
<?php echo $arrow;?>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:22%;"><?php echo $name;?>&nbsp;</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;"><?php echo $version;?>&nbsp;</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:8%;"><img src="plugin/admin/images/lifebuoy.png" width="16" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><?php echo $date;?>&nbsp;</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:13%; overflow:hidden;"><?php echo $igbo;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:7%;"><?php echo $status;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:5%;"><img onclick="remove_widget('<?php echo $widgetid;?>')" src="plugin/admin/images/bin.png" width="16" /></div>
</div>
<?php    
$color=1;    
}
	}

if(!isset($name)){ echo '<div class="nWarning">No widget Installed</div>';}
?>

</div>