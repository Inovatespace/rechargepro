<?php
$engine = new engine();

?>

<script type="text/javascript">



function remove_plugin(Id){
         $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to remove this plugin?',
    buttons: {
        confirm: function () {
            
                       var dataString = "id="+ Id;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/removeplugin.php",
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
		if(fsize>1048576*50) 
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
<div class="admin_page_title">Manage Plugin</div>
</div>

<?php include "notification.php";?>


<div style="padding: 10px;">
<div>Upload Plugin</div>
<div id="output"></div>
<div id="progressbox" style="display:none;"><div id="progressbar"></div ><div id="statustxt">0%</div></div>
<div style="overflow: hidden; margin-bottom:20px;">
<form action="plugin/admin/pages/pro/uploadplugin.php" onsubmit="return false" method="post" enctype="multipart/form-data" id="MyUploadForm">
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
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;">#</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:25%;">Name</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;">Version</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:8%;">Update</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;">Installed Date</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;">Details</div>
<div style="float:left; overflow:hidden; botdr-right:solid 1px #EEEEEE; width:8%;">Uninstall</div>
</div>

<?php
$color=1;
$sn = 0;
$row = $engine->db_query("SELECT pluginid,name,pluginkey,version,date  FROM plugin ",array()); 
for($dbc = 0; $dbc < $engine->array_count($row); $dbc++){
    
  $sn++;  
   $pluginid = $row[$dbc]['pluginid'];
   $name = $row[$dbc]['name'];
   $pluginkey = $row[$dbc]['pluginkey'];
   $version = $row[$dbc]['version'];
   $date = $row[$dbc]['date']; 
   
     
    if($color==1){
           
?>
<div class="stats" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $sn;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:25%;"><?php echo $name;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;"><?php echo $version;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:8%;"><img src="plugin/admin/images/lifebuoy.png" width="16" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><?php echo $date;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;">-</div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:8%;"><img onclick="remove_plugin('<?php echo $pluginid;?>')" style="cursor: pointer;" src="plugin/admin/images/bin.png" width="16" /></div>
</div>
<?php
	
    $color=2;
}else{ 
?>
<div class="stats stats2" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $sn;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:25%;"><?php echo $name;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:10%;"><?php echo $version;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:8%;"><img src="plugin/admin/images/lifebuoy.png" width="16" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><?php echo $date;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;">-</div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:8%;"><img onclick="remove_plugin('<?php echo $pluginid;?>')" style="cursor: pointer;" src="plugin/admin/images/bin.png" width="16" /></div>
</div>
<?php    
$color=1;    
}
	}
    
    if(!isset($name)){ echo '<div class="nWarning">No Plugin Installed</div>';}
?>

</div>