<?php
require "../../../engine.autoloader.php";

?>


<script type="text/javascript">
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();            
            reader.onload = function (e) {
                $('#targets').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $("#file").change(function(){
        readURL(this);
    });
    
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
			resetForm: false        // reset the form after successful submit 
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
function afterSuccess(responseText, statusText, xhr, $form)
{
	$('#submit-btn').show(); //hide submit button
	$('#loading-img').hide(); //hide submit button
    
if(responseText == "ok"){location.reload();}

}

//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
	{
	   
      // if(checkStrength($("#password").val()) != "strong"){
       // $('#submit-btn').show(); //hide submit button
		//$('#loading-img').hide(); //hide submit button
	//	$("#output").html("<span style='color:red;'>Password is weak, it must contain at least 1 Uppercase, number and Symbol</span>");  return false;
      // }

		if($('#file').val()) //check empty input filed
		{
		//	$("#output").html("Are you kidding me?");
		//	return false
	//	}
		
		var fsize = $('#file')[0].files[0].size; //get file size
		var ftype = $('#file')[0].files[0].type; // get file type
        

		
		//allow only valid image file types 
		switch(ftype)
        {
            case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
                break;
            default:
                $("#output").html("<b>"+ftype+"</b> Unsupported file type!");
				return false
        }
		
		//Allowed file size is less than 1 MB (1048576)
		if(fsize>1048576) 
		{
			$("#output").html("<b>"+bytesToSize(fsize) +"</b> Too big Image file! <br />Please reduce the size of your photo using an image editor.");
			return false
		}
        
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

<script type="text/javascript">
jQuery(document).ready(function($){
var myCalendar;
myCalendar = new dhtmlXCalendarObject(["calendar1", "calendar2", "calendar3","calendar4","calendar5","calendar6","calendar7"]);
myCalendar.hideTime();
});
</script>
<div class="barmenu" style="padding:5px; margin: -15px -5px 0px -5px; text-align:left;">New Admin</div>
<div style="overflow: hidden; text-align: left; padding:8px; border:solid 1px #EEEEEE; margin: 5px;">
<div id="output"></div>

<form action="plugin/admin/pages/pro/newadminpro.php" onsubmit="return false" method="post" enctype="multipart/form-data" id="MyUploadForm">
<div style="float: left; width:310px;">

<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;" id="ust">Username</div>
<div style="float:left; width:155px;"><input name="username" class="input" type="text" style="width: 150px;"/></div>
</div>

<script type="text/javascript">
function checkpassword(){
//var strength = checkStrength($("#password").val());
//$("#password_strenght").html("<span style='color:#6DABC9;'>Password strenght is <span style='text-transform: capitalize;'>"+strength+"</span></span>");
}
</script>
<div style="overflow: hidden;">
<div style="float:left; width:150px;">Password</div>
<div style="float:left; width:155px;"><input onkeypress="checkpassword()" onkeyup="checkpassword()" id="password" name="password" class="input" type="text" style="width: 150px;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:5px; text-align:right; width: 305px;" id="password_strenght"></div>


<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">Retype Password</div>
<div style="float:left; width:155px;"><input name="password2" class="input" type="text" style="width: 150px;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:5px; border-bottom:solid 1px #EEEEEE;">
<div style="float:left; width:150px;">Role</div>
<div style="float:left; width:155px;">
<select name="role" class="input" style="width: 150px;">
	<option value="admin">Admin</option>
	<option value="user">User</option>
</select></div>
</div>

<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">Name</div>
<div style="float:left; width:155px;"><input name="name" class="input" type="text" style="width: 150px;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">Email</div>
<div style="float:left; width:155px;"><input name="email" class="input" type="text" style="width: 150px;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">Address</div>
<div style="float:left; width:155px;"><input name="address" class="input" type="text" style="width: 150px;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">Mobile</div>
<div style="float:left; width:155px;"><input name="mobile" class="input" type="text" style="width: 150px;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">Date Of Birth</div>
<div style="float:left; width:155px;"><input readonly="readonly" id="calendar1" name="dob" class="input" type="text" style="width: 150px;"/></div>
</div>

<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">Sex</div>
<div style="float:left; width:155px;"><select name="sex" class="input" style="width: 150px;">
	<option>Male</option>
	<option>Female</option>
</select></div>
</div>

<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">Country</div>
<div style="float:left; width:155px;">
<select name="country" class="input" style="width: 150px;">
<?php echo $engine->country();?>
</select></div>
</div>
<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">State</div>
<div style="float:left; width:155px;">
<select name="state" class="input" style="width: 150px;">
<?php 
foreach($engine->state() AS $state){
  echo "<option>$state</option>";  
};?>

</select></div>
</div>

<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">LGA</div>
<div style="float:left; width:155px;">
<select name="lga" class="input" style="width: 150px;">
<?php echo $engine->lga();?>
</select></div>
</div>


<?php
if(file_exists('../../../secure/plugin/register.php')){
	include ('../../../secure/plugin/register.php');
    foreach($register AS $key => $val){
        $show = "";
        $show .= '<div style="overflow: hidden; margin-bottom:5px;">
<div style="float:left; width:150px;">'.$key.'</div>
<div style="float:left; width:155px;">
<select name="'.$val[0].'" id="'.$val[0].'" class="input" style="width: 150px;">';
unset($val[0]);
foreach($val AS $value){ $show .= "<option>$value</option>";}

$show .= '</select></div>
</div>';

echo $show;
    }
    }
?>



</div>

<div style="float: right; width:150px; margin-right:5px;">
<div><img id="targets" src="<?php echo $engine->config("theme_folder").$engine->config("theme");?>/images/default.png" class="shadow" style="width:99%; margin:5px;" /></div>
<input id="file" name="file" type="file" style="margin:5px; width:94%;" class="shadow input"/>
<div style="overflow: hidden;">
<script type="text/javascript">
function setball(){
 var thid = $("#theballid").val();
  $("#themball").attr("src","plugin/admin/images/"+thid+".png");  
}
</script>
<select onchange="setball()" id="theballid" name="theballid" class="input" style="float: left;">
<option value="1">CEO</option>
<option value="2">Diamond</option>
<option value="3">Elite</option>
<option value="4">Gold</option>
<option value="5">Platnum</option>
<option value="6">Pink</option>
<option value="7">Silver</option>
<option value="8">Super</option>
</select>
<img id="themball" style="float: left; verticle-align:middle;" src="plugin/admin/images/1.png" width="25" />
</div>
<div style="clear: both; overflow:hidden;">
<input style="padding: 3px 0px; border:none; margin:5px; width:99%;"  id="submit-btn" class="shadow activemenu" type="submit" value="Save" />
<img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/>
</div>

<div id="progressbox" style="display:none;"><div id="progressbar"></div ><div id="statustxt">0%</div></div>
</div>
<div style="clear: both;"></div>

</form>

</div>