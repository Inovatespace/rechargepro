<?php
$engine = new engine();

?>

<script type="text/javascript">
function set_theme(name){
         $.confirm({
    icon: 'fa fa-lock',
    title: 'Confirm!',
    content: 'Do you want to set theme '+name+' as default?',
    buttons: {
        confirm: function () {
            
                       var dataString = "name="+ name;                            
$("#contentload").html('<img src="plugin/admin/images/loading.gif"  />');   
                        $.ajax({
                        type: "POST",
                        url: "plugin/admin/pages/pro/settheme.php",
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
<div  style="padding:5px; font-weight:bold;  font-size:11px; overflow:hidden;">
<div class="admin_page_title">Manage Theme</div>
</div>

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
		if(fsize>1048576*5) 
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
<?php include "notification.php";?>


<div style="padding: 10px;">
<div>Upload Theme</div>
<div id="output"></div>
<div id="progressbox" style="display:none;"><div id="progressbar"></div ><div id="statustxt">0%</div></div>
<div style="overflow: hidden; margin-bottom:20px;">
<form action="plugin/admin/pages/pro/uploadtheme.php" onsubmit="return false" method="post" enctype="multipart/form-data" id="MyUploadForm">
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
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;">Name</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;">Thumb Nail</div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;">Status</div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:20%;">Uninstall</div>
</div>


<?php
$dir = $engine->config("theme_folder");
$a = scandir($dir,1);
$color=1;
$sn = 0;
$dirarray = array(".","..");
foreach($a AS $dirit){
 $sn++;   
    if(in_array($dirit,$dirarray)){
        break;
    }
    
 
 $assign = '<img style="cursor: pointer;" onclick="set_theme(\''.$dirit.'\')" src="plugin/admin/images/tick.png" width="16" height="16" />';
 
if($dirit == $engine->config("theme")){
$assign = "Assigned";    
}
    if($color==1){
           
?>
<div class="stats" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $sn;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><?php echo strtoupper($dirit);?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><img src="<?php echo $engine->config("theme_folder").$dirit;?>/thumb.jpg" width="20" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><?php echo $assign;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:20%;"><img  src="plugin/admin/images/cross.png" width="16" /></div>
</div>
<?php
	
 $color=2;
}else{ 
?>
<div class="stats stats2" style="border-bottom:solid 1px #EEEEEE; overflow:hidden; padding:5px;">
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:3%;"><?php echo $sn;?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><?php echo strtoupper($dirit);?></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><img src="<?php echo $engine->config("theme_folder").$dirit;?>/thumb.jpg" width="20" /></div>
<div style="float:left; margin-right: 5px; botdr-right:solid 1px #EEEEEE; width:20%;"><?php echo $assign;?></div>
<div style="float:left; botdr-right:solid 1px #EEEEEE; width:20%;"><img src="plugin/admin/images/cross.png" width="16" /></div>
</div>
<?php    
$color=1;    
}
	}
?>

</div>